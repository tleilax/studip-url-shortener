<?php
class SetupPlugin extends Migration
{
    public function up()
    {
        // Setup database
        $query = "CREATE TABLE IF NOT EXISTS `shorturls` (
                    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `user_id` CHAR(32) NOT NULL,
                    `url` VARCHAR(2048) NOT NULL,
                    `title` VARCHAR(256) NULL DEFAULT NULL,
                    `shorturl` VARCHAR(256) NOT NULL,
                    `clicks` INT(11) UNSIGNED NOT NULL DEFAULT 0,
                    `mkdate` INT(11) UNSIGNED NOT NULL,
                    `chdate` INT(11) UNSIGNED NOT NULL,
                    `updated` INT(11) UNSIGNED NOT NULL,
                    PRIMARY KEY (`id`),
                    INDEX `user_id` (`user_id`)
                  )";
        DBManager::get()->exec($query);

        // Setup configuration
        Config::get()->create('SHORTENER_ENDPOINT', [
            'value'       => '',
            'type'        => 'string',
            'range'       => 'global',
            'section'     => 'URL Shortener',
            'description' => 'API-Endpunkt für den Yourls-Shortener',
        ]);

        Config::get()->create('SHORTENER_SIGNATURE', [
            'value'       => '',
            'type'        => 'string',
            'range'       => 'global',
            'section'     => 'URL Shortener',
            'description' => 'API-Signatur für den Yourls-Shortener',
        ]);

        Config::get()->create('SHORTENER_USERNAME', [
            'value'       => '',
            'type'        => 'string',
            'range'       => 'global',
            'section'     => 'URL Shortener',
            'description' => 'API-Nutzername für den Yourls-Shortener',
        ]);

        Config::get()->create('SHORTENER_PASSWORD', [
            'value'       => '',
            'type'        => 'string',
            'range'       => 'global',
            'section'     => 'URL Shortener',
            'description' => 'API-Passwort für den Yourls-Shortener',
        ]);
    }

    public function down()
    {
        // Remove tables from database
        $query = "DROP TABLE IF EXISTS `shorturls`";
        DBManager::get()->exec($query);

        // Remove confiuration
        Config::get()->delete('SHORTENER_ENDPOINT');
        Config::get()->delete('SHORTENER_SIGNATURE');
        Config::get()->delete('SHORTENER_USERNAME');
        Config::get()->delete('SHORTENER_PASSWORD');
    }
}
