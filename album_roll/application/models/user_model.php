<?php
define ("DEFAULT_IMAGE_URL", 'default_user.jpg');
class User_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		include_once APPPATH.'models/User.php';
	}
	
	
	/*SIGN UP AND LOG IN*/
	
	private function hash_password($password)
	{
		return sha1($password.$this->config->item('encryption_key'));
	}
	
	public function add_user($email, $name, $password, $blurb, $image_url)
	{
		//check if user exists
		$query = $this->db->get_where('users', array('email' => $email));
		if($query->num_rows() > 0)
		{
			return -1; //user email already exists
		}
		$query = $this->db->get_where('users', array('name' => $name));
		if($query->num_rows() > 0)
		{
			return -2; //username already exists
		}
		else
		{
			if(empty($image_url))
			{
				$image_url = DEFAULT_IMAGE_URL;
			}
			$this->load->helper('date');
			$this->db->insert('users', array(
				'email' => $email,
				'password' => $this->hash_password($password),
				'name' => $name,
				'blurb' => $blurb,
				'member_since' => timestamp_to_mysqldatetime(),
				'last_seen' => timestamp_to_mysqldatetime(),
				'image_url' => $image_url));
				
			$query = $this->db->get_where('users', array('email' => $email));
			$id = $query->row()->user_id;
			
			$ci =& get_instance();
			$ci->load->model('List_model');
			$ci->list_model->create_list(
				$name."'s Favorites",
				'',
				ListType::Favorites,
				0,
				$id);
			$ci->list_model->create_list(
				$name."'s Current Rotation",
				'',
				ListType::CurrentRotation,
				0,
				$id);
			$ci->list_model->create_list(
				$name."'s Heard It",
				'',
				ListType::HeardIt,
				0,
				$id);
				
			return $id; //success
		}
	}
	
	public function get_user($id)
	{
		$query = $this->db->get_where('users', array('user_id' => $id));
		if($query->num_rows() < 1)
		{
			return false;
		}
		return new User(
			$id,
			$query->row()->name,
			mysqldatetime_to_timestamp($query->row()->member_since),
			$query->row()->blurb,
			mysqldatetime_to_timestamp($query->row()->last_seen),
			$query->row()->image_url,
			$query->row()->email);
	}
	
	public function count_users()
	{
		return $this->db->count_all_results('users');
	}
	
	private function update_user($id, $property, $value)
	{
		//check that user exists
		$query = $this->db->get_where('users', array('user_id' => $id));
		if($query->num_rows() < 0)
		{
			return -1; //user doesn't exist
		}
		else
		{
			$this->db->where('user_id', $id);
			$this->db->update('users', array($property => $value));
			return $id;
		}
	}
	
	public function update_user_password($id, $password)
	{
		$this->update_user($id, 'password', $this->hash_password($password));
	}
	
	public function update_user_name($id, $name)
	{
		$query = $this->db->get_where('users', array('name' => $name));
		if($query->num_rows() > 0)
		{
			return -2; //username already exists
		}
		
		$success = $this->update_user($id, 'name', $name);
		
		if($success >= 0)
		{
			$ci =& get_instance();
			$ci->load->model('List_model');
			$ci->list_model->update_list_title
				($this->favorites_list($id), $id, $name."'s Favorites");
			$ci->list_model->update_list_title
				($this->current_rotation_list($id), $id, $name."'s Current Rotation");
			$ci->list_model->update_list_title
				($this->heard_it_list($id), $id, $name."'s Heard It");
			return 0;
		}
		else
		{
			return $success;
		}
	}
	
	public function update_user_image($id, $image_url)
	{
		$this->update_user($id, 'image_url', $image_url);
	}
	
	public function update_user_blurb($id, $blurb)
	{
		$this->update_user($id, 'blurb', $blurb);
	}
	
	public function update_user_last_seen($id)
	{
		$this->update_user($id, 'last_seen', timestamp_to_mysqldatetime());
	}
	
	public function delete_user($id)
	{
		//delete album ratings
		$this->db->where('user_id', $id);
		$this->db->delete('album_ratings');
		
		//delete list ratings
		$this->db->where('user_id', $id);
		$this->db->delete('list_ratings');
		
		//delete comments
		$ci =& get_instance();
		$ci->load->model('Comment_model');
		
		$query = $this->db->get_where('comments', array('user_id' => $id));
		$results = $query->result();
		foreach($results as $row)
		{
			$ci->comment_model->delete_comment($row->comment_id);
		}
		
		//delete all lists (using list model)
		$ci =& get_instance();
		$ci->load->model('List_model');
		
		$query = $this->db->get_where('lists', array('user_id' => $id));
		$results = $query->result();
		foreach($results as $row)
		{
			$ci->list_model->delete_list($row->list_id);
		}
		
		//delete links
		$this->db->where('user_id', $id);
		$this->db->delete('user_links');
		
		//delete user
		$this->db->where('user_id', $id);
		$this->db->delete('users');
	}
	
	public function name_exists($name)
	{
		$query = $this->db->get_where('users', array('name' => $name));
		return $query->num_rows() > 0;
	}
	
	public function email_exists($email)
	{
		$query = $this->db->get_where('users', array('email' => $email));
		return $query->num_rows() > 0;
	}
	
	public function name_from_email($email)
	{
		$query = $this->db->get_where('users', array('email' => $email));
		return $query->row()->name;
	}
	
	public function correct_password($name, $password)
	{
		$query = $this->db->get_where('users', array('name' => $name));
		if($query->num_rows() == 0)
		{
			return FALSE;
		}
		return $query->row()->password == $this->hash_password($password);
	}
	
	public function signin($name, $password)
	{
		if(!$this->name_exists($name) || !$this->correct_password($name, $password))
		{
			return FALSE;
		}
		$query = $this->db->get_where('users', array('name' => $name));
		$this->update_user_last_seen($query->row()->user_id);
		return $this->get_user($query->row()->user_id);
	}
	
	public function reset_password($email)
	{
		$query = $this->db->get_where('users', array('email' => $email));
		if($query->num_rows() > 0)
		{
			$password = $this->random_string(8);
			$this->update_user_password($query->row(0)->user_id, $password);
			return $password;
		}
	}
	
	private function random_string($length) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		
		$str = '';	
	
		$size = strlen($chars);
		for($i = 0; $i < $length; $i++) {
			$str .= $chars[rand(0, $size - 1)];
		}
	
		return $str;
	}
	
	
	/*RETRIEVE/UPDATE USER LISTS */
	
	//returns a list of links filtered by a link type
	public function links($id)
	{
		$query = $this->db->get_where('user_links', array('user_id' => $id));
		$results = array();
		foreach($query->result() as $row){
			$results[] = new Link(
				$row->name,
				$row->url);
		}
		return $results;
	}
	
	//inserts a link
	public function insert_link($id, $link)
	{
		$query = $this->db->get_where('user_links', 
			array('user_id' => $id, 'url' => $link->url));
		if($query->num_rows()>0)
		{
			$this->db->where(array('user_id' => $id, 'url' => $link->url));
			$this->db->update('user_links', array('name' => $link->name));
		}
		else
		{
			$this->db->insert('user_links', 
				array('user_id' => $id, 'name' => $link->name, 'url' => $link->url));
		}
	}
	
	public function remove_link($id, $url)
	{
		$query = $this->db->get_where('user_links', 
			array('user_id' => $id, 'url' => $url));
		if($query->num_rows()==0)
		{
			return -1; //link doesn't exist
		}
		else
		{
			$this->db->delete('user_links', array('user_id' => $id, 'url' => $url));
			return 0; //success
		}
	}
	
	public function favorites_list($id)
	{
		return $this->db->get_where('lists', 
			array('user_id' => $id, 'type' => ListType::Favorites))->row()->list_id;
	}
	
	public function current_rotation_list($id)
	{
		return $this->db->get_where('lists', 
			array('user_id' => $id, 'type' => ListType::CurrentRotation))->row()->list_id;
	}
	
	public function heard_it_list($id)
	{
		return $this->db->get_where('lists', 
			array('user_id' => $id, 'type' => ListType::HeardIt))->row()->list_id;
	}
	
	public function random()
	{
		$this->db->order_by('user_id', 'random');
		$query = $this->db->get('users');
		if($query->num_rows() > 0)
		{
			return $query->row()->user_id;
		}
		else
		{
			return 0;
		}
	}
}
?>