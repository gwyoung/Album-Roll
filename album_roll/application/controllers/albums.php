<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('LASTFM_KEY', '3e13ff2ecdcd96cc13b46e700cbd2f28');
define('LASTFM_SECRET', '49e47185464d8784cf851eee693686bc');
class Albums extends MY_Controller
{
	function Albums()
	{
		parent::MY_Controller();
	}
	
	public function album($id)
	{
		$album = $this->album_model->get_album($id);
		if(!$album)
		{
			redirect('error/not_found');
		}
		$data['album'] = $album;
		$data['logged_in'] = $this->session->userdata('user_id') != FALSE;
		$data['information_links'] = $this->_format_links($this->album_model->information_links($id));
		$data['reviews'] = $this->_format_links($this->album_model->review_links($id));
		$data['streams'] = $this->_format_links($this->album_model->stream_links($id));
		$data['tags'] = $this->_format_tags($id);
		$data['average_rating'] = round($this->album_model->average_rating($id), 2);
		$data['total_votes'] = $this->album_model->rating_count($id);
		
		$data['lists'] = $this->_lists($id, 0, 'date added', 'desc');
		$data['comments'] = $this->_comments($id, 0);
		$data['related_albums'] = $this->_related_albums($id, 0);
		
		$data['view'] = 'album';
		$this->_load($data);
	}
	
	private function _format_links($links)
	{
		if(empty($links))
		{
			return 'No links have been added yet.';
		}
		
		$html = '';
		foreach($links as $link)
		{
			$html = $html.'<a href='.$link->url.'>'.$link->name.'</a>';
			if($link != end($links))
			{
				$html = $html.'&nbsp<wbr/>&nbsp<wbr/>&nbsp<wbr/>&nbsp<wbr/>';
			}
		}
		
		return $html;
	}
	
	private function _format_tags($album_id)
	{
		$tags = $this->album_model->tags($album_id);
		if(empty($tags))
		{
			return 'No tags have been added yet.';
		}
		
		$html = '';
		foreach($tags as $tag)
		{
			$html = $html.anchor('search/tag/'.$tag->tag_id, $tag->name);
			if($tag != end($tags))
			{
				$html = $html.'&nbsp<wbr/>&nbsp<wbr/>&nbsp<wbr/>&nbsp<wbr/>';
			}
		}
		
		return $html;
	}
	
	public function add_tag()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$tag_name = $this->input->post('tag_name');
			if($tag_name)
			{
				$album_id = $this->input->post('album_id');
				$this->album_model->add_tag($album_id, $tag_name);
				
				echo $this->_format_tags($album_id);
			}
		}
	}
	
	private function _add_link($type)
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$link_title = $this->input->post('link_title');
			$link_url = $this->input->post('link_url');
			$album_id = $this->input->post('album_id');
			
			if($link_title && $link_url && $album_id)
			{
				$link_url = rtrim($link_url, '/');
				if(Link::valid_url($link_url))
				{
					switch($type)
					{
						case LinkType::Information:
							$this->album_model->insert_information_link($album_id, 
								new Link($link_title, $link_url));
							echo $this->_format_links($this->album_model->information_links($album_id));
							break;
						case LinkType::Review:
							$this->album_model->insert_review_link($album_id, 
								new Link($link_title, $link_url));
							echo $this->_format_links($this->album_model->review_links($album_id));
							break;
						case LinkType::Stream:
							$this->album_model->insert_stream_link($album_id, 
								new Link($link_title, $link_url));
							echo $this->_format_links($this->album_model->stream_links($album_id));
							break;
					}
				}
				else
				{
					echo '0'; //invalid url
				}
			}
		}
	}
	
	public function add_information_link()
	{
		$this->_add_link(LinkType::Information);
	}
	
	public function add_review_link()
	{
		$this->_add_link(LinkType::Review);
	}
	
	public function add_stream_link()
	{
		$this->_add_link(LinkType::Stream);
	}
	
	public function rate()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$rating = intval($this->input->post('rating'));
			$album_id = $this->input->post('album_id');
			$this->album_model->add_rating($album_id, $rating, $user_id);
			
			echo round($this->album_model->average_rating($album_id), 2);
			echo ':';
			echo $this->album_model->rating_count($album_id);
		}
	}
	
	public function create()
	{
		$user_id = $this->_check_user();
		
		$artist = $this->input->post('artist');
		$title = $this->input->post('title');
		$label = $this->input->post('label');
		$date = $this->input->post('date');
		$tags = preg_split( "/(,|;)/", $this->input->post('tags'));
		
		if($date)
		{
			list($month, $day, $year) = explode("/", $date);
			$date = mktime(0, 0, 0, $month, $day, $year);
		}
		
		$mysqldate = timestamp_to_mysqldatetime($date, TRUE);
		
		if(!$title)
		{
			$list_id = $this->input->post('list_id');
			if($list_id)
			{
				$this->session->set_userdata('list_id', $list_id);
				$data['title_error'] = '';
				$data['art_url_error'] = '';
				$data['errors'] = '';
				$data['failed'] = FALSE;
				$data['view'] = 'new_album';
				$this->_load($data);
			}
			else
			{
				redirect('home/welcome');
			}
		}
		else
		{
			$existing_id = $this->album_model->check_album($title, $artist);
			if($existing_id > 0)
			{
				foreach($tags as $tag)
				{
					$tag = trim($tag);
					if(!empty($tag))
					{
						$this->album_model->add_tag($existing_id, $tag);
					}
				}
				
				$list_id = $this->session->userdata('list_id');
				if($list_id)
				{
					$this->list_model->add_album($list_id, $existing_id, '');
					$this->session->unset_userdata('list_id');
					redirect('lists/roll/'.$list_id);
				}
				else
				{
					redirect('albums/album/'.$album_id);
				}
				return;
			}
			
			$image_width = 0;
			$image_height = 0;
			$full_path = '';
			$filename = '';
			
			$art_url = $this->input->post('art_url');
			
			if($art_url)
			{
				if ((list($width, $height, $type, $attr) = @getimagesize($art_url)) === false) {
					$data['title_error'] = '';
					$data['art_url_error'] = '<div id="art_error" class="error_text">Invalid cover art image URL.</div>';
					$data['errors'] = '';
					$data['failed'] = TRUE;
					$data['view'] = 'new_album';
					$this->_load($data);
					return;
        		}
				
				$orig_filename = preg_replace("/[^a-zA-Z0-9]/", '', $title);
				$format = '.'.end(explode('.', $art_url));
				$filename = $orig_filename.$format;
				$i = 1;
				while(file_exists('./images/albums/'.$filename))
				{
					$filename = $orig_filename.$i.$format;
					$i++;
				}
				
				$result = @file_put_contents('./images/albums/'.$filename, file_get_contents($art_url));
				
				if(!$result)
				{
					$data['title_error'] = '';
					$data['art_url_error'] = '<div id="art_error" class="error_text">An error occurred downloading from the image URL.</div>';
					$data['errors'] = '';
					$data['failed'] = TRUE;
					$data['view'] = 'new_album';
					$this->_load($data);
					return;
				}
				
				$image_width = $width;
				$image_height = $height;
				$full_path = realpath('./images/albums/'.$filename);
			}
			else
			{
				$config['upload_path'] = './images/albums/';
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
		
				$this->load->library('upload', $config);
				
				if (!$this->upload->do_upload('album_art'))
				{
					$data['title_error'] = '';
					$data['art_url_error'] = '';
					$data['errors'] = $this->upload->display_errors('<div id="art_error" class="error_text">', '</div>');
					$data['failed'] = TRUE;
					$data['view'] = 'new_album';
					$this->_load($data);
					return;
				}
				
				$upload_data = $this->upload->data();
				
				$image_width = $upload_data['image_width'];
				$image_height = $upload_data['image_height'];
				$full_path = $upload_data['full_path'];
				$filename = $upload_data['file_name'];
			}
			
			$user_id = $this->session->userdata('user_id');
			
			$album_id = $this->album_model->create_album($title, $artist, $mysqldate, $label, 
				$filename);
				
			foreach($tags as $tag)
			{
				$tag = trim($tag);
				if(!empty($tag))
				{
					$this->album_model->add_tag($album_id, $tag);
				}
			}
			
			$ratio = $image_width / $image_height;
			if($ratio >= .98 && $ratio <= 1.02)
			{
				$list_id = $this->session->userdata('list_id');
				if($list_id)
				{
					$this->list_model->add_album($list_id, $album_id, '');
					$this->session->unset_userdata('list_id');
					redirect('lists/roll/'.$list_id);
				}
				else
				{
					redirect('albums/album/'.$album_id);
				}
			}
			else
			{
				$upload_path = base_url().'images/albums/'.$filename;
				$this->session->set_userdata('file_path', $full_path);
				$this->session->set_userdata('upload_path', $upload_path);
				$this->session->set_userdata('actual_width', $image_width);
				$this->session->set_userdata('album_id', $album_id);
				redirect('albums/crop_picture');
			}
		}
	}
	
	public function crop_picture()
	{
		$user_id = $this->_check_user();
		$file_path = $this->session->userdata('file_path');
		if($file_path)
		{
			$data['errors'] = FALSE;
			$data['upload_path'] = $this->session->userdata('upload_path');
			$data['actual_width'] = $this->session->userdata('actual_width');
			$data['file_path'] = $file_path;
			$data['view'] = 'crop_picture';
			$data['destination'] = 'albums/do_crop';
			$this->_load($data);
		}
		else
		{
			redirect('home/welcome');
		}
	}
	
	public function do_crop()
	{
		$user_id = $this->_check_user();
		$file_path = $this->input->post('file_path');
		if($file_path)
		{
			$ratio = $this->input->post('actual_width') / 400; //ratio of actual to displayed
			
			$this->load->library('image_lib');
			
			$config['source_image'] = $this->input->post('file_path');
	        $config['x_axis'] = $this->input->post('x') * $ratio;
	        $config['y_axis'] = $this->input->post('y') * $ratio;
			$config['width'] = $this->input->post('w') * $ratio;
			$config['height'] = $this->input->post('h') * $ratio;
			$config['maintain_ratio'] = FALSE;
			
			$this->image_lib->initialize($config); 
	
	        if(!$this->image_lib->crop()) {
	            $data['errors'] = $this->image_lib->display_errors('<div class="error_text"></div>');
				$data['upload_path'] = $this->input->post('upload_path');
				$data['actual_width'] = $this->input->post('actual_width');
				$data['file_path'] = $file_path;
				$data['view'] = 'crop_picture';
				$data['destination'] = 'albums/do_crop';
				$this->_load($data);
	        } 
			else 
			{
				$this->session->unset_userdata('file_path');
				$this->session->unset_userdata('upload_path');
				$this->session->unset_userdata('actual_width');
				
				$list_id = $this->session->userdata('list_id');
				$album_id = $this->session->userdata('album_id');
				if($album_id)
				{
					if($list_id)
					{
						$this->list_model->add_album($list_id, $album_id, '');
						$this->session->unset_userdata('list_id');
						redirect('lists/roll/'.$list_id);
					}
					else
					{
						$this->session->unset_userdata('album_id');
						redirect('albums/album/'.$album_id);
					}
				}
				else
				{
					redirect('home/welcome');
				}
	        }
		}
		else
		{
			redirect('home/welcome');
		}
	}
	
	public function add_comment()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$text = $this->input->post('text');
			$album_id = $this->input->post('album_id');
			if($text && $album_id)
			{
				$this->comment_model->insert_album_comment($text, $user_id, $album_id);
				echo $this->_comments($album_id, 0);
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
			$album_id = $this->input->post('album_id');
			if($comment_id && $album_id)
			{
				$this->comment_model->delete_comment($comment_id);
				echo $this->_comments($album_id, 0);
			}
			else
			{
				echo SESSION_EXPIRED;
			}
		}
	}
	
	public function refresh_comments()
	{
		$offset = $this->input->post('offset');
		$album_id = $this->input->post('album_id');
		if($album_id)
		{
			echo $this->_comments($album_id, $offset);
		}
	}
	
	private function _comments($album_id, $offset)
	{
		$comments = $this->comment_model->album_comments($album_id, PAGED_LIST_SIZE, $offset);
		$count = $this->comment_model->count_album_comments($album_id);
		return $this->load->view('list_of_comments', array('comments' => $comments, 'can_remove' => 
			FALSE, 'user_id' => $this->session->userdata('user_id'), 'pageable' => pageable($count), 
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('albums/refresh_comments'),
			'post_values' => "'album_id':".$album_id), TRUE);
	}
	
	public function refresh_lists()
	{
		$offset = $this->input->post('offset');
		$album_id = $this->input->post('album_id');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($album_id)
		{
			echo $this->_lists($album_id, $offset, $order_by, $direction);
		}
	}
	
	private function _lists($album_id, $offset, $order_by, $direction)
	{
		$lists = $this->list_model->lists_by_album($album_id, PAGED_LIST_SIZE, $offset, $order_by,
			$direction);
		$count = $this->list_model->count_lists_by_album($album_id);
		return $this->load->view('list_of_lists', array('lists' => $lists, 'pageable' => pageable($count), 
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('albums/refresh_lists'),
			'post_values' => "'album_id':".$album_id, 'sortable' => TRUE, 'order_by' => $order_by,
			'direction' => $direction), TRUE); 
	}
	
	public function refresh_related_albums()
	{
		$offset = $this->input->post('offset');
		$album_id = $this->input->post('album_id');
		if($album_id)
		{
			echo $this->_related_albums($album_id, $offset);
		}
	}
	
	private function _related_albums($album_id, $offset)
	{
		$related_albums = $this->album_model->related_albums($album_id, PAGED_LIST_SIZE, intval($offset));
		$count = $this->album_model->count_related_albums($album_id);
		return $this->load->view('horizontal_albums', array('albums' => $related_albums, 
			'count' => $count, 'offset' => $offset, 'post_url' => 
			site_url('albums/refresh_related_albums'), 'post_values' => "'album_id':".$album_id), TRUE);
	}
}
?>