<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Search extends MY_Controller
{
	function Search()
	{
		parent::MY_Controller();
	}
	
	public function keyword()
	{
		$terms = $this->input->post('search');
		if(empty($terms))
		{
			redirect($this->input->post('current_url'));
		}
		else
		{
			redirect('/search/keywords/'.$terms);
		}
	}
	
	public function keywords($terms)
	{
		$terms = str_replace('%20', ' ', $terms);
		$data['albums'] = FALSE;
		$data['lists'] = FALSE;
		
		$data['search_title'] = 'search: '.$terms;
		
		$data['albums'] = $this->_search_albums($terms, 0, 'release date', 'desc');
			
		$data['lists'] = $this->_search_lists($terms, 0, 'date added', 'desc');
			
		$data['view'] = 'search_result';
		$this->_load($data);
	}
	
	public function refresh_search_albums()
	{
		$offset = $this->input->post('offset');
		$terms = $this->input->post('terms');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($terms)
		{
			echo $this->_search_albums($terms, $offset, $order_by, $direction);
		}
	}
	
	private function _search_albums($terms, $offset, $order_by, $direction)
	{
		$terms = explode(' ', $terms);
		$albums = $this->album_model->search_albums($terms, PAGED_LIST_SIZE, intval($offset), $order_by,
			$direction);
		$count = $this->album_model->count_search_albums($terms);
		return $this->load->view('list_of_albums', array('albums' => $albums, 'can_remove' => 
			FALSE, 'can_reorder' => FALSE, 'pageable' => pageable($count), 'count' => $count, 
			'offset' => $offset, 'post_url' => site_url('search/refresh_search_albums'),
			'post_values' => "'terms':'".implode(' ', $terms)."'", 'sortable' => TRUE, 'order_by' =>
			$order_by, 'direction' => $direction), TRUE);
	}
	
	public function refresh_search_lists()
	{
		$offset = $this->input->post('offset');
		$terms = $this->input->post('terms');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($terms)
		{
			echo $this->_search_lists($terms, $offset, $order_by, $direction);
		}
	}
	
	private function _search_lists($terms, $offset, $order_by, $direction)
	{
		$terms = explode(' ', $terms);
		$lists = $this->list_model->search_lists($terms, PAGED_LIST_SIZE, intval($offset), $order_by,
			$direction);
		$count = $this->list_model->count_search_lists($terms);
		return $this->load->view('list_of_lists', array('lists' => $lists, 'pageable' => pageable($count),
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('search/refresh_search_lists'), 
			'post_values' => "'terms':'".implode(' ', $terms)."'", 'sortable' => TRUE, 'order_by' =>
			$order_by, 'direction' => $direction), TRUE);
	}
	
	
	public function artist($artist_id)
	{
		//only albums
		$data['albums'] = FALSE;
		$data['lists'] = FALSE;
		
		$artist = $this->album_model->get_artist($artist_id);
		if(!$artist)
		{
			redirect('error/not_found');
		}
		
		$data['search_title'] = $artist->name;
		
		$data['albums'] = $this->_artist_albums($artist_id, 0, 'release date', 'desc');
			
		$data['view'] = 'search_result';
		$this->_load($data);
	}
	
	public function refresh_artist_albums()
	{
		$offset = $this->input->post('offset');
		$artist_id = $this->input->post('artist_id');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($artist_id)
		{
			echo $this->_artist_albums($artist_id, $offset, $order_by, $direction);
		}
	}
	
	private function _artist_albums($artist_id, $offset, $order_by, $direction)
	{
		$albums = $this->album_model->albums_by_artist($artist_id, PAGED_LIST_SIZE, $offset, $order_by, 
			$direction);
		$count = $this->album_model->count_albums_by_artist($artist_id);
		return $this->load->view('list_of_albums', array('albums' => $albums, 'can_remove' => 
			FALSE, 'can_reorder' => FALSE, 'pageable' => pageable($count), 'count' => $count, 
			'offset' => $offset, 'post_url' => site_url('search/refresh_artist_albums'),
			'post_values' => "'artist_id':".$artist_id, 'sortable' => TRUE, 'order_by' => $order_by,
			'direction' => $direction), TRUE);
	}
	
	
	
	public function label($label_id)
	{
		//only albums
		$data['albums'] = FALSE;
		$data['lists'] = FALSE;
		
		$label = $this->album_model->get_label($label_id);
		if(!$label)
		{
			redirect('error/not_found');
		}
		
		$data['search_title'] = $label->name;
		
		$data['albums'] = $this->_label_albums($label_id, 0, 'release date', 'desc');
			
		$data['view'] = 'search_result';
		$this->_load($data);
	}
	
	public function refresh_label_albums()
	{
		$offset = $this->input->post('offset');
		$label_id = $this->input->post('label_id');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($label_id)
		{
			echo $this->_label_albums($label_id, $offset, $order_by, $direction);
		}
	}
	
	private function _label_albums($label_id, $offset, $order_by, $direction)
	{
		$albums = $this->album_model->albums_by_label($label_id, PAGED_LIST_SIZE, $offset, $order_by, 
			$direction);
		$count = $this->album_model->count_albums_by_label($label_id);
		return $this->load->view('list_of_albums', array('albums' => $albums, 'can_remove' => 
			FALSE, 'can_reorder' => FALSE, 'pageable' => pageable($count), 'count' => $count, 
			'offset' => $offset, 'post_url' => site_url('search/refresh_label_albums'),
			'post_values' => "'label_id':".$label_id, 'sortable' => TRUE, 'order_by' => $order_by,
			'direction' => $direction), TRUE);
	}
	
	
	
	public function tag($tag_id)
	{
		//albums and lists
		$data['albums'] = FALSE;
		$data['lists'] = FALSE;
		
		$tag = $this->album_model->get_tag($tag_id);
		if(!$tag)
		{
			redirect('error/not_found');
		}
		
		$data['search_title'] = $tag->name;
		
		$data['albums'] = $this->_tag_albums($tag_id, 0, 'release date', 'desc');
			
		$data['lists'] = $this->_tag_lists($tag_id, 0, 'date added', 'desc');
			
		$data['view'] = 'search_result';
		$this->_load($data);
	}
	
	public function refresh_tag_albums()
	{
		$offset = $this->input->post('offset');
		$tag_id = $this->input->post('tag_id');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($tag_id)
		{
			echo $this->_tag_albums($tag_id, $offset, $order_by, $direction);
		}
	}
	
	private function _tag_albums($tag_id, $offset, $order_by, $direction)
	{
		$albums = $this->album_model->albums_by_tag($tag_id, PAGED_LIST_SIZE, $offset, $order_by,
			$direction);
		$count = $this->album_model->count_albums_by_tag($tag_id);
		return $this->load->view('list_of_albums', array('albums' => $albums, 'can_remove' => 
			FALSE, 'can_reorder' => FALSE, 'pageable' => pageable($count), 'count' => $count, 
			'offset' => $offset, 'post_url' => site_url('search/refresh_tag_albums'),
			'post_values' => "'tag_id':".$tag_id, 'sortable' => TRUE, 'order_by' => $order_by,
			'direction' => $direction), TRUE);
	}
	
	public function refresh_tag_lists()
	{
		$offset = $this->input->post('offset');
		$tag_id = $this->input->post('tag_id');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($tag_id)
		{
			echo $this->_tag_lists($tag_id, $offset, $order_by, $direction);
		}
	}
	
	private function _tag_lists($tag_id, $offset, $order_by, $direction)
	{
		$lists = $this->list_model->lists_by_tag($tag_id, PAGED_LIST_SIZE, $offset, $order_by, $direction);
		$count = $this->list_model->count_lists_by_tag($tag_id);
		return $this->load->view('list_of_lists', array('lists' => $lists, 'pageable' => pageable($count),
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('search/refresh_tag_lists'),
			'post_values' => "'tag_id':".$tag_id, 'sortable' => TRUE, 'order_by' => $order_by,
			'direction' => $direction), TRUE);
	}
	
	
	public function year($year)
	{
		//albums and lists
		$data['albums'] = FALSE;
		$data['lists'] = FALSE;
		
		$data['search_title'] = $year;
		
		$data['albums'] = $this->_year_albums($year, 0, 'release date', 'desc');
			
		$data['lists'] = $this->_year_lists($year, 0, 'date added', 'desc');
			
		$data['view'] = 'search_result';
		$this->_load($data);
	}
	
	public function refresh_year_albums()
	{
		$offset = $this->input->post('offset');
		$year = $this->input->post('year');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($year)
		{
			echo $this->_year_albums($year, $offset, $order_by, $direction);
		}
	}
	
	private function _year_albums($year, $offset, $order_by, $direction)
	{
		$albums = $this->album_model->albums_by_year($year, PAGED_LIST_SIZE, $offset, $order_by,
			$direction);
		$count = $this->album_model->count_albums_by_year($year);
		return $this->load->view('list_of_albums', array('albums' => $albums, 'can_remove' => 
			FALSE, 'can_reorder' => FALSE, 'pageable' => pageable($count), 'count' => $count, 
			'offset' => $offset, 'post_url' => site_url('search/refresh_year_albums'),
			'post_values' => "'year':".$year, 'sortable' => TRUE, 'order_by' => $order_by,
			'direction' => $direction), TRUE);
	}
	
	public function refresh_year_lists()
	{
		$offset = $this->input->post('offset');
		$year = $this->input->post('year');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($year)
		{
			echo $this->_tag_lists($year, $offset, $order_by, $direction);
		}
	}
	
	private function _year_lists($year, $offset, $order_by, $direction)
	{
		$lists = $this->list_model->lists_by_year($year, PAGED_LIST_SIZE, $offset, $order_by, $direction);
		$count = $this->list_model->count_lists_by_year($year);
		return $this->load->view('list_of_lists', array('lists' => $lists, 'pageable' => pageable($count),
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('search/refresh_year_lists'),
			'post_values' => "'year':".$year, 'sortable' => TRUE, 'order_by' => $order_by,
			'direction' => $direction), TRUE);
	}
	
	
	public function decade($decade)
	{
		$data['albums'] = FALSE;
		$data['lists'] = FALSE;
		
		$data['search_title'] = $decade;
		
		$data['albums'] = $this->_decade_albums($decade, 0, 'release date', 'desc');
		
		$data['lists'] = $this->_decade_lists($decade, 0, 'date added', 'desc');
			
		$data['view'] = 'search_result';
		$this->_load($data);
	}
	
	public function refresh_decade_albums()
	{
		$offset = $this->input->post('offset');
		$decade = $this->input->post('decade');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($decade)
		{
			echo $this->_decade_albums($decade, $offset, $order_by, $direction);
		}
	}
	
	private function _decade_albums($decade, $offset, $order_by, $direction)
	{
		$albums = $this->album_model->albums_by_decade($decade, PAGED_LIST_SIZE, $offset, $order_by,
			$direction);
		$count = $this->album_model->count_albums_by_decade($decade);
		return $this->load->view('list_of_albums', array('albums' => $albums, 'can_remove' => 
			FALSE, 'can_reorder' => FALSE, 'pageable' => pageable($count), 'count' => $count, 
			'offset' => $offset, 'post_url' => site_url('search/refresh_decade_albums'),
			'post_values' => "'decade':".$decade, 'sortable' => TRUE, 'order_by' => $order_by,
			'direction' => $direction), TRUE);
	}
	
	public function refresh_decade_lists()
	{
		$offset = $this->input->post('offset');
		$decade = $this->input->post('decade');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($decade)
		{
			echo $this->_decade_lists($decade, $offset, $order_by, $direction);
		}
	}
	
	private function _decade_lists($decade, $offset, $order_by, $direction)
	{
		$lists = $this->list_model->lists_by_year($decade, PAGED_LIST_SIZE, $offset, $order_by, 
			$direction);
		$count = $this->list_model->count_lists_by_year($decade);
		return $this->load->view('list_of_lists', array('lists' => $lists, 'pageable' => pageable($count),
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('search/refresh_decade_lists'),
			'post_values' => "'decade':".$decade, 'sortable' => TRUE, 'order_by' => $order_by,
			'direction' => $direction), TRUE);
	}
	
	
	public function recent()
	{
		//albums and lists
		$data['albums'] = FALSE;
		$data['lists'] = FALSE;
		
		$data['search_title'] = 'recent';
		
		$data['albums'] = $this->_recent_albums(0);
			
		$data['lists'] = $this->_recent_lists(0);
			
		$data['view'] = 'search_result';
		$this->_load($data);
	}
	
	public function refresh_recent_albums()
	{
		$offset = $this->input->post('offset');
		echo $this->_recent_albums($offset);
	}
	
	private function _recent_albums($offset)
	{
		$albums = $this->album_model->recent_albums(PAGED_LIST_SIZE, $offset);
		$count = $this->album_model->count_albums();
		return $this->load->view('list_of_albums', array('albums' => $albums, 'can_remove' => 
			FALSE, 'can_reorder' => FALSE, 'pageable' => pageable($count), 'count' => $count, 
			'offset' => $offset, 'post_url' => site_url('search/refresh_recent_albums'),
			'post_values' => '', 'sortable' => FALSE), TRUE);
	}
	
	public function refresh_recent_lists()
	{
		$offset = $this->input->post('offset');
		echo $this->_recent_lists($offset);
	}
	
	private function _recent_lists($offset)
	{
		$lists = $this->list_model->recent_lists(PAGED_LIST_SIZE, $offset);
		$count = $this->list_model->count_lists();
		return $this->load->view('list_of_lists', array('lists' => $lists, 'pageable' => pageable($count),
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('search/refresh_recent_lists'),
			'post_values' => '', 'sortable' => FALSE), TRUE);
	}
	
	
	public function trendy()
	{
		//albums and lists
		$data['albums'] = FALSE;
		$data['lists'] = FALSE;
		
		$data['search_title'] = 'trendy';
		
		$data['albums'] = $this->_trending_albums(0);
			
		$data['lists'] = $this->_trending_lists(0);
			
		$data['view'] = 'search_result';
		$this->_load($data);
	}
	
	public function refresh_trending_albums()
	{
		$offset = $this->input->post('offset');
		echo $this->_trending_albums($offset);
	}
	
	private function _trending_albums($offset)
	{
		$albums = $this->album_model->trending_albums(PAGED_LIST_SIZE, $offset);
		$count = $this->album_model->count_trending_albums();
		return $this->load->view('list_of_albums', array('albums' => $albums, 'can_remove' => 
			FALSE, 'can_reorder' => FALSE, 'pageable' => pageable($count), 'count' => $count, 
			'offset' => $offset, 'post_url' => site_url('search/refresh_trending_albums'),
			'post_values' => '', 'sortable' => FALSE), TRUE);
	}
	
	public function refresh_trending_lists()
	{
		$offset = $this->input->post('offset');
		echo $this->_trending_lists($offset);
	}
	
	private function _trending_lists($offset)
	{
		$lists = $this->list_model->lists_by_views(PAGED_LIST_SIZE, $offset);
		$count = $this->list_model->count_lists();
		return $this->load->view('list_of_lists', array('lists' => $lists, 'pageable' => pageable($count),
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('search/refresh_trending_lists'), 'post_values' => '', 'sortable' => FALSE), TRUE);
	}
	
	
	public function recommended()
	{
		$user_id = $this->_check_user();
		if($user_id)
		{
			//only albums
			$data['albums'] = FALSE;
			$data['lists'] = FALSE;
			
			$data['search_title'] = 'recommended';
			
			$data['albums'] = $this->_recommended_albums($user_id, 0);
				
			$data['view'] = 'search_result';
			$this->_load($data);
		}
	}
	
	public function refresh_recommended_albums()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$offset = $this->input->post('offset');
			echo $this->_recommended_albums($user_id, $offset);
		}
	}
	
	private function _recommended_albums($user_id, $offset)
	{
		$albums = $this->album_model->recommended_albums($user_id, PAGED_LIST_SIZE, intval($offset));
		$count = $this->album_model->count_recommended_albums($user_id);
		return $this->load->view('list_of_albums', array('albums' => $albums, 'can_remove' => 
			TRUE, 'can_reorder' => FALSE, 'pageable' => pageable($count), 'count' => $count, 
			'offset' => $offset, 'post_url' => site_url('search/refresh_recommended_albums'),
			'post_values' => "", 'sortable' => FALSE), TRUE);
	}
	
	public function hide_recommendation()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$album_id = $this->input->post('album_id');
			if($album_id)
			{
				$heard_it_id = $this->user_model->heard_it_list($user_id);
				$this->list_model->add_album($heard_it_id, $album_id, '');
				echo $this->_recommended_albums($user_id, 0);
			}
			else
			{
				echo SESSION_EXPIRED;
			}
		}
	}
}
?>