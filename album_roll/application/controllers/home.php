<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Home extends MY_Controller
{
	function Home()
	{
		parent::MY_Controller();
	}
	
	public function welcome()
	{
		$data['user_count'] = $this->user_model->count_users();
		$data['album_count'] = $this->album_model->count_albums();
		$data['list_count'] = $this->list_model->count_lists();
		$data['album_of_the_week'] = $this->album_model->get_album(7);
		$data['trendy_tags'] = $this->album_model->trending_tags(20, 0);
		$data['top_albums'] = $this->_top_albums(0);
		$data['top_lists'] = $this->_top_lists(0);
		$data['view'] = 'home';
		$this->_load($data);
	}
	
	public function refresh_top_albums()
	{
		$offset = $this->input->post('offset');
		echo $this->_top_albums($offset);
	}
	
	private function _top_albums($offset)
	{
		$albums = $this->album_model->top_albums(PAGED_LIST_SIZE, intval($offset));
		$count = $this->album_model->count_top_albums();
		return $this->load->view('list_of_albums', array('albums' => $albums, 'can_remove' => 
			FALSE, 'can_reorder' => FALSE, 'pageable' => pageable($count), 'count' => $count, 
			'offset' => $offset, 'post_url' => site_url('home/refresh_top_albums'),
			'post_values' => '', 'sortable' => FALSE), TRUE);
	}
	
	public function refresh_top_lists()
	{
		$offset = $this->input->post('offset');
		echo $this->_top_lists($offset);
	}
	
	private function _top_lists($offset)
	{
		$lists = $this->list_model->top_lists(PAGED_LIST_SIZE, intval($offset));
		$count = $this->list_model->count_top_lists();
		return $this->load->view('list_of_lists', array('lists' => $lists, 'pageable' => pageable($count),
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('home/refresh_top_lists'),
			'post_values' => '', 'sortable' => FALSE), TRUE);
	}
	
	public function about()
	{
		$data['view'] = 'about';
		$this->_load($data);
	}
	
	public function random_album()
	{
		redirect('albums/album/'.$this->album_model->random());
	}
	
	public function random_user()
	{
		redirect('users/profile/'.$this->user_model->random());
	}
	
	public function random_list()
	{
		redirect('lists/roll/'.$this->list_model->random());
	}
}
?>