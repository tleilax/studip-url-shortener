<?php
namespace URLShortener;

use CronJob as GlobalCronjob;

class Cronjob extends GlobalCronjob
{
    public static function getName()
    {
        return 'URL Shortener cronjob';
    }

    /**
     * Return the description of the cronjob.
     */
    public static function getDescription()
    {
        return 'Fetches statistic data for shortened urls';
    }

    public static function getParameters()
    {
        return [
            'limit' => [
                'type'        => 'integer',
                'default'     => 50,
                'status'      => 'mandatory',
                'description' => _('Wieviele Links sollen Aufruf überprüft werden?'),
            ],
            'refresh_rate' => [
                'type'        => 'integer',
                'default'     => 2,
                'status'      => 'mandatory',
                'description' => _('Anzahl der Stunden, nach der ein Link überprüft werden soll'),
            ],
        ];
    }

    public function setUp()
    {
        require_once __DIR__ . '/../URLShortener.php';
        require_once __DIR__ . '/../lib/YourlsAPI.php';
        require_once __DIR__ . '/../lib/URL.php';

        \URLShortener::configureAPI();
    }

    public function execute($last_result, $parameters = [])
    {
        $parameters = array_merge(
            array_map(function ($param) { return $param['default']; }, self::getParameters()),
            $parameters
        );

        if (!\URLShortener::isConfigured()) {
            return;
        }

        $api = YourlsAPI::getInstance();

        URL::findEachBySQL(
            function ($url) use ($api) {
                try {
                    $info = $api->urlStats($url->shorturl);

                    $url->clicks  = $info['link']['clicks'];
                    $url->updated = time();
                    $url->store();
                } catch (\Exception $e) {
                }
            },
            "FROM_UNIXTIME(updated) < NOW() - INTERVAL {$parameters['refresh_rate']} HOUR ORDER BY updated ASC LIMIT {$parameters['limit']}"
        );
    }
}
