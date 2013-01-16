<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class User{
	public $user_id;
	public $name;
	public $member_since;
	public $blurb;
	public $last_seen;
	public $image_file;
	public $email;
	
	public function __construct($uid, $n, $ms, $b, $ls, $iu, $e)
	{
		$this->user_id = $uid;
		$this->name = $n;
		$this->member_since = $ms;
		$this->blurb = $b;
		$this->last_seen = $ls;
		$this->image_file = $iu;
		$this->email = $e;
	}
	
	public function __toString()
	{
		return $this->name;
	}
	
	static function full_image_url($fname)
	{
		return base_url().'images/users/'.$fname;
	}
	
	static function image_path($fname)
	{
		return './images/users/'.$fname;
	}
	
	public function image_url()
	{
		return User::full_image_url($this->image_file);
	}
}
?>