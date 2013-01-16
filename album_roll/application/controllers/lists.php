<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Lists extends MY_Controller
{
	function Lists()
	{
		parent::MY_Controller();
	}
	
	public function roll($id)
	{
		$list = $this->list_model->get_list($id);
		if(!$list || $list->type == ListType::HeardIt)
		{
			redirect('error/not_found');
		}
		$data['list'] = $list;
		$user_id = $this->session->userdata('user_id');
		$data['logged_in'] = $user_id != FALSE;
		$data['is_current_user'] = $user_id == $list->user_id;
		if(!$data['is_current_user'])
		{
			$this->list_model->update_list_view_count($list->list_id, $list->user_id);
		}
		$user = $this->user_model->get_user($list->user_id);
		$data['user_name'] = $user->name;
		$data['title_editable'] = $data['is_current_user'] && $list->type != ListType::Favorites 
			&& $list->type != ListType::CurrentRotation;
		$data['tags'] = $this->_format_tags($id, $data['is_current_user']);
		$data['average_rating'] = round($this->list_model->average_rating($id), 2);
		$data['total_votes'] = $this->list_model->rating_count($id);
		if($list->type == ListType::Year)
		{
			$data['year_string'] = 'Year: '.anchor('search/year/'.$list->year, $list->year);
		}
		else if($list->type == ListType::Decade)
		{
			$data['year_string'] = 'Decade: '.anchor('search/decade/'.$list->year, $list->year);
		}
		else
		{
			$data['year_string'] = '';
		}
		$albums = $this->album_model->albums_by_list($id, 50, 0);
		$can_reorder = FALSE;
		if($data['is_current_user'])
		{
			$can_reorder = $id;
		}
		$data['list_albums'] = $this->load->view('list_of_albums', array('albums' => $albums, 
			'can_remove' => $data['is_current_user'], 'can_reorder' => $can_reorder, 'pageable' => FALSE,
			'sortable' => FALSE),
			TRUE);
			
		$data['comments'] = $this->_comments($id, 0);
		$data['related_lists'] = $this->_related_lists($id, 0);
		$data['view'] = 'list';
		$this->_load($data);
	}
	
	public function user($id)
	{
		$user = $this->user_model->get_user($id);
		if(!$user)
		{
			redirect('home/welcome');
		}
		$data['search_title'] = anchor('users/profile/'.$id, $user->name)."'s lists";
		$data['lists'] = $this->_lists_user($id, 0, 'date added', 'desc');
		$data['albums'] = FALSE;
		$data['view'] = 'search_result';
		$this->_load($data);
	}
	
	private function _format_tags($list_id, $is_current_user)
	{
		$tags = $this->list_model->tags($list_id);
		if(empty($tags))
		{
			return 'No tags have been added yet.';
		}
		
		$html = '';
		foreach($tags as $tag)
		{
			$html = $html.anchor('search/tag/'.$tag->tag_id, $tag->name);
			if($is_current_user)
			{
				$html = $html.'<img id="remove_tag_button" src="'.base_url().'images/x_icon.jpg" class="x_icon" alt="'.$tag->tag_id.'" />';
			}
			if($tag != end($tags))
			{
				$html = $html.'&nbsp<wbr/>&nbsp<wbr/>&nbsp<wbr/>&nbsp<wbr/>';
			}
		}
		
		return $html;
	}
	
	public function edit_title()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$title = $this->input->post('title');
			$list_id = $this->input->post('list_id');
			$result = $this->list_model->update_list_title($list_id, $user_id, $title);
			if($result == -2)
			{
				echo '0';
			}
			else
			{
				echo $title;
			}
		}
	}
	
	public function rate()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$rating = intval($this->input->post('rating'));
			$list_id = $this->input->post('list_id');
			$this->list_model->add_rating($list_id, $rating, $user_id);
			
			echo round($this->list_model->average_rating($list_id), 2);
			echo ':';
			echo $this->list_model->rating_count($list_id);
		}
	}
	
	public function edit_blurb()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$blurb = $this->input->post('blurb');
			$list_id = $this->input->post('list_id');
			$this->list_model->update_list_blurb($list_id, $user_id, $blurb);
			echo $blurb;
		}
	}
	
	public function add_tag()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$tag_name = $this->input->post('tag_name');
			if($tag_name)
			{
				$list_id = $this->input->post('list_id');
				$this->list_model->add_tag($list_id, $tag_name);
				
				echo $this->_format_tags($list_id, TRUE);
			}
		}
	}
	
	public function remove_tag()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$tag_id = $this->input->post('tag_id');
			$list_id = $this->input->post('list_id');
			$this->list_model->remove_tag($list_id, $tag_id);
			
			echo $this->_format_tags($list_id, TRUE);
		}
	}
	
	public function create()
	{
		$user_id = $this->_check_user();
		$list_title = $this->input->post('title');
		if($list_title)
		{
			$blurb = $this->input->post('blurb');
			$type_string = $this->input->post('type');
			switch($type_string)
			{
				case 'Themed':
					$type = ListType::Themed;
					$year = '';
					break;
				case 'Year':
					$type = ListType::Year;
					$year = $this->input->post('year');
					break;
				case 'Decade':
					$type = ListType::Decade;
					$year = $this->input->post('decade');
					break;
			}
			$list_id = $this->list_model->create_list($list_title, $blurb, $type, $year, $user_id);
			if($list_id > 0)
			{
				redirect('lists/roll/'.$list_id);
			}
			else
			{
				$data['title_error'] = '<div id="title_error" class="error_text">You already have a list with that title.</div>';
				$data['view'] = 'new_list';
				$this->_load($data);
			}
		}
		else
		{
			$data['title_error'] = '';
			$data['view'] = 'new_list';
			$this->_load($data);
		}
	}
	
	public function add_album()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$list_id = $this->input->post('list_id');
			$album_id = $this->input->post('album_id');
			if($list_id && $album_id)
			{
				$this->list_model->add_album($list_id, $album_id, '');
				echo $this->_editable_albums($list_id);
			}
			else
			{
				echo SESSION_EXPIRED;
			}
		}
	}
	
	public function remove_album()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$list_id = $this->input->post('list_id');
			$album_id = $this->input->post('album_id');
			if($list_id && $album_id)
			{
				$this->list_model->remove_album($list_id, $album_id);
				$heard_it_id = $this->user_model->heard_it_list($user_id);
				$this->list_model->add_album($heard_it_id, $album_id, '');
				echo $this->_editable_albums($list_id);
			}
			else
			{
				echo SESSION_EXPIRED;
			}
		}
	}
	
	public function reorder()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$list_id = $this->input->post('list_id');
			$old_ordinal = $this->input->post('old_ordinal');
			$new_ordinal = $this->input->post('new_ordinal');
			if($list_id && $old_ordinal && $new_ordinal)
			{
				$this->list_model->reorder($list_id, $old_ordinal, $new_ordinal);
				echo $this->_editable_albums($list_id);
			}
			else
			{
				echo SESSION_EXPIRED;
			}
		}
	}
	
	public function edit_album_blurb()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$list_id = $this->input->post('list_id');
			$blurb = $this->input->post('blurb');
			$album_id = $this->input->post('album_id');
			if($list_id && $album_id)
			{
				$this->list_model->edit_album_blurb($list_id, $album_id, $blurb);
				echo $this->_editable_albums($list_id);
			}
			else
			{
				echo SESSION_EXPIRED;
			}
		}
	}
	
	private function _editable_albums($list_id)
	{
		$albums = $this->album_model->albums_by_list($list_id, 50, 0);
		return $this->load->view('list_of_albums', array('albums' => $albums, 'can_remove' => TRUE, 
			'can_reorder' => $list_id, 'pageable' => FALSE, 'sortable' => FALSE), TRUE);
	}
	
	public function add_comment()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$text = $this->input->post('text');
			$list_id = $this->input->post('list_id');
			if($text && $list_id)
			{
				$this->comment_model->insert_list_comment($text, $user_id, $list_id);
				echo $this->_comments($list_id, 0);
			}
			else
			{
				echo SESSION_EXPIRED;
			}
		}
	}
	
	public function remove_comment()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$comment_id = $this->input->post('comment_id');
			$list_id = $this->input->post('list_id');
			if($comment_id && $list_id)
			{
				$this->comment_model->delete_comment($comment_id);
				echo $this->_comments($list_id, 0);
			}
			else
			{
				echo SESSION_EXPIRED;
			}
		}
	}
	
	public function refresh_related_lists()
	{
		$offset = $this->input->post('offset');
		$list_id = $this->input->post('list_id');
		if($list_id)
		{
			echo $this->_related_lists($list_id, $offset);
		}
	}
	
	private function _related_lists($list_id, $offset)
	{
		$lists = $this->list_model->related_lists($list_id, PAGED_LIST_SIZE, intval($offset));
		$count = $this->list_model->count_related_lists($list_id);
		return $this->load->view('list_of_lists', array('lists' => $lists, 'pageable' => pageable($count), 
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('lists/refresh_related_lists'),
			'post_values' => "'list_id':".$list_id, 'sortable' => FALSE), TRUE); 
	}
	
	public function refresh_comments()
	{
		$offset = $this->input->post('offset');
		$list_id = $this->input->post('list_id');
		if($list_id)
		{
			echo $this->_comments($list_id, $offset);
		}
	}
	
	private function _comments($list_id, $offset)
	{
		$list = $this->list_model->get_list($list_id);
		$user_id = $this->session->userdata('user_id');
		$comments = $this->comment_model->list_comments($list_id, PAGED_LIST_SIZE, $offset);
		$count = $this->comment_model->count_list_comments($list_id);
		return $this->load->view('list_of_comments', array('comments' => $comments, 'can_remove' => 
			$user_id == $list->user_id, 'user_id' => $user_id, 'pageable' => pageable($count), 
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('lists/refresh_comments'),
			'post_values' => "'list_id':".$list_id), TRUE);
	}
	
	public function refresh_lists_user()
	{
		$offset = $this->input->post('offset');
		$user_id = $this->input->post('user_id');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($user_id)
		{
			echo $this->_lists_user($user_id, $offset, $order_by, $direction);
		}
	}
	
	private function _lists_user($user_id, $offset, $order_by, $direction)
	{
		$user_lists = $this->list_model->all_lists_by_user($user_id, PAGED_LIST_SIZE, $offset, $order_by,
			$direction);
		$count = $this->list_model->count_all_lists_by_user($user_id);
		return $this->load->view('list_of_lists', array('lists' => $user_lists,
			'pageable' => pageable($count), 'count' => $count, 'offset' => $offset, 'post_url' => 
			site_url('lists/refresh_lists_user'), 'post_values' => "'user_id':".$user_id,
			'sortable' => TRUE, 'order_by' => $order_by, 'direction' => $direction), TRUE);
	}
	
	public function delete($list_id)
	{
		$user_id = $this->_check_user();
		$list = $this->list_model->get_list($list_id);
		if($list->user_id != $user_id)
		{
			redirect('lists/roll/'.$list_id);
		}
		$this->list_model->delete_list($list_id);
		redirect('lists/user/'.$user_id);
	}
}
?>