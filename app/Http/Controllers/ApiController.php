<?php

namespace App\Http\Controllers;

use App\Repositories\Comment;
use App\Repositories\Post;
use App\Repositories\User;

class ApiController extends Controller
{
    public function users()
    {
        return User::get();
    }

    public function user ($id)
    {
        return User::find($id);
    }

    public function userPosts ($uid, $searchText=null)
    {
        return Post::get($uid, $searchText);
    }

    public function postSearch ($searchText)
    {
        return Post::search($searchText);
    }

    public function postComments ($pid)
    {
        return Comment::get($pid);
    }
}
