<?php

namespace App\Http\Controllers;

use App\Repositories\Comment;
use App\Repositories\Post;
use App\Repositories\User;

// We generally want to keep our Controller code small but encapsulating the
// processing logic in other classes. This has the additional advantage that
// any code we abstract like this, is easily reusable in other contexts,
// for example the classes called here could again be called/used in web
// controller code. This separation also allows for easy testing since
// all functionality is properly scoped/encapsulated.
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
