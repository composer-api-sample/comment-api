{{-- <div class="container commentContainer">  --}}
    {{-- <ul class="custom-list"> --}}
    	<?php $i=0;?>       		<?php $j=0;?>	
       @foreach($comments as $comment)
	       	{{-- <li>	 --}}
	       		<div class="panel panel-default">
	       			<div class="panel-heading customPanelHeader commentAuthor">
			       		<strong>{{$comment->user_id}}</strong>&nbsp;
			       		<span class="comment-time">commented 
			       			<?php 
			       				$created_time = new DateTime('@'.strtotime($comment->created_at));
			       				$current_time = new DateTime('@'.time());
			       				$diff = $current_time->diff($created_time);			       				
			       			?>
			       			<span style="cursor:pointer;">
			       				<time title = "{{date('M d, Y, h:i A e  ',strtotime($comment->created_at))}}">
					       			@if($diff->y > 0)
					       				{{$diff->y}} years
					       			@elseif($diff->m > 0)
					       				{{$diff->m}} months
					       			@elseif($diff->d > 0)
					       				{{$diff->d}} days
					       			@elseif($diff->h > 0)
					       				{{$diff->h}} hours
				       				@elseif($diff->i > 0) 
				       					{{$diff->i}} minutes
				       				@elseif($diff->s > 0)
				       					{{$diff->s}} seconds 
					       			@endif
					       			 ago
				       			</time>
				       		</span>
			       		</span>
			       		<div class="pull-right">
				       		@if($user==$comment->user_id)
					       		<span id="toggleEditComment" class="toggleReply" onclick="toggleEditComment({{$i}});"><span class="glyphicon glyphicon-edit"></span></span>
					       	@endif&nbsp;&nbsp;
				       	</div>
					       	<span id="toggleReply" data-target="{{$i}}" class="toggleNewReply">Reply</span><br>
		       		</div>
		       		<div class="panel-body commentBody">
		       			{{$comment->comment}}
		       		</div>
		       		@if(count($comment->replies)>0)
		       			<div class="panel-footer replyWrapperToggler" onclick="toggleReplyList('replyWrapper-{{$i}}');">
		       				<span>View {{count($comment->replies)}} replies</span>
		       			</div>
		       		@endif
	       		</div>
	       		@if($user==$comment->user_id)
		       		
		       		{{-- <ul class="custom-list"><li> --}}
			       		<div class="customize" id="editCommentDiv{{$i}}">
				       		{{-- <br> --}}
			       			<div class="editComment">
				       			<form method="post" action={{url('/')."/editComment"}} id="editForm{{$i}}" class="">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<input type="hidden" name="_comment" value="{{ $comment->id*21 }}">
									<input type="hidden" name="_thread" value="{{  $_SERVER['REQUEST_URI'] }}">
									<div class="form-group">
										<label for="comment">{{$user}}:</label>
										<textarea type="text" id="comment" name="comment"  placeholder="{{$comment->comment}}" class="form-control editText"></textarea>
									</div>							
									<input type="submit" value="Update Comment" class="btn btn-warning btn-xs editButton"/>
								</form>
							</div>
							{{-- </li><li> --}}
							<div class="deleteComment">				
				       			<form method="post" action={{url('/')."/deleteComment"}} id="deleteForm{{$i}}" class="">
				       				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				       				<input type="hidden" name="_comment" value="{{ $comment->id*21 }}">
				       				<input type="submit" value="Delete Comment" class="btn btn-danger btn-xs"/>
				       			</form>
			       			</div>
		       			</div>       		
					{{-- </li></ul> --}}
	       		@endif
	       		<ul class="customReplyList" id="replyWrapper-{{$i}}">
	       			
		       		@foreach(($comment->replies) as $reply)		       						       		
			       		<li class="custom-divider">			       			
			       			<br>
			       			<div class="reply-wrapper">
				       			<div class="reply">					       		
					       			<span class="replyBody">{{$reply->reply}}</span>	  
					       			<span class="replyAuthor"> - {{$reply->user_id}}</span>
					       			<?php 
					       				$created_time = new DateTime('@'.strtotime($reply->created_at));
					       				$current_time = new DateTime('@'.time());
					       				$diff = $current_time->diff($created_time);			       				
					       			?>
					       			<span class="comment-time" style="cursor:pointer;">
					       				<time title = "{{date('M d, Y, h:i A e  ',strtotime($reply->created_at))}}">
							       			@if($diff->y > 0)
							       				{{$diff->y}} years
							       			@elseif($diff->m > 0)
							       				{{$diff->m}} months
							       			@elseif($diff->d > 0)
							       				{{$diff->d}} days
							       			@elseif($diff->h > 0)
							       				{{$diff->h}} hours
						       				@elseif($diff->i > 0) 
						       					{{$diff->i}} minutes
						       				@elseif($diff->s > 0)
						       					{{$diff->s}} seconds 
							       			@endif
							       			 ago
						       			</time>
						       		</span>
			       				</div>
			       				
			       				{{-- <ul class="custom-list"> --}}
					       			@if($user==$reply->user_id)
						       			{{-- <br> --}}
						       			<span id="toggleEditReply" class="toggleReply" onclick="toggleEditReply({{$j}});">Edit Reply</span>
								       		<div class="customize" id="editReplyDiv{{$j++}}">
									       		{{-- <li> --}}<br>
										       		<div class="editReply">
								       					<form method="post" action={{url('/')."/editReply"}} class="">
															<input type="hidden" name="_token" value="{{ csrf_token() }}">
															<input type="hidden" name="_reply" value="{{ $reply->id*21 }}">
															<input type="hidden" name="_thread" value="{{  $_SERVER['REQUEST_URI'] }}">
															<div class="form-group">
															<label for="reply">{{$user}}:</label>
																<input type="text" name="reply" id="reply"  placeholder="{{$reply->reply}}" class="form-control editReply"/>
															</div>										
															<input type="submit" value="Update Reply" class="btn btn-warning btn-xs editButton"/>
														</form>
													</div>				       			
											{{-- </li><li> --}}
													<div class="deleteReply">
								       					<form method="post" action={{url('/')."/deleteReply"}} class="">
										       				<input type="hidden" name="_token" value="{{ csrf_token() }}">
										       				<input type="hidden" name="_reply" value="{{ $reply->id*21 }}">
										       				<input type="submit" value="Delete Reply" class="btn btn-danger btn-xs"/>
										       			</form>
													</div>
												{{-- </li> --}}
											</div>
									@endif
								</div>
							{{-- </ul> --}}
						</li>
		       		@endforeach
	       		</ul>
	       		@if($user)
		       		<div class="newReply" id="newReply{{$i}}">
		       			<br>
			       		<?php echo $replyView[$i++];?>
		       		</div><br>
	       		@endif
	       	{{-- </li> --}}
       @endforeach      
   {{-- </ul> --}}
