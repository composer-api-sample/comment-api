<?php 
namespace asp\commenter;
use GuzzleHttp;
use GuzzleHttp\Exception\RequestException;
use asp\commenter\Helpers\Contracts\CommenterContract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
class Commenter implements CommenterContract{
	// --------------------Commenter app access variables-------------------------
	private $commentAppURL;
	private $isDebug;
	public function __construct(){
		$this->commentAppURL = config('CommenterConfig.appUrl');
		$this->isDebug = config('CommenterConfig.isDebug');
	}

// -------------------------------------------------------------------------------
	public function getThread($thread_uri){
		$client = new GuzzleHttp\Client();
		$hostname=gethostname();
		$thread_uri=$thread_uri;
		try{
		   	$res=$client->request('POST',$this->commentAppURL.'getThread',['form_params'=>[
																							'token'=>config('CommenterConfig.api_key'),
																							'hostname'=>$hostname,
																							'thread'=>$thread_uri
																								],
																			'verify'=>false]);
	   }catch(RequestException $e){
		   	return "";
	   }
	   	if($res->getBody()=="Authentication failure"){
	   		if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return "";
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
		try{
			$res=$client->request('POST',$this->commentAppURL.'thread',['form_params'=>[
																					'token'=>config('CommenterConfig.api_key'),
																					'hostname'=>$hostname,
																					'slug'=>$thread_uri
																					],
																			'verify'=>false]);
		}catch(RequestException $e){
			return "";
		}
		if($res->getBody()=="Authentication failure"){
	   		if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return "";
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
		try{
			$res=$client->request('POST',$this->commentAppURL.'comment',['form_params'=>[
																					'token'=>config('CommenterConfig.api_key'),
																					'hostname'=>$hostname,
																					'slug'=>$thread_uri,
																					'comment'=>$comment,
																					'user'=>$user
																					],
																			'verify'=>false]);
		}catch(RequestException $e){
			return \Redirect::back();
		}
		if($res->getBody()=="Authentication failure"){
	   		if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return \Redirect::back();;
	   	}
	   	$this->notifyClients(1,$thread_uri);
		return \Redirect::back();
	}
	public function loadComment(){
		$thread_uri= $_SERVER['REQUEST_URI'];
        $thread_object= \Commenter::getThread($thread_uri);
        if($thread_object=="Authentication failure"){
        	if($this->isDebug){
		   		return $thread_object;	   			
	   		}
	   		return \View::make('commenter::appNotFound');
        }
        if($thread_object == ""){
        	return \View::make('commenter::appNotFound');
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
		try{
			$res=$client->request('PUT',$this->commentAppURL.$editUrl,['form_params'=>[
																					'token'=>config('CommenterConfig.api_key'),
																					'hostname'=>$hostname,
																					// "_method"=>'PUT',
																					'slug'=>$thread_uri,
																					'comment'=>$comment,
																					'user'=>$user
																					],
																			'verify'=>false]);
		}catch(RequestException $e){
			if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return \Redirect::back();
		}
		if($res->getBody()=="Authentication failure"){
	   		if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return \Redirect::back();
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
		try{
			$res=$client->request('DELETE',$this->commentAppURL.$editUrl,['form_params'=>[
																					'token'=>config('CommenterConfig.api_key'),
																					'hostname'=>$hostname
																					],
																			'verify'=>false]);
		}catch(RequestException $e){
			if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return \Redirect::back();
		}
		if($res->getBody()=="Authentication failure"){
	   		if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return \Redirect::back();
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
		try{
			$res=$client->request('POST',$this->commentAppURL.'reply',['form_params'=>[
																					'token'=>config('CommenterConfig.api_key'),
																					'hostname'=>$hostname,
																					'reply'=>$reply,
																					'comment'=>$comment,
																					'user'=>$user
																					],
																			'verify'=>false]);
		}catch(RequestException $e){
			if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return \Redirect::back();;
		}
		if($res->getBody()=="Authentication failure"){
	   		if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return \Redirect::back();;
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
		try{
			$res=$client->request('PUT',$this->commentAppURL.$editUrl,['form_params'=>[
																					'token'=>config('CommenterConfig.api_key'),
																					'hostname'=>$hostname,
																					// "_method"=>'PUT',
																					'slug'=>$thread_uri,
																					// 'comment'=>$comment,
																					'user'=>$user,
																					'reply'=>$reply
																					],
																			'verify'=>false]);
		}catch(RequestException $e){
			if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return \Redirect::back();;
		}
		if($res->getBody()=="Authentication failure"){
	   		if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return \Redirect::back();;
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
		try{
			$res=$client->request('DELETE',$this->commentAppURL.$editUrl,['form_params'=>[
																					'token'=>config('CommenterConfig.api_key'),
																					'hostname'=>$hostname
																					],
																			'verify'=>false]);
		}catch(RequestException $e){
			if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return \Redirect::back();;
		}
		if($res->getBody()=="Authentication failure"){
	   		if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return \Redirect::back();;
	   	}
		return \Redirect::back();
	}
	public function notifyClients($a,$thread_uri){
		if(config('CommenterConfig.email_notification')){
		$client = new GuzzleHttp\Client();
		$hostname=gethostname();
		$thread_uri=$thread_uri;
		try{
		   	$res=$client->request('POST',$this->commentAppURL.'threadUsers',['form_params'=>[
																							'token'=>config('CommenterConfig.api_key'),
																							'hostname'=>$hostname,
																							'thread'=>$thread_uri
																								],
																			'verify'=>false]);
		}catch(RequestException $e){
			if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return 0;
		}
	   	if($res->getBody()=="Authentication failure"){
	   		if($this->isDebug){
		   		return $res->getBody();	   			
	   		}
	   		return 0;
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
	   		if($temp!=null){
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
		        }
		        \Mail::send(($mailBodyView), ['origin'=>$origin,'fname'=>$receipt->firstname,'thread'=>$hostname."/".$thread_uri], function($message) use($receipt){
		        $message->to($receipt->email, $receipt->firstname.' '.$receipt->lastname)->subject($receipt->subject);
		        });	
	   		}
	        	
	   	}
	   	// return "done";
		}		
		return 0;
	}
}
class Foo{}