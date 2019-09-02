<?php
class AdminController extends URLShortener\Controller
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem('/tools/url-shortener/admin');

        $this->setupSidebar();
    }

    public function index_action()
    {
        $this->endpoint  = Config::get()->SHORTENER_ENDPOINT;
        $this->prefix    = Config::get()->SHORTENER_URL_PREFIX;
        $this->signature = Config::get()->SHORTENER_SIGNATURE;
        $this->username  = Config::get()->SHORTENER_USERNAME;
        $this->password  = Config::get()->SHORTENER_PASSWORD;
    }

    public function store_action()
    {
        if (!Request::isPost()) {
            throw new MethodNotAllowedException();
        }

        Config::get()->store('SHORTENER_ENDPOINT', Request::get('endpoint'));
        Config::get()->store('SHORTENER_URL_PREFIX', Request::get('prefix'));
        Config::get()->store('SHORTENER_SIGNATURE', Request::get('signature'));
        Config::get()->store('SHORTENER_USERNAME', Request::get('username'));
        Config::get()->store('SHORTENER_PASSWORD', Request::get('password'));

        PageLayout::postSuccess($this->_('Die Konfiguration wurde gespeichert'));

        $this->redirect('admin');
    }

    private function setupSidebar()
    {
        $cache  = StudipCacheFactory::getCache();
        $cached = $cache->read('URLShortener/stats');

        try {
            $data = URLShortener\YourlsAPI::getInstance()->dbStats();
            $cache->write('URLShortener/stats', $data, 10 * 60);
        } catch (Exception $e) {
            $data = false;
        }

        if (!$data) {
            return;
        }

        $widget = Sidebar::get()->addWidget(new SidebarWidget());
        $widget->setTitle($this->_('Statistiken'));
        $widget->addElement(new WidgetElement(sprintf(
            implode('<br>', [
                $this->_('Anzahl Links: %s'),
                $this->_('Anzahl Klicks: %s'),
            ]),
            number_format($data['db-stats']['total_links'], 0, ',', '.'),
            number_format($data['db-stats']['total_clicks'], 0, ',', '.')
        )));
    }
}
