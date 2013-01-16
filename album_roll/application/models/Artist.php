<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Artist{
	public $artist_id;
	public $name;
	
	public function __construct($aid, $n)
	{
		$this->artist_id = $aid;
		$this->name = $n;
	}
	
	public function __toString()
	{
		return $this->name;
	}
}
?>