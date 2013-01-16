<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Hooks
{
	public function session_from_cookie()
	{
		$ci =& get_instance();
		if(!$ci->session->userdata('user_id'))
		{
			if(isset($_COOKIE['user_id']))
			{
				$user_id = $_COOKIE['user_id'];
				if($user_id)
				{
					$ci->session->set_userdata(array('user_id' => $user_id));
					setcookie('user_id', $user_id, time()+60*60*24*7, '/');
				}
			}
		}
	}
}
?>