@extends('commenter::layouts.comment')
<div class="container commentContainer">
<?php echo $loader;?>
@if($user)
	<form method="post" action={{url('/')."/postComment"}} class="form-inline">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_thread" value="{{  $_SERVER['REQUEST_URI'] }}">
		<div class="form-group">
			<label for="comment">{{$user}}:</label>
			<input type="text" name="comment" id="comment" class="form-control" /><br>
		</div>
		<input type="submit" value="post" class="postButton btn btn-primary"/>
	</form>
@endif	
</div>