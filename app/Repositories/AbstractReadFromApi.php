<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Config;


abstract class AbstractReadFromApi
{
    // Child classes need to implement this
    abstract protected static function getAndInsertData(int $id=null) : Collection;


    // Retrieve base API from Config
    private static function getBaseApiUrl() : string
    {
        $url = Config::get('carthook.apiUrl');
        if (!$url) {
            throw new Exception ('Invalid [apiUrl] retrieved from config.carthook');
        }

        return $url;
    }


    // Build target remote API
    public static function getApiUrl(string $objectType, int $objectId=null, string $subObjectType=null) : string
    {
        if (!$objectType) {
            throw new \InvalidArgumentException('Invalid [objectType] received');
        }

        $baseApiUrl = self::getBaseApiUrl();
        $apiUrl     = "$baseApiUrl/$objectType";

        if ($objectId && $subObjectType) {
            $apiUrl .= "/$objectId/$subObjectType";
        }

        return $apiUrl;
    }


    // Returns the maxCache setting from Config
    public static function getMaxCacheItems (string $objectType) : int
    {
        if (!$objectType) {
            throw new \InvalidArgumentException('Invalid [objectType] received');
        }

        $maxCacheItems = (int)Config::get("carthook.maxCacheItems.$objectType");
        if (!$maxCacheItems) {
            throw new Exception ('Invalid [maxCacheItems] retrieved from config.carthook');
        }

        return $maxCacheItems;
    }

    // Read data from remote API
    public static function readDataFromApi (string $url) : array
    {
        if (!$url) {
            throw new \InvalidArgumentException('Invalid [url] received');
        }

        // We could use something more flexible like Guzzle or Unirest here, but for
        // the purposes of this exercise we'll keep it simple and just use file_get_contents.
        // The primary reason for this is that we only need to process GET requests, so
        // there's no real need for a complicated solution at this point.
        $data = file_get_contents($url);
        if (!$data) {
            return [];
        }

        return json_decode($data, true);
    }
}

