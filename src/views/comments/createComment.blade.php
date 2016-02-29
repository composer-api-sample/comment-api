@extends('commenter::layouts.comment')
<div class="commentContainer">
@if($user)
	<form method="post" action={{url('/')."/postComment"}}>
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_thread" value="{{  $_SERVER['REQUEST_URI'] }}">
		<div class="form-group">
			<label for="comment">{{$user}}:</label>
			<textarea name="comment" id="comment" placeholder="Write a new comment" class="form-control"></textarea><br>
		</div>
		<input type="submit" value="Post Comment" class="postButton btn btn-primary"/>
	</form>
	<br>
@endif	
<?php echo $loader;?>
</div>