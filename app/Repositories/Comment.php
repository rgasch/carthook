<?php

namespace App\Repositories;

use App\Models\Comment as Model;
use Illuminate\Support\Collection;

class Comment extends AbstractReadFromApi
{
    // Acquire data from API and save it to DB
    public static function getAndInsertData(int $id=null) : Collection
    {
        $url           = self::getApiUrl('posts', $id, 'comments');
        $apiData       = self::readDataFromApi($url);
        $maxCacheItems = self::getMaxCacheItems('posts');

        $rc = new Collection();
        foreach ($apiData as $v) {
            $model = new Model();
            $model->id_external = $v['id'];
            $model->post_id     = $v['postId'];
            $model->name        = $v['name'];
            $model->email       = $v['email'];
            $model->body        = $v['body'];
            $model->save();
            if (count($rc) < $maxCacheItems) {
                $model->save();
            }
            $rc->add($model);
        }

        return $rc;
    }

    // Get comment data, either from local DB/cache or from API
    public static function get ($pid) : Collection
    {
        // Try to get from DB
        $items = Model::where('post_id', $pid)->get();

        // If nothing in DB, fetch from API
        if (!count($items)) {
            $items= self::getAndInsertData($pid);
        }

        return $items;
    }
}
