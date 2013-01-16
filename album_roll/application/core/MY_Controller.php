<?php
define('SESSION_EXPIRED', 'SESSION_EXPIRED');
class  MY_Controller  extends  CI_Controller  {

    function MY_Controller ()  
	{
        parent::__construct();
    }
	
	//assumes $data['view'] is assigned
	protected function _load($data)
	{
		$data['user_id'] = $this->session->userdata('user_id');
		if($data['user_id'])
		{
			$data['current_user'] = $this->user_model->get_user($data['user_id']);
			$data['favorites_id'] = $this->user_model->favorites_list($data['user_id']);
			$data['current_rotation_id'] = $this->user_model->current_rotation_list($data['user_id']);
			$data['left_column'] = 'user_sidebar';
			$recommended = $this->album_model->recommended_albums($data['user_id'], 1, 0);
			if(!empty($recommended))
			{
				$data['recommended_header'] = anchor('search/recommended', 'recommended');
				$data['recommended'] = $recommended;
			}
			else
			{
				$data['recommended_header'] = 'top rated';
				$data['recommended'] = $this->album_model->top_albums(1, 0);
			}
		}
		else
		{
			$data['left_column'] = 'signin';
		}
		$data['recent_albums'] = $this->album_model->recent_albums(1, 0);
		$data['trending_albums'] = $this->album_model->trending_albums(1, 0);
		$data['trending_by_year'] = $this->album_model->trending_albums_by_year(date('Y'), 1, 0);
		$this->load->view('template', $data);
	}
	
	protected function _check_user()
	{
		$user_id = $this->session->userdata('user_id');
		if(!$user_id)
		{
			redirect('/home/welcome');
		}
		else
		{
			return $user_id;
		}
	}
	
	protected function _check_user_ajax()
	{
		$user_id = $this->session->userdata('user_id');
		if(!$user_id)
		{
			echo SESSION_EXPIRED;
		}
		return $user_id;
	}
}
?>