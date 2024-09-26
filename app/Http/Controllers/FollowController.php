<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    //

    public function createFollow(User $user){


    if($user->id == auth()->user()->id){
        return back()->with('failure','you can not follow yourself');
    }

    $existCount = Follow::where([['user_id','=',auth()->user()->id],['followeduser','=',$user->id]])->count();

    if($existCount){

        return back()->with('failure','you already following that user');

    }

        $newFollow = new Follow;

        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();

        return back()->with('success','you successfully followed user!');

    }
    
    public function removeFollow(User $user){
        
    if($user->id == auth()->user()->id){
        return back()->with('failure','you can not unfollow yourself');
    }

     Follow::where([['user_id','=',auth()->user()->id],['followeduser','=',$user->id]])->delete();

     return back()->with('success','user successfully unfollowed!');

    }
}
