<?php
class UrlsController extends StudipController
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
            _('Link kürzen'),
            $this->link_for('urls/add'),
            Icon::create('add')
        )->asDialog('size=auto');
    }

    public function index_action($page = 0)
    {
        $limit  = Config::get()->ENTRIES_PER_PAGE;
        $offset = $page * $limit;

        $this->count = URLShortener\URL::countBySQL('user_id = ?', [$GLOBALS['user']->id]);
        $this->urls  = URLShortener\URL::findBySQL(
            "user_id = ?",
            [$GLOBALS['user']->id],
            "ORDER BY mkdate DESC LIMIT {$offset}, {$limit}"
        );
    }

    public function add_action()
    {

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
                    _('URL wurde zu %s gekürzt'),
                    $model->shorturl
                )));
            } catch (Exception $e) {
                PageLayout::postError(
                    _('URL konnte nicht gekürzt werden'),
                    [$e->getMessage() ?: _('Unbekannter Fehler')]
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
                _('URL wurde zu %s gekürzt'),
                $model->shorturl
            )));
        } else {
            PageLayout::postInfo(formatLinks(sprintf(
                _('Sie haben diese URL bereits zu %s gekürzt'),
                $check->shorturl
            )));
        }

        $this->redirect('urls/index');
    }

    public function delete_action($url_id)
    {
        if (!Request::isPost()) {
            throw new MethodNotAllowedException();
        }

        $url = URLShortener\URL::find($url_id);
        if ($url->user_id !== $GLOBALS['user']->id && $GLOBALS['user']->perms !== 'root') {
            throw new AccessDeniedException();
        }

        $url->delete();

        PageLayout::postSuccess(_('Die URL wurde gelöscht'));
        $this->redirect('urls/index');
    }
}
