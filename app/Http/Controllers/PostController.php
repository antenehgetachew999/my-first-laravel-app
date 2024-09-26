<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{


    public function search($term){
        $posts = Post::search($term)->get();

        $posts->load('user:id,username,avatar');

        return $posts;

    }
    public function showCreateForm(){
        //this is possible to check if logged in user is accessing it but it is repetitive. so we use middle ware at one point ///for alll
        // if(!auth()->check()){
        //     return redirect('/');
        
        // }

        return view('create-post');

    }

    public function savePost(Request $request){


        $incommingFileds=$request->validate([
            'title'=>'required',
            'body'=>'required'
        ]);

        $incommingFileds['title'] =strip_tags($incommingFileds['title']);
        $incommingFileds['body'] =strip_tags($incommingFileds['body']);
        $incommingFileds['user_id'] = auth()->id();

        $post = Post::create($incommingFileds);

        return redirect('/post/'.$post['id'])->with('success','New post successfuly created!');
    }
    public function createPostApi(Request $request){


        $incommingFileds=$request->validate([
            'title'=>'required',
            'body'=>'required'
        ]);

        $incommingFileds['title'] =strip_tags($incommingFileds['title']);
        $incommingFileds['body'] =strip_tags($incommingFileds['body']);
        $incommingFileds['user_id'] = auth()->id();

        $post = Post::create($incommingFileds);

        return $post->id;
    }

    public function getPost(Post $post){
        //allowing some tags and handling markdowns as well
        $post['body']=strip_tags(Str::markdown($post->body),'<p><ul><li><strong><em><h3><h1><h4><h2><h5><ol>');

        return view('single-post',['post'=>$post]);

    }
    public function delete(Post $post){
        //CONTROLLER LEVEL POLICY
        // if(auth()->user()->cannot('delete',$post)){

        //     return 'can not delete!';
        // }
    
        $post->delete();

        return redirect('/profile/'.auth()->user()->username)->with('success','Post successfully deleted');

    }

    public function deleteApi(Post $post){
        //CONTROLLER LEVEL POLICY
        // if(auth()->user()->cannot('delete',$post)){

        //     return 'can not delete!';
        // }
    
        $post->delete();

        return 'deleted';

    }

    public function updatePost(Post $post,Request $request){

        $incommingFileds= $request->validate([
            'title'=>'required',
            'body'=>'required'
        ]);
       
        $incommingFileds['title'] =strip_tags($incommingFileds['title']);
        $incommingFileds['body'] =strip_tags($incommingFileds['body']);

        $post->update($incommingFileds);

        // return redirect("/post/{$post->id}/edit")->with('success','Post successfully updated.');
        //equivalent but bette to go back to previous url
        return back()->with('success','Post successfully updated.');
    }

    public function showEditForm(Post $post){

        return view('edit-post',['post'=>$post]);

    }
}
