<?php
class UrlsController extends URLShortener\Controller
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        try {
            Navigation::activateItem('/tools/url-shortener/urls');
        } catch (Exception $e) {
            Navigation::activateItem('/tools/url-shortener');
        }

        $actions = Sidebar::get()->addWidget(new ActionsWidget());
        $actions->addLink(
            $this->_('Link kürzen'),
            $this->link_for('urls/add'),
            Icon::create('add')
        )->asDialog('size=auto');
    }

    public function index_action($page = 0)
    {
        $limit  = Config::get()->ENTRIES_PER_PAGE;
        $offset = $page * $limit;

        $this->count = URLShortener\URL::countBySQL('user_id = ?', [$GLOBALS['user']->id]);
        if ($offset > $this->count) {
            $page   = 0;
            $offset = 0;
        }

        $this->page  = $page;
        $this->urls  = URLShortener\URL::findBySQL(
            "user_id = ?",
            [$GLOBALS['user']->id],
            "ORDER BY mkdate DESC LIMIT {$offset}, {$limit}"
        );
    }

    public function add_action()
    {
        $this->prefix = Config::get()->SHORTENER_URL_PREFIX;
    }

    public function store_action()
    {
        $url = trim(Request::get('url'));

        $check = URLShortener\URL::findOneByUrl($url);
        if (!$check) {
            try {
                $result = URLShortener\YourlsAPI::getInstance()->shorten(
                    $url,
                    Request::get('keyword')
                );

                $model = new URLShortener\URL();
                $model->user_id  = $GLOBALS['user']->id;
                $model->url      = $result['url']['url'];
                $model->title    = $result['title'];
                $model->shorturl = $result['shorturl'];
                $model->store();

                PageLayout::postSuccess(formatLinks(sprintf(
                    $this->_('URL wurde zu %s gekürzt'),
                    $model->shorturl
                )));
            } catch (Exception $e) {
                PageLayout::postError(
                    $this->_('URL konnte nicht gekürzt werden'),
                    [$e->getMessage() ?: $this->_('Unbekannter Fehler')]
                );
            }
        } elseif ($check->user_id !== $GLOBALS['user']->id) {
            $model = new URLShortener\URL();
            $model->user_id  = $GLOBALS['user']->id;
            $model->url      = $check->url;
            $model->title    = $check->title;
            $model->shorturl = $check->shorturl;
            $model->store();

            PageLayout::postSuccess(formatLinks(sprintf(
                $this->_('URL wurde zu %s gekürzt'),
                $model->shorturl
            )));
        } else {
            PageLayout::postInfo(formatLinks(sprintf(
                $this->_('Sie haben diese URL bereits zu %s gekürzt'),
                $check->shorturl
            )));
        }

        $this->redirect('urls/index');
    }

    public function delete_action(URLShortener\URL $url)
    {
        if (!Request::isPost()) {
            throw new MethodNotAllowedException();
        }

        if ($url->user_id !== $GLOBALS['user']->id && $GLOBALS['user']->perms !== 'root') {
            throw new AccessDeniedException();
        }

        $url->delete();

        PageLayout::postSuccess($this->_('Die URL wurde gelöscht'));
        $this->redirect('urls/index');
    }
}
