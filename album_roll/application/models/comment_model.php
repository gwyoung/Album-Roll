<?php
class Comment_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		include_once APPPATH.'models/Comment.php';
	}
	
	
	private function build_comments($rows)
	{
		$ci =& get_instance();
		$ci->load->model('User_model');
		
		$results = array();
		foreach($rows as $row)
		{
			$results[] = new Comment(
				$row->comment_id,
				$ci->user_model->get_user($row->user_id),
				mysqldatetime_to_timestamp($row->timestamp),
				$row->content);
		}
		return $results;
	}
	
	private function prepare_comments($id, $table)
	{
		$this->db->select('comments.comment_id, comments.timestamp, comments.content, comments.user_id');
		$this->db->from($table.'_comments');
		$this->db->join('comments', 'comments.comment_id = '.$table."_comments.comment_id");
		$this->db->order_by('comments.timestamp', 'desc');
		$this->db->where($table.'_comments.'.$table.'_id', $id);
	}
	
	private function comments($id, $table, $limit, $offset)
	{
		$this->prepare_comments($id, $table);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $this->build_comments($query->result());
	}
	
	private function count_comments($id, $table)
	{
		$this->prepare_comments($id, $table);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	public function album_comments($id, $limit, $offset)
	{
		return $this->comments($id, 'album', $limit, $offset);
	}
	
	public function count_album_comments($id)
	{
		return $this->count_comments($id, 'album');
	}
	
	public function list_comments($id, $limit, $offset)
	{
		return $this->comments($id, 'list', $limit, $offset);
	}
	
	public function count_list_comments($id)
	{
		return $this->count_comments($id, 'list');
	}
	
	private function insert_comment($text, $user_id, $id, $table)
	{
		$this->db->insert('comments', array(
			'user_id' => $user_id,
			'content' => $text,
			'timestamp' => timestamp_to_mysqldatetime()));
			
		$this->db->where('user_id', $user_id);
		$this->db->order_by('timestamp', 'desc');
		$cid = $this->db->get('comments')->row()->comment_id;
		
		$this->db->insert($table.'_comments', array(
			'comment_id' => $cid,
			$table.'_id' => $id));
	}
	
	public function insert_album_comment($text, $user_id, $id)
	{
		$this->insert_comment($text, $user_id, $id, 'album');
	}
	
	public function insert_list_comment($text, $user_id, $id)
	{
		$this->insert_comment($text, $user_id, $id, 'list');
	}
	
	public function delete_comment($id)
	{
		//delete album comments
		$this->db->where('comment_id', $id);
		$this->db->delete('album_comments');
		
		//delete list comments
		$this->db->where('comment_id', $id);
		$this->db->delete('list_comments');
		
		//delete comment
		$this->db->where('comment_id', $id);
		$this->db->delete('comments');
	}
}
?>