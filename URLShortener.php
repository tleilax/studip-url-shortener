<?php
class URLShortener extends StudIPPlugin implements SystemPlugin, RESTAPIPlugin
{
    public function __construct()
    {
        parent::__construct();

        StudipAutoloader::addAutoloadPath(__DIR__ . '/lib', 'URLShortener\\');

        if (!$this->userIsLoggedIn()) {
            return;
        }

        if (!self::isConfigured() && !$this->userIsRoot()) {
            return;
        }

        self::configureAPI();

        $navigation = new Navigation(
            _('URL Shortener'),
            PluginEngine::getURL($this, [], 'urls')
        );
        if ($this->userIsRoot()) {
            $navigation->addSubnavigation('urls', new Navigation(
                _('Übersicht'),
                PluginEngine::getURL($this, [], 'urls')
            ));
            $navigation->addSubnavigation('admin', new Navigation(
                _('Verwaltung'),
                PluginEngine::getURL($this, [], 'admin')
            ));

            if (!$this->isConfigured()) {
                $navigation->setURL(PluginEngine::getURL($this, [], 'admin'));
            }
        }
        Navigation::addItem('/tools/url-shortener', $navigation);
    }

    public static function isConfigured()
    {
        return Config::get()->SHORTENER_ENDPOINT
            && (
                Config::get()->SHORTENER_SIGNATURE
                || (
                    Config::get()->SHORTENER_USERNAME
                    && Config::get()->SHORTENER_PASSWORD
                )
            );
    }

    public static function configureAPI()
    {
        if (!self::isConfigured()) {
            return;
        }

        URLShortener\YourlsAPI::configureInstance(
            Config::get()->SHORTENER_ENDPOINT,
            Config::get()->SHORTENER_SIGNATURE
                ? ['signature' => Config::get()->SHORTENER_SIGNATURE]
                : [
                    'username' => Config::get()->SHORTENER_USERNAME,
                    'password' => Config::get()->SHORTENER_PASSWORD,
                  ]
        );
    }

    private function userIsLoggedIn()
    {
        return is_object($GLOBALS['user'])
            && $GLOBALS['user']->id !== 'nobody';
    }

    private function userIsRoot()
    {
        return $this->userIsLoggedIn()
            && $GLOBALS['user']->perms === 'root';
    }

    public function perform($unconsumed)
    {
        $version = $this->getMetadata()['version'];

        $this->addStylesheet('assets/style.less');
        PageLayout::addScript($this->getPluginURL() . '/assets/script.js?v=' . $version);

        parent::perform($unconsumed);
    }

    public function getRouteMaps()
    {
        return [
            new URLShortener\RouteMap(),
        ];
    }
}
