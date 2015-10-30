<?php

namespace asp\commenter;

use App\Http\Controllers\Controller;
use GuzzleHttp;
use asp\commenter\Helpers\Commenter;
class CommentController extends Controller
{
   public function index(){
   	 $thread_uri= $_SERVER['REQUEST_URI'];
   	$thread= Commenter::getThread($thread_uri);
    return $thread;
   }
   public function postComment(){
   	$input=\Input::all();
   	$thread_uri=\Input::get('_thread');
     return Commenter::createComment($input,$thread_uri);   
    }
    public function postReply(){
     $input=\Input::all();
    $thread_uri=\Input::get('_thread');
     return Commenter::createReply($input,$thread_uri);    
    }
    public function editComment(){
      $input=\Input::all();
      $thread_uri=\Input::get('_thread');
      return Commenter::editComment($input,$thread_uri);
    }
    public function editReply(){
      $input=\Input::all();
      $thread_uri=\Input::get('_thread');
      return Commenter::editReply($input,$thread_uri);
    }
    public function deleteComment(){
      $input=\Input::all();
      // $thread_uri=\Input::get('_thread');
      return Commenter::deleteComment($input);
    }
    public function deleteReply(){
      $input=\Input::all();
      // $thread_uri=\Input::get('_thread');
      return Commenter::deleteReply($input);
    }
}
