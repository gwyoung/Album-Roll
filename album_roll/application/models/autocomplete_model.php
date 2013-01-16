<?php
class Autocomplete_model extends CI_Model {

	public function __construct()
	{
	
	}
	
	private function prepare_term($term)
	{
		$term = strtolower($term);
		$prefix = 'the ';
		if (substr($term, 0, strlen($prefix) ) == $prefix) {
    		return substr($term, strlen($prefix), strlen($term) );
		}
		else
		{
			return $term;
		}
	}
	
	public function search_artists($term)
	{
		$term = $this->prepare_term($term);
		if(strlen($term) < 2)
		{
			return array();
		}
		 
		$this->db->like('name', $term, 'after');
		$this->db->or_like('name', 'The '.$term, 'after');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get('artists');
		
		$results = array();
		foreach($query->result() as $row)
		{
			$results[] = $row->name;
		}
		return $results;
	}
	
	public function search_labels($term)
	{
		if(strlen($term) < 2)
		{
			return array();
		}
		
		$this->db->like('name', $term, 'after');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get('labels');
		
		$results = array();
		foreach($query->result() as $row)
		{
			$results[] = $row->name;
		}
		return $results;
	}
	
	public function search_tags($term)
	{
		if(strlen($term) < 2)
		{
			return array();
		}
		
		$this->db->like('name', $term, 'after');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get('tags');
		
		$results = array();
		foreach($query->result() as $row)
		{
			$results[] = $row->name;
		}
		return $results;
	}
	
	private function album_query($term)
	{
		$this->db->select('albums.title, albums.album_id, artists.name');
		$this->db->from('albums');
		$this->db->join('artists', 'artists.artist_id = albums.artist_id');
		$this->db->like('albums.title', $term, 'after');
		$this->db->or_like('albums.title', 'The '.$term, 'after');
		$this->db->or_like('artists.name', $term, 'after');
		$this->db->or_like('artists.name', 'The '.$term, 'after');
		$this->db->order_by('artists.name asc, albums.title asc');
		return $this->db->get();
	}
	
	public function search_albums($term)
	{
		$term = $this->prepare_term($term);
		if(strlen($term) < 2)
		{
			return array();
		}
		
		$query = $this->album_query($term);
		
		$results = array();
		foreach($query->result() as $row)
		{
			$results[] = array('label' => $row->name.' - '.$row->title, 'value' => $row->album_id);
		}
		return $results;
	}
	
	public function search_all($term)
	{
		$term = $this->prepare_term($term);
		if(strlen($term) < 3)
		{
			return array();
		}
		
		$results = array();
		
		$this->db->like('name', $term, 'after');
		$this->db->or_like('name', 'The '.$term, 'after');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get('artists');
		foreach($query->result() as $row)
		{
			$results[] = array('label' => 'Artist: '.$row->name, 'value' => 
				site_url('search/artist/'.$row->artist_id));
		}
		
		$query = $this->album_query($term);
		foreach($query->result() as $row){
			$results[] = array('label' => 'Album: '.$row->name.' - '.$row->title, 'value' => 
				site_url('albums/album/'.$row->album_id));
		}
		
		$this->db->like('name', $term, 'after');
		$this->db->or_like('email', $term, 'after');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get('users');
		foreach($query->result() as $row)
		{
			$results[] = array('label' => 'User: '.$row->name, 'value' =>
				site_url('users/profile/'.$row->user_id));
		}
		
		$this->db->like('title', $term);
		$this->db->or_like('year', $term, 'after');
		$this->db->where('type !=', ListType::HeardIt);
		$this->db->order_by('title', 'asc');
		$query = $this->db->get('lists');
		foreach($query->result() as $row)
		{
			$results[] = array('label' => 'List: '.$row->title, 'value' =>
				site_url('lists/roll/'.$row->list_id));
		}
		
		$this->db->like('name', $term, 'after');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get('tags');
		foreach($query->result() as $row)
		{
			$results[] = array('label' => 'Tag: '.$row->name, 'value' => 
				site_url('search/tag/'.$row->tag_id));
		}
		
		$this->db->like('name', $term, 'after');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get('labels');
		foreach($query->result() as $row)
		{
			$results[] = array('label' => 'Label: '.$row->name, 'value' => 
				site_url('search/label/'.$row->label_id));
		}
		
		return $results;
	}
}
?>