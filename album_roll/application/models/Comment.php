<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Comment{
	public $comment_id;
	//the User who posted the comment
	public $user;
	public $timestamp;
	public $text;
	
	public function __construct($cid, $u, $ts, $t)
	{
		$this->comment_id = $cid;
		$this->user = $u;
		$this->timestamp = $ts;
		$this->text = $t;
	}
	
	public function image_url()
	{
		return $user->image_url;
	}
	
	public function __toString()
	{
		return '('.$this->timestamp.') '.$user->name.' said:';
	}
}
?>