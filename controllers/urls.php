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

        $this->count = (int) URLShortener\URL::countByUser_id($GLOBALS['user']->id);
        $this->urls  = URLShortener\URL::findBySQL(
            "user_id = ?",
            [$GLOBALS['user']->id],
            "ORDER BY mkdate DESC LIMIT {$offset}, {$limit}"
        );
    }

    public function add_action()
    {

    }

    public function used_action($type = 'url')
    {
        if (!Request::isXhr()) {
            throw new MethodNotAllowedException();
        }

        $result = false;
        if ($type === 'url') {
            $url = trim(Request::get('url'));
            if (URLShortener\URL::findOneByUrl($url)) {
                $result = true;
            } else {
                // Check api
            }
        } elseif ($type === 'keyword') {
            $keyword = trim(Request::get('keyword'));
            if (URLShortener\URL::findOneByShorturl($keyword)) {
                $result = true;
            } else {
                // Check api
            }
        } else throw new Exception('Unknown type');

        $this->render_json($result);
    }

    public function store_action()
    {
        $url = trim(Request::get('url'));

        $check = URLShortener\URL::findOneByUrl($url);
        if (!$check) {
            $result = URLShortener\YourlsAPI::getInstance()->shorten(
                $url,
                Request::get('keyword')
            );

            if ($result['statusCode'] === 200 && $result['status'] === 'success') {
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
            } else {
                PageLayout::postError(
                    _('URL konnte nicht gekürzt werden'),
                    [$result['message'] ?: _('Unbekannter Fehler')]
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
