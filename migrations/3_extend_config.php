<?php
class ExtendConfig extends Migration
{
    public function up()
    {
        Config::get()->create('SHORTENER_URL_PREFIX', [
            'value'       => '',
            'type'        => 'string',
            'range'       => 'global',
            'section'     => 'URL Shortener',
            'description' => 'Basis-URL für den URL Shortener. Wird hier ein Wert eingetragen, so wird in der Maske zum Erzeugen dieses Präfix dem Kurzlink vorangestellt.',
        ]);
    }

    public function down()
    {
        Config::get()->delete('SHORTENER_URL_PREFIX');
    }
}
