<?php

namespace App\Repositories;

use App\Models\User as Model;
use http\Exception\InvalidArgumentException;
use Illuminate\Support\Collection;

class User extends AbstractReadFromApi
{
    protected static function getAndInsertData(int $id=null) : Collection
    {
        $url           = self::getApiUrl('users');
        $apiData       = self::readDataFromApi($url);
        $maxCacheItems = self::getMaxCacheItems('users');

        $rc = new Collection();
        foreach ($apiData as $v) {
            $model = new Model();
            $model->id_external = $v['id'];
            $model->name        = $v['name'];
            $model->email       = $v['email'];
            if (count($rc) < $maxCacheItems) {
                $model->save();
            }
            $rc->add($model);
        }

        return $rc;
    }

    public static function get() : Collection
    {
        // Try to get from DB
        $items = Model::get();

        // If nothing in DB, fetch from API
        if (!count($items)) {
            $items = self::getAndInsertData();
        }

        return $items;
    }


    public static function find($id) : Collection
    {
        if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
            return Model::where('email', $id)->first();
        }

        if (is_numeric($id)) {
            return Model::find($id);
        }

        throw new InvalidArgumentException('Invalid filter format [ NOT(email|int) ] received');
    }
}

