<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Link{
	public $name;
	public $url;
	
	public function __construct($n, $u)
	{
		$this->name = $n;
		$this->url = $u;
	}
	
	static function valid_url($url)
	{
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}
}
?>