<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class AlbumList{
	public $list_id;
	public $title;
	public $blurb;
	public $type;
	public $year;
	public $created_time;
	public $view_count;
	public $user_id;
	public $image_urls;
	public $rating;
	public $rating_count;
	
	public function __construct($lid, $t, $b, $typ, $y, $ct, $vc, $uid, $ius, $r, $rc)
	{
		$this->list_id = $lid;
		$this->title = $t;
		$this->blurb = $b;
		$this->type = $typ;
		$this->year = $y;
		$this->created_time = $ct;
		$this->view_count = $vc;
		$this->user_id = $uid;
		//array of 4 image urls
		$this->image_urls = $ius;
		$this->rating = $r;
		$this->rating_count = $rc;
	}
	
	public function __toString()
	{
		return $this->title;
	}
	
	static function displayed_sort_options()
	{
		return array('title' => 'title', 'rating' => 'rating', 'view count' => 'view count', 
			'date added' => 'date added');
	}
	
	static function sort_options()
	{
		return array('title' => 'lists.title', 'rating' => 'rating', 'view count' => 'lists.view_count', 
			'date added' => 'lists.created_time');
	}
}
?>