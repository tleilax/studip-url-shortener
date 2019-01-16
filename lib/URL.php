<?php
namespace URLShortener;

use SimpleORMap;

class URL extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'shorturls';

        $config['belongs_to']['user'] = [
            'class_name'  => 'User',
            'foreign_key' => 'user_id',
        ];

        $config['additional_fields']['unique']['get'] = function ($url) {
            return self::countByShorturl($url->shorturl) === 1;
        };

        $config['registered_callbacks']['before_create'][] = function ($url) {
            $url->updated = time();
        };

        // Remove url from yourls if this user was the only one using it.
        $config['registered_callbacks']['after_delete'][] = function ($url) {
            if (self::countByShorturl($url->shorturl) < 1) {
                YourlsAPI::getInstance()->delete($url->shorturl);
            }
        };

        parent::configure($config);
    }
}
