<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Tag{
	public $tag_id;
	public $name;
	
	public function __construct($tid, $n)
	{
		$this->tag_id = $tid;
		$this->name = $n;
	}
	
	public function __toString()
	{
		return $this->name;
	}
}
?>