<?php
namespace URLShortener;

use Cronjob as GlobalCronjob;

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


    public function execute($last_result, $parameters = array())
    {
        
    }
}
