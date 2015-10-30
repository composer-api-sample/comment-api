{{-- <div class="container commentContainer"> --}} 
    <ul class="custom-list">
    	<?php $i=0;?>       		<?php $j=0;?>	
       @foreach($comments as $comment)
	       	<li>	
	       		<div class="comment">
	       		<span class="commentAuthor">{{$comment->user_id}}:</span>
	       			<span class="commentBody">{{$comment->comment}}</span>	       			
	       		</div>
	       		
	       		@if($user==$comment->user_id)
		       		<br><br>
		       		<span id="toggleEditComment" class="toggleReply" onclick="toggleEditComment({{$i}});">Edit Comment</span>
	       		{{-- <ul class="custom-list"><li> --}}
		       		<div class="customize" id="editCommentDiv{{$i}}">
		       			<div class="editComment">
			       			<form method="post" action={{url('/')."/editComment"}} id="editForm{{$i}}" class="form-inline">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="_comment" value="{{ $comment->id*21 }}">
								<input type="hidden" name="_thread" value="{{  $_SERVER['REQUEST_URI'] }}">
								<div class="form-group">
									{{-- <label for="comment">Edit</label> --}}
									<input type="text" id="comment" name="comment"  placeholder="{{$comment->comment}}" class="form-control editText"/>
								</div>							
								<input type="submit" value="Update" class="btn btn-warning btn-xs editButton"/>
							</form>
						</div>
						{{-- </li><li> --}}
						<div class="deleteComment">
			       			<form method="post" action={{url('/')."/deleteComment"}} id="deleteForm{{$i}}" class="form-inline">
			       				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			       				<input type="hidden" name="_comment" value="{{ $comment->id*21 }}">
			       				<input type="submit" value="delete" class="btn btn-danger btn-xs"/>
			       			</form>
		       			</div>
	       			</div>       		
				{{-- </li></ul> --}}
	       		@endif
	       		<br>
	       		<ul class="customReplyList">
	       			
		       		@foreach(($comment->replies) as $reply)		       						       		
			       		<li class="custom-divider">			       			
			       			<br>
			       			<div class="reply">
					       		<span class="replyAuthor">{{$reply->user_id}}:</span>
				       			<span class="replyBody">{{$reply->reply}}</span>	  
		       				</div>
		       				
		       				{{-- <ul class="custom-list"> --}}
				       			@if($user==$reply->user_id)
					       			<br>
					       			<span id="toggleEditReply" class="toggleReply" onclick="toggleEditReply({{$j}});">Edit Reply</span>
							       		<div class="customize" id="editReplyDiv{{$j++}}">
								       		{{-- <li> --}}
									       		<div class="editReply">
							       					<form method="post" action={{url('/')."/editReply"}} class="form-inline">
														<input type="hidden" name="_token" value="{{ csrf_token() }}">
														<input type="hidden" name="_reply" value="{{ $reply->id*21 }}">
														<input type="hidden" name="_thread" value="{{  $_SERVER['REQUEST_URI'] }}">
														<div class="form-group">
														{{-- <label for="reply">Edit reply</label> --}}
															<input type="text" name="reply" id="reply"  placeholder="{{$reply->reply}}" class="form-control editReply"/>
														</div>										
														<input type="submit" value="Update" class="btn btn-warning btn-xs editButton"/>
													</form>
												</div>				       			
										{{-- </li><li> --}}
												<div class="deleteReply">
							       					<form method="post" action={{url('/')."/deleteReply"}} class="form-inline">
									       				<input type="hidden" name="_token" value="{{ csrf_token() }}">
									       				<input type="hidden" name="_reply" value="{{ $reply->id*21 }}">
									       				<input type="submit" value="delete" class="btn btn-danger btn-xs"/>
									       			</form>
												</div>
											{{-- </li> --}}
										</div>
								@endif
							{{-- </ul> --}}
						</li>
		       		@endforeach
		       		@if($user)
			       		<br>
		       			<span id="toggleReply" class="toggleNewReply" onclick="toggleReply({{$i}});">Reply</span><br>
			       		<div class="newReply" id="newReply{{$i}}">
				       		<?php echo $replyView[$i++];?>
			       		</div><br>
		       		@endif
	       		</ul>
	       	</li>
       @endforeach      
   </ul>
