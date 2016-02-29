<form method="post" action={{url('/')."/postReply"}} class="">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" name="_thread" value="{{  $_SERVER['REQUEST_URI'] }}">
	<input type="hidden" name="commentId" value="{{$comment->id}}" >
	<div class="form-group">
		<label for="reply">{{$user}}:</label>
		<input type="text" name="reply" id="reply" placeholder="Type your reply" class="form-control" />
	</div>
	<input type="submit" value="Reply"class="btn btn-info" />
</form>