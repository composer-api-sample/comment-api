<?php
Route::get('com','asp\commenter\CommentController@index');
Route::post('postComment','asp\commenter\CommentController@postComment');
Route::post('postReply','asp\commenter\CommentController@postReply');
Route::post('editComment','asp\commenter\CommentController@editComment');
Route::post('editReply','asp\commenter\CommentController@editReply');
Route::post('deleteComment','asp\commenter\CommentController@deleteComment');
Route::post('deleteReply','asp\commenter\CommentController@deleteReply');