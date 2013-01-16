<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Label{
	public $label_id;
	public $name;
	
	public function __construct($lid, $n)
	{
		$this->label_id = $lid;
		$this->name = $n;
	}
	
	public function __toString()
	{
		return $this->name;
	}
}
?>