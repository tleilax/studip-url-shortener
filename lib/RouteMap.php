<?php
namespace URLShortener;

use Request;
use RESTAPI\RouteMap as GlobalRouteMap;

class RouteMap extends GlobalRouteMap
{
    /**
     * @get /url-shortener/keyword
     */
    public function getKeyword()
    {
        $keyword = Request::option('keyword');
        if (!$keyword) {
            $this->error(400, 'Missing keyword parameter');
        }

        try {
            return YourlsAPI::getInstance()->expand($keyword);
        } catch (\Exception $e) {
            $this->notFound();
        }
    }
}
