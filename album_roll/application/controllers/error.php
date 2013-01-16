<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Error extends MY_Controller
{
	function Error()
	{
		parent::MY_Controller();
	}
	
	function not_found()
	{
		$data['error_title'] = 'page not found';
		$data['error_message'] = 'Not sure how you got here, but there\'s nothing to see. Click away!';
		$data['view'] = 'error';
		$this->_load($data);
	}
}
?>