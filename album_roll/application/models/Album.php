<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Album{
	public $album_id;
	public $title;
	public $artist;
	public $label;
	public $release_date;
	public $blurb;
	public $image_file;
	public $rating;
	public $rating_count;
	public $ordinal;
	
	public function __construct($aid, $t, $a, $l, $rd, $b, $img, $r, $rc, $ord)
	{
		$this->album_id = $aid;
		$this->title = $t;
		$this->artist = $a;
		$this->label = $l;
		$this->release_date = $rd;
		$this->blurb = $b;
		$this->image_file = $img;
		$this->rating = $r;
		$this->rating_count = $rc;
		$this->ordinal = $ord;
	}
	
	public function description()
	{
		if($this->ordinal && !empty($this->blurb))
		{
			return $this->blurb;
		}
		else
		{
			return anchor('search/label/'.$this->label->label_id, $this->label->name).' ('
				.anchor('search/year/'.date('Y', $this->release_date), date('Y', $this->release_date)).')';
		}
	}
	
	public function __toString()
	{
		return $this->artist->name.' - '.$this->title;
	}
	
	static function full_image_url($fname)
	{
		return base_url().'images/albums/'.$fname;
	}
	
	static function image_path($fname)
	{
		return './images/albums/'.$fname;
	}
	
	public function image_url()
	{
		return Album::full_image_url($this->image_file);
	}
	
	static function displayed_sort_options()
	{
		return array('title' => 'title', 'rating' => 'rating',
			'date added' => 'date added', 'release date' => 'release date');
	}
	
	static function sort_options()
	{
		return array('title' => 'albums.title', 'rating' => 'rating', 
			'date added' => 'albums.album_id', 'release date' => 'albums.release_date');
	}
}
?>