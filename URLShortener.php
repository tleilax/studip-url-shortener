<?php
require_once __DIR__ . '/bootstrap.php';

class URLShortener extends URLShortener\Plugin implements SystemPlugin, RESTAPIPlugin, PrivacyPlugin
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->userIsLoggedIn()) {
            return;
        }

        if (!self::isConfigured() && !$this->userIsRoot()) {
            return;
        }

        self::configureAPI();

        $navigation = new Navigation(
            $this->_('URL Shortener'),
            PluginEngine::getURL($this, [], 'urls')
        );
        if ($this->userIsRoot()) {
            $navigation->addSubnavigation('urls', new Navigation(
                $this->_('Übersicht'),
                PluginEngine::getURL($this, [], 'urls')
            ));
            $navigation->addSubnavigation('admin', new Navigation(
                $this->_('Verwaltung'),
                PluginEngine::getURL($this, [], 'admin')
            ));

            if (!$this->isConfigured()) {
                $navigation->setURL(PluginEngine::getURL($this, [], 'admin'));
            }
        }
        Navigation::addItem('/tools/url-shortener', $navigation);

        Notificationcenter::addObserver($this, 'removeUserData', 'UserDataDidRemove');
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

    ## RESTAPIPlugin

    public function getRouteMaps()
    {
        return [
            new URLShortener\RouteMap(),
        ];
    }

    ## PrivacyPlugin

    public function removeUserData($event, $user_id, $type)
    {
        URLShortener\URL::deleteByUser_id($user_id);
    }

    public function exportUserData(StoredUserData $storage)
    {
        $storage->addTabularData('URL Shortener: Gekürzte URLs', 'shorturls', URLShortener\URL::findAndMapBySQL(
            function ($url) {
                return $url->toRawArray();
            },
            'user_id = ?',
            [$storage->user_id]
        ));
    }
}
