<?php
define("MAX_LIST_ALBUMS", 50);
define("LIST_COLUMNS", 'lists.list_id, lists.title, lists.blurb, lists.type, lists.created_time, lists.user_id, lists.view_count, lists.year');
class List_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		include_once APPPATH.'models/AlbumList.php';
		include_once APPPATH.'models/ListType.php';
	}
	
	
	/*LIST OPERATIONS*/
	
	public function create_list($title, $blurb, $type, $year, $user_id)
	{
		$query = $this->db->get_where('lists', array('title' => $title, 'user_id' => $user_id));
		if($query->num_rows() > 0)
		{
			return -1; //list with this name already exists
		}
		else
		{
			$this->db->insert('lists', array(
				'title' => $title,
				'blurb' => $blurb,
				'type' => $type,
				'year' => $year,
				'created_time' => timestamp_to_mysqldatetime(),
				'view_count' => 0,
				'user_id' => $user_id));
			$query = $this->db->get_where('lists', array('title' => $title, 'user_id' => $user_id));
			return $query->row(0)->list_id; //return id
		}
	}
	
	private function update_list($id, $user_id, $property, $value)
	{
		//check that list exists
		$query = $this->db->get_where('lists', array('list_id' => $id));
		if($query->num_rows() < 0)
		{
			return -1; //list doesn't exist
		}
		else
		{
			if($property == 'title')
			{
				$query = $this->db->get_where('lists', array('title' => $value, 'user_id' => $user_id));
				if($query->num_rows() > 0 && $query->row(0)->list_id != $id)
				{
					return -2; //other list has the new title already
				}
			}
			$this->db->where('list_id', $id);
			$this->db->update('lists', array($property => $value));
			return $id;
		}
	}
	
	public function update_list_title($id, $user_id, $title)
	{
		return $this->update_list($id, $user_id, 'title', $title);
	}
	
	public function update_list_blurb($id, $user_id, $blurb)
	{
		return $this->update_list($id, $user_id, 'blurb', $blurb);
	}
	
	public function update_list_year($id, $user_id, $year)
	{
		return $this->update_list($id, $user_id, 'year', $year);
	}
	
	public function update_list_view_count($id, $user_id)
	{
		$query = $this->db->get_where('lists', array('list_id' => $id));
		if($query->num_rows() > 0)
		{
			return $this->update_list($id, $user_id, 'view_count', $query->row(0)->view_count + 1);
		}
	}
	
	public function delete_list($id)
	{
		//delete list_albums
		$this->db->where('list_id', $id);
		$this->db->delete('list_albums');
		
		//delete list_tags
		$this->db->where('list_id', $id);
		$this->db->delete('list_tags');
		
		//delete list_comments
		$query = $this->db->get_where('list_comments', array('list_id' => $id));
		$ci =& get_instance();
		foreach($query->result() as $row)
		{
			$ci->comment_model->delete_comment($row->comment_id);
		}
		
		//delete list_ratings
		$this->db->where('list_id', $id);
		$this->db->delete('list_ratings');
		
		//delete list
		$this->db->where('list_id', $id);
		$this->db->delete('lists');
	}
	
	
	/*RETRIEVE MULTIPLE LISTS*/
	
	private function build_lists($rows)
	{
		$results = array();
		foreach($rows as $row)
		{
			$results[] = new AlbumList(
				$row->list_id,
				$row->title,
				$row->blurb,
				$row->type,
				$row->year,
				mysqldatetime_to_timestamp($row->created_time),
				$row->view_count,
				$row->user_id,
				$this->image_urls($row->list_id),
				$this->average_rating($row->list_id),
				$this->rating_count($row->list_id));
		}
		return $results;
	}
	
	private function image_urls($id)
	{
		$this->db->select('albums.image_url');
		$this->db->from('albums');
		$this->db->join('list_albums', 'list_albums.album_id = albums.album_id');
		$this->db->order_by('list_albums.ordinal');
		$this->db->where('list_albums.list_id', $id);
		$this->db->limit(4);
		$query = $this->db->get();
		
		$image_urls = array();
		$image_urls[0] = '';
		$image_urls[1] = '';
		$image_urls[2] = '';
		$image_urls[3] = '';
		for($i = 0; $i < $query->num_rows(); $i++)
		{
			$image_urls[$i] = $query->row($i)->image_url;
		}
		return $image_urls;
	}
	
	private function order_by($order_by, $direction)
	{
		$sort_options = AlbumList::sort_options();
		$order_by = $sort_options[$order_by];
		if($order_by == 'rating')
		{
			$this->db->from('lists');
			$this->db->join('list_ratings', 'list_ratings.list_id = lists.list_id', 'left outer');
			$this->db->group_by('lists.list_id');
			$this->db->order_by('AVG(list_ratings.rating) '.$direction.', COUNT(list_ratings.rating) '
				.$direction.', lists.list_id DESC');
		}
		else
		{
			$this->db->order_by($order_by.' '.$direction.', list_id DESC');
			$this->db->from('lists');
		}
	}
	
	public function get_list($id)
	{
		$query = $this->db->get_where('lists', array('list_id' => $id));
		if($query->num_rows() < 1)
		{
			return false;
		}
		$lists = $this->build_lists($query->result());
		return $lists[0];
	}
	
	public function lists_by_user($id, $limit, $offset, $order_by, $direction)
	{
		$this->order_by($order_by, $direction);
		$this->db->where("lists.user_id = ".$id." AND type !=".ListType::HeardIt." AND type !=".
			ListType::Favorites." AND type !=".ListType::CurrentRotation);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $this->build_lists($query->result());
	}
	
	public function count_lists_by_user($id)
	{
		$this->db->where("user_id = ".$id." AND type !=".ListType::HeardIt." AND type !=".
			ListType::Favorites." AND type !=".ListType::CurrentRotation);
		$query = $this->db->get('lists');
		return $query->num_rows();
	}
	
	public function all_lists_by_user($id, $limit, $offset, $order_by, $direction)
	{
		$this->order_by($order_by, $direction);
		$this->db->where("lists.user_id = ".$id." AND type !=".ListType::HeardIt);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $this->build_lists($query->result());
	}
	
	public function count_all_lists_by_user($id)
	{
		$this->db->where("user_id = ".$id." AND type !=".ListType::HeardIt);
		$query = $this->db->get('lists');
		return $query->num_rows();
	}
	
	public function lists_by_year($year, $limit, $offset, $order_by, $direction)
	{
		$this->order_by($order_by, $direction);
		$this->db->where('year', $year);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $this->build_lists($query->result());
	}
	
	public function count_lists_by_year($year)
	{
		$query = $this->db->get_where('lists', array('year' => $year));
		return $query->num_rows();
	}
	
	public function lists_by_views($limit, $offset)
	{
		$this->db->order_by('view_count desc, list_id desc');
		$this->db->where('type != ', ListType::HeardIt);
		$this->db->limit($limit, $offset);
		
		$query = $this->db->get('lists');
		return $this->build_lists($query->result());
	}
	
	private function prepare_lists_by_album($album_id)
	{
		$this->db->select(LIST_COLUMNS);
		$this->db->join('list_albums', 'list_albums.list_id = lists.list_id');
		$this->db->where(array('list_albums.album_id' => $album_id, 'lists.type !=' => ListType::HeardIt));
	}
	
	public function lists_by_album($album_id, $limit, $offset, $order_by, $direction)
	{
		$this->prepare_lists_by_album($album_id);
		$this->order_by($order_by, $direction);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		return $this->build_lists($query->result());
	}
	
	public function count_lists_by_album($album_id)
	{
		$this->prepare_lists_by_album($album_id);
		$this->db->from('lists');
		$query = $this->db->get();
		
		return $query->num_rows();
	}
	
	private function prepare_lists_by_tag($tag_id)
	{
		$this->db->select(LIST_COLUMNS);
		$this->db->group_by('lists.list_id');
		$this->db->join('list_tags', 'list_tags.list_id = lists.list_id');
		$this->db->where(array('list_tags.tag_id' => $tag_id, 'lists.type !=' => ListType::HeardIt));
	}
	
	public function lists_by_tag($tag_id, $limit, $offset, $order_by, $direction)
	{
		$this->prepare_lists_by_tag($tag_id);
		$this->order_by($order_by, $direction);
		$this->db->limit($limit, $offset);
		
		$query = $this->db->get();
		return $this->build_lists($query->result());
	}
	
	public function count_lists_by_tag($tag_id)
	{
		$this->prepare_lists_by_tag($tag_id);
		$this->db->from('lists');
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	public function top_lists($limit, $offset)
	{
		$sql = "SELECT ".LIST_COLUMNS." 
              	FROM lists JOIN list_ratings ON list_ratings.list_id = lists.list_id
				WHERE (lists.type != ?)
              	GROUP BY lists.list_id
              	ORDER BY AVG(list_ratings.rating) DESC, COUNT(list_ratings.rating) DESC, 
					lists.list_id DESC
				LIMIT ?, ?";
				
		$query = $this->db->query($sql, array(ListType::HeardIt, $offset, $limit));
		return $this->build_lists($query->result());
	}
	
	public function count_top_lists()
	{
		$sql = "SELECT ".LIST_COLUMNS." 
              	FROM lists JOIN list_ratings ON list_ratings.list_id = lists.list_id
				WHERE (lists.type != ?)
              	GROUP BY lists.list_id";
				
		$query = $this->db->query($sql, array(ListType::HeardIt));
		return $query->num_rows();
	}
	
	public function recent_lists($limit, $offset)
	{
		$this->db->order_by('list_id', 'desc');
		$this->db->limit($limit, $offset);
		$this->db->where('type !=', ListType::HeardIt);
		$query = $this->db->get('lists');
		return $this->build_lists($query->result());
	}
	
	public function count_lists()
	{
		$this->db->where('type !=', ListType::HeardIt);
		return $this->db->count_all_results('lists');
	}
	
	private function prepare_related_lists($list_id)
	{
		$temp_select = "SELECT albums.album_id 
						FROM albums 
						JOIN list_albums ON list_albums.album_id = albums.album_id
						WHERE list_id = ".$list_id;
		
		return "SELECT ".LIST_COLUMNS."
				FROM lists JOIN list_albums ON list_albums.list_id = lists.list_id
				WHERE lists.list_id != ".$list_id." AND lists.type != ".ListType::HeardIt." 
				AND list_albums.album_id IN (".$temp_select.")
				GROUP BY lists.list_id
				ORDER BY COUNT(list_albums.album_id IN (".$temp_select.")) DESC, lists.list_id DESC";
	}
	
	public function related_lists($list_id, $limit, $offset)
	{
		$sql = $this->prepare_related_lists($list_id)." LIMIT ?, ?";
		
		$query = $this->db->query($sql, array($offset, $limit));
		return $this->build_lists($query->result());
	}
	
	public function count_related_lists($list_id)
	{
		$sql = $this->prepare_related_lists($list_id);
		
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	
	private function prepare_search_lists($terms)
	{
		$like = "";
		foreach($terms as $term)
		{
			$like = $like." AND (users.name LIKE '%".$term."%'
								OR lists.title LIKE '%".$term."%'
								OR lists.year LIKE '%".$term."%'
								OR tags.name LIKE '%".$term."%')";
		}
		
		return "SELECT ".LIST_COLUMNS." 
				FROM lists
				JOIN users ON users.user_id = lists.user_id
				LEFT OUTER JOIN list_ratings ON list_ratings.list_id = lists.list_id
				LEFT OUTER JOIN list_tags ON list_tags.list_id = lists.list_id
				LEFT OUTER JOIN tags ON tags.tag_id = list_tags.tag_id
				WHERE lists.type != ".ListType::HeardIt." ".$like." 
				GROUP BY lists.list_id";
	}
	
	public function search_lists($terms, $limit, $offset, $order_by, $direction)
	{
		$sql = $this->prepare_search_lists($terms);
		
		$sql = $sql." ORDER BY ";
		$sort_options = AlbumList::sort_options();
		$order_by = $sort_options[$order_by];
		if($order_by == 'rating')
		{
			$sql = $sql.'AVG(list_ratings.rating) '.$direction.', COUNT(list_ratings.rating)';
		}
		else
		{
			$sql = $sql.$order_by;
		}
		$sql = $sql." ".$direction.', lists.list_id DESC';
		
		$sql = $sql." LIMIT ?, ?";
		
		$query = $this->db->query($sql, array($offset, $limit));
		return $this->build_lists($query->result());
	}
	
	public function count_search_lists($terms)
	{
		$sql = $this->prepare_search_lists($terms);
		
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	
	private function order_by_rating_sql()
	{
		return 'ORDER BY AVG(list_ratings.rating)';
	}
	
	
	/*RETRIEVE/MODIFY INFORMATION FOR A SPECIFIC LIST*/
	
	public function add_album($id, $album_id, $blurb)
	{
		$query = $this->db->get_where('list_albums', array('list_id' => $id, 'album_id' => $album_id));
		if($query->num_rows() > 0)
		{
			return -1; //list already contains album
		}
		else
		{
			$ci =& get_instance();
			$ci->load->model('Album_model');
			$list = $this->get_list($id);
			$list_albums = $ci->album_model->albums_by_list($id, MAX_LIST_ALBUMS, 0);
			if(count($list_albums) >= MAX_LIST_ALBUMS && $list->type != ListType::HeardIt)
			{
				return -2; //list has the maximum number of elements
			}
			else
			{
				$this->db->insert('list_albums', array(
					'list_id' => $id,
					'album_id' => $album_id,
					'ordinal' => count($list_albums) + 1,
					'blurb' => $blurb));
				return 0; //success
			}
		}
	}
	
	private function edit_album($id, $album_id, $property, $value)
	{
		$query = $this->db->get_where('list_albums', array('list_id' => $id, 'album_id' => $album_id));
		if($query->num_rows() == 0)
		{
			return -1; //list doesn't contain this album
		}
		else
		{
			$this->db->where(array('list_id' => $id, 'album_id' => $album_id));
			$this->db->update('list_albums', array($property => $value));
			return 0; //success
		}
	}
	
	public function edit_album_blurb($id, $album_id, $blurb)
	{
		return $this->edit_album($id, $album_id, 'blurb', $blurb);
	}
	
	public function edit_album_ordinal($id, $album_id, $ordinal)
	{
		return $this->edit_album($id, $album_id, 'ordinal', $ordinal);
	}
	
	public function remove_album($id, $album_id)
	{
		$query = $this->db->get_where('list_albums', array('list_id' => $id, 'album_id' => $album_id));
		if($query->num_rows() == 0)
		{
			return -1; //list doesn't contain this album
		}
		else
		{
			//reset subsequent ordinals
			$ordinal = $query->row(0)->ordinal;
			$this->db->where('list_id = '.$id.' AND ordinal > '.$ordinal);
			$query = $this->db->get('list_albums');
			foreach($query->result() as $row)
			{
				$this->edit_album_ordinal($id, $row->album_id, $row->ordinal - 1);
			}
			
			$this->db->delete('list_albums', array('list_id' => $id, 'album_id' => $album_id));
			return 0; //success
		}
	}
	
	public function reorder($id, $old_ordinal, $new_ordinal)
	{
		if($old_ordinal == $new_ordinal)
		{
			return;
		}
		
		$query = $this->db->get_where('list_albums', array('list_id' => $id, 'ordinal' => $old_ordinal));
		$moved_album_id = $query->row(0)->album_id;
		
		//update all affected ordinals
		$limit = abs($old_ordinal - $new_ordinal);
		$offset = $new_ordinal - 1;
		$delta = 1;
		if($old_ordinal < $new_ordinal)
		{
			$offset = $old_ordinal;
			$delta = -1;
		}
		$this->db->order_by('ordinal');
		$query = $this->db->get_where('list_albums', array('list_id' => $id), $limit, $offset);
		foreach($query->result() as $row)
		{
			$this->edit_album_ordinal($id, $row->album_id, $row->ordinal + $delta);
		}
		
		//update original
		$this->edit_album_ordinal($id, $moved_album_id, $new_ordinal);
	}
	
	//gets a list of the tags associated with this album
	public function tags($id)
	{
		$this->db->from('tags');
		$this->db->join('list_tags', 'list_tags.tag_id = tags.tag_id');
		$this->db->where('list_tags.list_id', $id);
		$query = $this->db->get();
		$results = array();
		foreach($query->result() as $row){
			$results[] = new Tag(
				$row->tag_id,
				$row->name);
		}
		return $results;
	}
	
	//adds a tag to the album (inserting if necessary)
	public function add_tag($id, $name)
	{
		$query = $this->db->get_where('tags', array('name' => $name));
		if($query->num_rows() > 0)
		{
			$tid = $query->row(0)->tag_id;
			$query = $this->db->get_where('list_tags', array('list_id' => $id, 'tag_id' => $tid));
			if($query->num_rows() > 0)
			{
				return -1; //album already has this tag, do nothing
			}
			else
			{
				$this->db->insert('list_tags', array('list_id' => $id, 'tag_id' => $tid));
				return 0; //added this tag to list
			}
		}
		else
		{
			$this->db->insert('tags', array('name' => $name));
			$query = $this->db->get_where('tags', array('name' => $name));
			$tid = $query->row(0)->tag_id;
			$this->db->insert('list_tags', array('list_id' => $id, 'tag_id' => $tid));
			return 0; //inserted the tag and added to list
		}
	}
	
	public function remove_tag($id, $tag_id)
	{
		$query = $this->db->get_where('list_tags', array('list_id' => $id, 'tag_id' => $tag_id));
		if($query->num_rows() == 0)
		{
			return -1; //list doesn't contain this tag
		}
		else
		{
			$this->db->delete('list_tags', array('list_id' => $id, 'tag_id' => $tag_id));
			$query = $this->db->get_where('list_tags', array('tag_id' => $tag_id));
			if($query->num_rows() == 0)
			{
				$query = $this->db->get_where('album_tags', array('tag_id' => $tag_id));
				if($query->num_rows() == 0)
				{
					$this->db->delete('tags', array('tag_id' => $tag_id));
				}
			}
			return 0; //success
		}
	}
	
	//averages all ratings for this list
	public function average_rating($id)
	{
		$count = $this->rating_count($id);
		if($count > 0)
		{
			$this->db->select('AVG(rating) AS average');
			$this->db->from('list_ratings');
			$this->db->where('list_id', $id);
			$query = $this->db->get();
			return round($query->row(0)->average, 2);
		}
		else
		{
			return 0;
		}
	}
	
	//counts the number of ratings given to this list
	public function rating_count($id)
	{
		$query = $this->db->get_where('list_ratings', array('list_id' => $id));
		return $query->num_rows();
	}
	
	//inserts a rating
	public function add_rating($id, $rating, $user_id)
	{
		$query = $this->db->get_where('list_ratings', array('list_id' => $id, 'user_id' => $user_id));
		if($query->num_rows() > 0)
		{
			$this->db->update('list_ratings', array('rating' => $rating),
				array('list_id' => $id, 'user_id' => $user_id));
		}
		else
		{
			$this->db->insert('list_ratings', array('list_id' => $id, 'rating' => $rating, 
				'user_id' => $user_id));
		}
	}
	
	public function random()
	{
		$this->db->order_by('list_id', 'random');
		$this->db->where('type !=', ListType::HeardIt);
		$query = $this->db->get('lists');
		if($query->num_rows() > 0)
		{
			return $query->row()->list_id;
		}
		else
		{
			return 0;
		}
	}
}
?>