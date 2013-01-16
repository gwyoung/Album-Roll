<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Users extends MY_Controller
{
	function Users()
	{
		parent::MY_Controller();
	}
	
	public function profile($id)
	{
		$data['view'] = 'profile';
		$user = $this->user_model->get_user($id);
		if(!$user)
		{
			redirect('error/not_found');
		}
		$data['user'] = $user;
		$is_current_user = $this->session->userdata('user_id') == $id;
		$data['is_current_user'] = $is_current_user;
		$data['links'] = $this->_format_links($id, $data['is_current_user']);
		$data['favorites_id'] = $this->user_model->favorites_list($id);
		$data['favorites'] = $this->_favorites($id, 0);
		$data['current_rotation_id'] = $this->user_model->current_rotation_list($id);
		$data['current_rotation'] = $this->_current_rotation($id, 0);
		$data['user_lists'] = $this->_lists($id, 0, 'date added', 'desc');
		
		$this->_load($data);
	}
	
	private function _format_links($user_id, $is_current_user)
	{
		$links = $this->user_model->links($user_id);
		if(empty($links))
		{
			return 'No links have been added yet.';
		}
		
		$html = '';
		foreach($links as $link)
		{
			$html = $html.'<a href='.$link->url.'>'.$link->name.'</a>';
			if($is_current_user)
			{
				$html = $html.'<img id="remove_link_button" src="'.base_url().'images/x_icon.jpg" class="x_icon" alt="'.$link->url.'" />';
			}
			if($link != end($links))
			{
				$html = $html.'&nbsp<wbr/>&nbsp<wbr/>&nbsp<wbr/>&nbsp<wbr/>';
			}
		}
		
		return $html;
	}
	
	public function signup()
	{
		if($this->session->userdata('user_id'))
		{
			redirect('/home/welcome');
		}
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('email', 'email', 
			'trim|required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('signup_name', 'name', 
			'trim|required|min_length[3]|max_length[25]|xss_clean|is_unique[users.name]');
		$this->form_validation->set_rules('signup_password', 'password', 
			'required|min_length[6]|matches[confirm_password]');
		$this->form_validation->set_rules('confirm_password', 'confirm password', 'required');
		$this->form_validation->set_rules('blurb', '\'about you\'', 'required|max_length[150]');
		
		if($this->form_validation->run() == FALSE)
		{
			$data['blurb_value'] = set_value('blurb');
			if(empty($data['blurb_value']))
			{
				$data['blurb_value'] = "Just your average music-lover.";
			}
			
			$data['view'] = 'signup';
			
			$this->_load($data);
		}
		else
		{
			$this->user_model->add_user(
				$this->input->post('email'),
				$this->input->post('signup_name'),
				$this->input->post('signup_password'),
				$this->input->post('blurb'),
				'');
			$user = $this->user_model->signin($this->input->post('signup_name'), $this->input->post('signup_password'));
			$this->session->set_userdata(array('user_id' => $user->user_id));
			setcookie('user_id', $user->user_id, time()+60*60*24*7);
			redirect('users/getting_started');
		}
	}
	
	public function signin()
	{
		$user = $this->user_model->signin($this->input->post('name'), $this->input->post('password'));
		if($user)
		{
			$this->session->set_userdata(array('user_id' => $user->user_id));
			if($this->input->post('remember_me'))
			{
				setcookie('user_id', $user->user_id, time()+60*60*24*7, '/');
			}
			echo 'success';
		}
	}
	
	public function signout()
	{
		$this->session->unset_userdata('user_id');
		delete_cookie('user_id');
		redirect($this->input->post('current_url'));
	}
	
	public function getting_started()
	{
		$user_id = $this->_check_user();
		$data['user_id'] = $user_id;
		$user = $this->user_model->get_user($user_id);
		$data['name'] = $user->name;
		$data['favorites_id'] = $this->user_model->favorites_list($user_id);
		$data['current_rotation_id'] = $this->user_model->current_rotation_list($user_id);
		$data['view'] = 'getting_started';
		$this->_load($data);
	}
	
	public function add_link()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$link_title = $this->input->post('link_title');
			$link_url = $this->input->post('link_url');
			
			if($link_title && $link_url)
			{
				$link_url = rtrim($link_url, '/');
				if(Link::valid_url($link_url))
				{
					$this->user_model->insert_link($user_id, new Link($link_title, $link_url));
					
					echo $this->_format_links($user_id, TRUE);
				}
				else
				{
					echo '0'; //invalid url
				}
			}
		}
	}
	
	public function remove_link()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$link_url = $this->input->post('link_url');
			$this->user_model->remove_link($user_id, $link_url);
			
			echo $this->_format_links($user_id, TRUE);
		}
	}
	
	public function edit_blurb()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$blurb = $this->input->post('blurb');
			if($blurb)
			{
				$this->user_model->update_user_blurb($user_id, $blurb);
				echo $blurb;
			}
		}
	}
	
	public function options()
	{
		$this->_check_user();
		$data['errors'] = FALSE;
		$data['view'] = 'options';
		$this->_load($data);
	}
	
	public function change_picture()
	{
		$user_id = $this->_check_user();
		$user = $this->user_model->get_user($user_id);
		if(!$user)
		{
			redirect('home/welcome');
			return;
		}
		
		$image_width = 0;
		$image_height = 0;
		$full_path = '';
		$filename = '';
		
		$image_url = $this->input->post('image_url');
		
		if($image_url)
		{
			if ((list($width, $height, $type, $attr) = @getimagesize($image_url)) === false) {
				$data['errors'] = '<div id="upload_error" class="error_text">Invalid image URL.</div>';
				$data['view'] = 'options';
				$this->_load($data);
				return;
    		}
			
			$orig_filename = str_replace(' ', '_', $user->name);
			$format = '.'.end(explode('.', $image_url));
			$filename = $orig_filename.$format;
			$i = 1;
			while(file_exists('./images/users/'.$filename))
			{
				$filename = $orig_filename.$i.$format;
				$i++;
			}
			
			$result = @file_put_contents('./images/users/'.$filename, file_get_contents($image_url));
			
			if(!$result)
			{
				$data['errors'] = '<div id="upload_error" class="error_text">An error occurred while downloading from the image URL.</div>';
				$data['view'] = 'options';
				$this->_load($data);
				return;
			}
			
			$image_width = $width;
			$image_height = $height;
			$full_path = realpath('./images/users/'.$filename);
		}
		else
		{
			$config['upload_path'] = './images/users/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
	
			$this->load->library('upload', $config);
	
			if (!$this->upload->do_upload('user_photo'))
			{
				$data['errors'] = $this->upload->display_errors('<div id="upload_error" class="error_text">', '</div>');
				$data['view'] = 'options';
				$this->_load($data);
				return;
			}
			
			$upload_data = $this->upload->data();
			$full_path = $upload_data['full_path'];
			$filename = $upload_data['file_name'];
			$image_height = $upload_data['image_height'];
			$image_width = $upload_data['image_width'];
		}
		
		if($user->image_file != DEFAULT_IMAGE_URL)
		{
			unlink(User::image_path($user->image_file));
		}
		
		$this->user_model->update_user_image($user_id, $filename);
		
		$ratio = $image_width / $image_height;
		if($ratio >= .98 && $ratio <= 1.02)
		{
			redirect('users/profile/'.$user_id);
		}
		else
		{
			$upload_path = base_url().'images/users/'.$filename;
			$this->session->set_userdata('file_path', $full_path);
			$this->session->set_userdata('upload_path', $upload_path);
			$this->session->set_userdata('actual_width', $image_width);
			redirect('users/crop_picture');
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
			$data['destination'] = 'users/do_crop';
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
				$data['destination'] = 'users/do_crop';
				$this->_load($data);
	        } 
			else 
			{
				$this->session->unset_userdata('file_path');
				$this->session->unset_userdata('upload_path');
				$this->session->unset_userdata('actual_width');
				redirect('users/profile/'.$user_id);
	        }
		}
		else
		{
			redirect('home/welcome');
		}
	}
	
	public function change_name()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('new_name', 'name', 
				'trim|required|min_length[3]|max_length[25]|xss_clean|is_unique[users.name]');
				
			if($this->form_validation->run() == FALSE)
			{
				$user = $this->user_model->get_user($user_id);
				if($user->name != $this->input->post('new_name'))
				{
					echo form_error('new_name', '<div id="change_name_error" class="error_text">', 
						'</div>');
				}
			}
			else
			{
				$this->user_model->update_user_name($user_id, $this->input->post('new_name'));
			}
		}
	}
	
	public function change_password()
	{
		$user_id = $this->_check_user_ajax();
		if($user_id)
		{
			$user = $this->user_model->get_user($user_id);
			
			if($this->user_model->correct_password($user->name, $this->input->post('old_password')))
			{
				$this->load->library('form_validation');
				
				$this->form_validation->set_rules('old_password', 'old password', 'required');
				$this->form_validation->set_rules('new_password', 'new password', 
					'required|min_length[6]|matches[confirm_password]');
				$this->form_validation->set_rules('confirm_password', 'confirm password', 'required');
			
				if($this->form_validation->run() == FALSE)
				{
					echo validation_errors('<div id="change_password_error" class="error_text">', '</div>');
				}
				else
				{
					$this->user_model->update_user_password($user_id, $this->input->post('new_password'));
					echo '<div id="change_password_error" class="success_text">Success!</div>';
				}
			}
			else
			{
				echo '<div id="change_password_error" class="error_text">The old password is incorrect.</div>';
			}
		}
	}
	
	public function delete()
	{
		$user_id = $this->_check_user();
		$this->session->unset_userdata('user_id');
		delete_cookie('user_id');
		$this->user_model->delete_user($user_id);
		redirect('home/welcome');
	}
	
	public function refresh_current_rotation()
	{
		$offset = $this->input->post('offset');
		$user_id = $this->input->post('user_id');
		if($user_id)
		{
			echo $this->_current_rotation($user_id, $offset);
		}
	}
	
	private function _current_rotation($profile_id, $offset)
	{
		$current_rotation_id = $this->user_model->current_rotation_list($profile_id);
		$current_rotation = $this->album_model->albums_by_list($current_rotation_id, PAGED_LIST_SIZE, 
			$offset);
		$count = $this->album_model->count_albums_by_list($current_rotation_id);
		return $this->load->view('horizontal_albums', array('albums' => $current_rotation, 
			'count' => $count, 'offset' => $offset, 'post_url' => 
			site_url('users/refresh_current_rotation'), 'post_values' => "'user_id':".$profile_id,
			'sortable' => FALSE), TRUE);
	}
	
	public function refresh_favorites()
	{
		$offset = $this->input->post('offset');
		$user_id = $this->input->post('user_id');
		if($user_id)
		{
			echo $this->_favorites($user_id, $offset);
		}
	}
	
	private function _favorites($profile_id, $offset)
	{
		$favorites_id = $this->user_model->favorites_list($profile_id);
		$favorites = $this->album_model->albums_by_list($favorites_id, PAGED_LIST_SIZE, $offset);
		$count = $this->album_model->count_albums_by_list($favorites_id);
		return $this->load->view('list_of_albums', array('albums' => $favorites, 
			'can_remove' => FALSE, 'can_reorder' => FALSE, 'pageable' => pageable($count), 
			'count' => $count, 'offset' => $offset, 'post_url' => site_url('users/refresh_favorites'),
			'post_values' => "'user_id':".$profile_id, 'sortable' => FALSE), TRUE);
	}
	
	public function refresh_lists()
	{
		$offset = $this->input->post('offset');
		$user_id = $this->input->post('user_id');
		$order_by = $this->input->post('order_by');
		$direction = $this->input->post('direction');
		if($user_id)
		{
			echo $this->_lists($user_id, $offset, $order_by, $direction);
		}
	}
	
	private function _lists($profile_id, $offset, $order_by, $direction)
	{
		$user_lists = $this->list_model->lists_by_user($profile_id, PAGED_LIST_SIZE, $offset, $order_by,
			$direction);
		$count = $this->list_model->count_lists_by_user($profile_id);
		return $this->load->view('list_of_lists', array('lists' => $user_lists,
			'pageable' => pageable($count), 'count' => $count, 'offset' => $offset, 'post_url' => 
			site_url('users/refresh_lists'), 'post_values' => "'user_id':".$profile_id, 
			'sortable' => TRUE, 'direction' => $direction, 'order_by' => $order_by), TRUE);
	}
}
?>