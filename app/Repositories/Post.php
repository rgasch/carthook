<?php

namespace App\Repositories;

use App\Models\Post as Model;
use Illuminate\Support\Collection;

class Post extends AbstractReadFromApi
{
    protected static function getAndInsertData(int $id=null) : Collection
    {
        $url           = self::getApiUrl('users', $id, 'posts');
        $apiData       = self::readDataFromApi($url);
        $maxCacheItems = self::getMaxCacheItems('posts');

        $rc = new Collection();
        foreach ($apiData as $v) {
            $model = new Model();
            $model->id_external = $v['id'];
            $model->user_id     = $id;
            $model->title       = $v['title'];
            $model->body        = $v['body'];
            if (count($rc) < $maxCacheItems) {
                $model->save();
            }
            $rc->add($model);
        }

        return $rc;
    }

    public static function get ($id, $searchText=null) : Collection
    {
        $items = [];

        // See if we have anything in the DB for this user/post combinations
        $items = Model::where('user_id', $id)->get();

        // If not, try to get it via API
        if (!count($items)) {
            $items = self::getAndInsertData($id);
        }

        // Since we already got the user's posts from the DB, it's faster to do a local search
        // rather than query the DB again. This only holds if we know that we're searching a
        // small amount of data (ie: the user's first 50 posts), for generic/unversal post
        // searching we should use an indexed DB field.
        if ($searchText && count($items)) {
            $items = $items->filter(function ($item) use ($searchText) {
                return stripos($item->title, $searchText) !== false;
            });
        }

        return $items;
    }


    // This method method does a fulltext search and obviously only works on DB contents.
    // Thus the DB should be filled by previous queries/operations before using this method.
    public static function search($searchText) : Collection
    {
        return Model::whereRaw("MATCH (title) AGAINST ('$searchText')")->get();
    }
}

