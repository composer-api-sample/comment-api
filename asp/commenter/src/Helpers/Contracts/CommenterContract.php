<?php
namespace asp\commenter\Helpers\Contracts;
Interface CommenterContract{
	public function getThread($thread_uri);
	public function createThread($thread_uri);
	public function deleteThread();
	public function createComment($input,$thread_uri);
	public function editComment($input,$thread_uri);
	public function deleteComment($input);
	public function createReply($input,$thread_uri);
	public function editReply($input,$thread_uri);
	public function deleteReply($input);
}