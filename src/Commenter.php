<?php 
namespace asp\commenter;
use GuzzleHttp;
use asp\commenter\Helpers\Contracts\CommenterContract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class Commenter implements CommenterContract{
	// --------------------Commenter app access variables-------------------------
	private static $commentAppURL="http://localhost/Comment/public/";

// -------------------------------------------------------------------------------
	public function getThread($thread_uri){
		$client = new GuzzleHttp\Client();
		$hostname=gethostname();
		$thread_uri=$thread_uri;
	   	$res=$client->request('POST',Commenter::$commentAppURL.'getThread',['form_params'=>[
																						'token'=>config('CommenterConfig.api_key'),
																						'hostname'=>$hostname,
																						'thread'=>$thread_uri
																							]]);
	   	if($res->getBody()=="Authentication failure"){
	   		return $res->getBody();
	   	}
	   	$counter= count(json_decode($res->getBody()));
	   	if($counter==0){
	   		return ($this->createThread($thread_uri));
	   	}
	   	// return "done";
	   	return json_decode($res->getBody());
	}	
	public function createThread($thread_uri){
		$client = new GuzzleHttp\Client();
		$hostname=gethostname();
		$thread_uri=$thread_uri;
		$res=$client->request('POST',Commenter::$commentAppURL.'thread',['form_params'=>[
																				'token'=>config('CommenterConfig.api_key'),
																				'hostname'=>$hostname,
																				'slug'=>$thread_uri
																				]]);
		if($res->getBody()=="Authentication failure"){
	   		return $res->getBody();
	   	}
		return json_decode($res->getBody());
	}
	public function deleteThread(){}
	public function createComment($input,$thread_uri){
		if(!(\Session::has('user'))){return \Redirect::back();}
		$validator=\Validator::make($input,[
            'comment'=>'required'
            ]);
		if ($validator->fails()) {
            return \Redirect::back();
        }
		$client = new GuzzleHttp\Client();
		$comment=$input['comment'];
		$user=\Session::get('user');
		$hostname=gethostname();
		$res=$client->request('POST',Commenter::$commentAppURL.'comment',['form_params'=>[
																				'token'=>config('CommenterConfig.api_key'),
																				'hostname'=>$hostname,
																				'slug'=>$thread_uri,
																				'comment'=>$comment,
																				'user'=>$user
																				]]);
		if($res->getBody()=="Authentication failure"){
	   		return $res->getBody();
	   	}
	   	return $this->notifyClients(1,$thread_uri);
		return \Redirect::back();
	}
	public function loadComment(){
		$thread_uri= $_SERVER['REQUEST_URI'];
        $thread_object= \Commenter::getThread($thread_uri);
        if($thread_object=="Authentication failure"){
        	return "Authentication failure";
        }
        // $thread_object=json_decode($thread_object);
        // return $thread_object;
        $comments;
        if(!isset($thread_object[0]->comments)){$comments=[];}
        else{$comments=$thread_object[0]->comments;}
        $replyView=[];
                $user=\Session::get('user');
        foreach ($comments as $comment) {
	        $replyView[]=\View::make('commenter::replies.createReply')->with('comment',$comment)->with('user',$user);
        }
        // $commenterView =\View::make('commenter::comments.createComment')->with('createReply',$replyView);
        // return \View::make('commenter::comments.createComment')->nest('loader',$commenterView,['comments'=>$comments]);
        return \View::make('commenter::comments.createComment')->with('user',$user)->nest('loader','commenter::comments.viewComment',['comments'=>$comments,
        																										 'replyView'=>$replyView,
        																										 'user'=>$user]);
	}
	public function editComment($input,$thread_uri){
		if(!(\Session::has('user'))){return \Redirect::back();}
		$validator=\Validator::make($input,[
            'comment'=>'required'
            ]);
		if ($validator->fails()) {
            return \Redirect::back();
        }
		$commentId=$input['_comment']/21;
		$editUrl="comment/".$commentId;
		$client = new GuzzleHttp\Client();
		$comment=$input['comment'];
		$user=\Session::get('user');
		$hostname=gethostname();
		$res=$client->request('PUT',Commenter::$commentAppURL.$editUrl,['form_params'=>[
																				'token'=>config('CommenterConfig.api_key'),
																				'hostname'=>$hostname,
																				// "_method"=>'PUT',
																				'slug'=>$thread_uri,
																				'comment'=>$comment,
																				'user'=>$user
																				]]);
		if($res->getBody()=="Authentication failure"){
	   		return $res->getBody();
	   	}
	   	$this->notifyClients(2,$thread_uri);
		return \Redirect::back();
	}
	public function deleteComment($input){
		if(!(\Session::has('user'))){return \Redirect::back();}
		$validator=\Validator::make($input,[
            '_comment'=>'required'
            ]);
		if ($validator->fails()) {
            return \Redirect::back();
        }
		$commentId=$input['_comment']/21;
		$editUrl="comment/".$commentId;
		$client = new GuzzleHttp\Client();	
		$hostname=gethostname();
		$res=$client->request('DELETE',Commenter::$commentAppURL.$editUrl,['form_params'=>[
																				'token'=>config('CommenterConfig.api_key'),
																				'hostname'=>$hostname
																				]]);
		if($res->getBody()=="Authentication failure"){
	   		return $res->getBody();
	   	}
	   	// $this->notifyClients(3,$thread_uri);
		return \Redirect::back();

	}
	public function createReply($input,$thread_uri){
		if(!(\Session::has('user'))){return \Redirect::back();}
		$validator=\Validator::make($input,[
            'reply'=>'required'
            ]);
		if ($validator->fails()) {
            return \Redirect::back();
        }
		$client = new GuzzleHttp\Client();
		$reply=$input['reply'];
		$comment=$input['commentId'];
		$user=\Session::get('user');
		$hostname=gethostname();
		$res=$client->request('POST',Commenter::$commentAppURL.'reply',['form_params'=>[
																				'token'=>config('CommenterConfig.api_key'),
																				'hostname'=>$hostname,
																				'reply'=>$reply,
																				'comment'=>$comment,
																				'user'=>$user
																				]]);
		if($res->getBody()=="Authentication failure"){
	   		return $res->getBody();
	   	}
	   	// $this->notifyClients();
		return \Redirect::back();
	}
	public function editReply($input,$thread_uri){
		if(!(\Session::has('user'))){return \Redirect::back();}
		$validator=\Validator::make($input,[
            'reply'=>'required'
            ]);
		if ($validator->fails()) {
            return \Redirect::back();
        }
		$replyId=$input['_reply']/21;
		$editUrl="reply/".$replyId;
		$client = new GuzzleHttp\Client();
		$reply=$input['reply'];
		$user=\Session::get('user');
		$hostname=gethostname();
		$res=$client->request('PUT',Commenter::$commentAppURL.$editUrl,['form_params'=>[
																				'token'=>config('CommenterConfig.api_key'),
																				'hostname'=>$hostname,
																				// "_method"=>'PUT',
																				'slug'=>$thread_uri,
																				// 'comment'=>$comment,
																				'user'=>$user,
																				'reply'=>$reply
																				]]);
		if($res->getBody()=="Authentication failure"){
	   		return $res->getBody();
	   	}
		return \Redirect::back();
	   	
	}
	public function deleteReply($input){
		if(!(\Session::has('user'))){return \Redirect::back();}
		$validator=\Validator::make($input,[
            '_reply'=>'required'
            ]);
		if ($validator->fails()) {
            return \Redirect::back();
        }
		$commentId=$input['_reply']/21;
		$editUrl="reply/".$commentId;
		$client = new GuzzleHttp\Client();	
		$hostname=gethostname();
		$res=$client->request('DELETE',Commenter::$commentAppURL.$editUrl,['form_params'=>[
																				'token'=>config('CommenterConfig.api_key'),
																				'hostname'=>$hostname
																				]]);
		if($res->getBody()=="Authentication failure"){
	   		return $res->getBody();
	   	}
		return \Redirect::back();
	}
	public function notifyClients($a,$thread_uri){
		if(config('CommenterConfig.email_notification')){
		$client = new GuzzleHttp\Client();
		$hostname=gethostname();
		$thread_uri=$thread_uri;
	   	$res=$client->request('POST',Commenter::$commentAppURL.'threadUsers',['form_params'=>[
																						'token'=>config('CommenterConfig.api_key'),
																						'hostname'=>$hostname,
																						'thread'=>$thread_uri
																							]]);
	   	if($res->getBody()=="Authentication failure"){
	   		//return $res->getBody();
	   	}
	   	$counter= count(json_decode($res->getBody()));
	   	if($counter==0){
	   		return 0;
	   	}
	   	$origin=\Session::get('user');
	   	$users=json_decode($res->getBody());
	   	foreach ($users as $user) {
	   		$receipt=new Foo();
	   		$temp=User::where('fsu_id',$user)->first();
	        $receipt->firstname=$temp->fname;
	        $receipt->lastname=$temp->lname;
	        $receipt->email=$temp->email;
	        $receipt->subject="Regarding the thread ".$hostname."/".$thread_uri;
	        $mailBodyView="";
	        switch ($a) {
	        	case 1:
	        		$receipt->subject=$origin." commented on the thread ".$hostname."/".$thread_uri;
	        		$mailBodyView="commenter::user.createComment";
	        		break;
	        	case 2:
	        		$receipt->subject=$origin." commented on the thread ".$hostname."/".$thread_uri;
	        		$mailBodyView="commenter::user.editComment";
	        		break;
	        	default:
	        		# code...
	        		break;
	        }
	        \Mail::send($mailBodyView), ['origin'=>$origin,'fname'=>$receipt->firstname,'thread'=>$hostname."/".$thread_uri], function($message) use($receipt){
	        $message->to($receipt->email, $receipt->firstname.' '.$receipt->lastname)->subject($receipt->subject);
	        });	
	   	}
	   	// return "done";
		}		
		return 0;
	}
}
class Foo{}