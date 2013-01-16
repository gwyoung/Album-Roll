<?php
define("ALBUM_COLUMNS", 'albums.album_id, albums.title, albums.artist_id, albums.label_id, albums.release_date, albums.image_url');
class Album_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		include_once APPPATH.'models/Album.php';
		include_once APPPATH.'models/Artist.php';
		include_once APPPATH.'models/Label.php';
		include_once APPPATH.'models/Link.php';
		include_once APPPATH.'models/LinkType.php';
		include_once APPPATH.'models/Tag.php';
	}
	
	
	/*ALBUM OPERATIONS*/
	
	//creates an album
	public function create_album($title, $artist, $release_date, $label, $image_url)
	{
		if(!$this->check_album($title, $artist))
		{
			$query = $this->db->get_where('artists', array('name' => $artist));
			$artist_id = $query->row(0)->artist_id;
			
			$plainlabel = preg_replace('/ Records$/i', '', $label);
			$plainlabel = preg_replace('/ Recordings$/i', '', $plainlabel);
			$labelrecords = $plainlabel.' Records';
			$labelrecordings = $plainlabel.' Recordings';
			$this->db->where('name =', $plainlabel);
			$this->db->or_where('name =', $labelrecords);
			$this->db->or_where('name =', $labelrecordings);
			$query = $this->db->get_where('labels');
			if($query->num_rows() == 0)
			{
				$this->db->insert('labels', array('name' => $label));
				$query = $this->db->get_where('labels', array('name' => $label));
			}
			$label_id = $query->row(0)->label_id;
			
			$this->db->insert('albums', array(
				'title' => $title, 
				'artist_id' => $artist_id,
				'release_date' => $release_date,
				'label_id' => $label_id,
				'image_url' => $image_url));
			$query = $this->db->get_where('albums', array('title' => $title, 'artist_id' => $artist_id));
			return $query->row()->album_id;
		}
	}
	
	public function check_album($title, $artist)
	{
		$plainartist = preg_replace('/^The /i', '', $artist);
		$theartist = 'The '.$plainartist;
		$this->db->where('name =', $plainartist);
		$this->db->or_where('name =', $theartist); 
		$query = $this->db->get('artists');
		if($query->num_rows() == 0)
		{
			$this->db->insert('artists', array('name' => $artist));
			$query = $this->db->get_where('artists', array('name' => $artist));
		}
		$artist_id = $query->row(0)->artist_id;
		$query = $this->db->get_where('albums', array('title' => $title, 'artist_id' => $artist_id));
		if($query->num_rows()>0)
		{
			return $query->row()->album_id;
		}
		else
		{
			return 0;
		}
	}
	
	
	/*RETRIEVE MULTIPLE ALBUMS*/
	
	private function build_albums($query, $in_list)
	{
		$results = array();
		foreach($query->result() as $row)
		{
			if($in_list)
			{
				$ordinal = $row->ordinal;
				$blurb = $row->blurb;
			}
			else
			{
				$ordinal = FALSE;
				$blurb = '';
			}
			
			$query = $this->db->get_where('artists', array('artist_id' => $row->artist_id));
			$artist = new Artist($row->artist_id, $query->row(0)->name);
			
			$query = $this->db->get_where('labels', array('label_id' => $row->label_id));
			$label = new Label($row->label_id, $query->row(0)->name);
			
			$results[] = new Album(
				$row->album_id,
				$row->title,
				$artist,
				$label,
				mysqldatetime_to_timestamp($row->release_date),
				$blurb,
				$row->image_url,
				$this->average_rating($row->album_id),
				$this->rating_count($row->album_id),
				$ordinal);
		}
		return $results;
	}
	
	private function order_by($order_by, $direction)
	{
		$sort_options = Album::sort_options();
		$order_by = $sort_options[$order_by];
		if($order_by == 'rating')
		{
			$this->db->from('albums');
			$this->db->join('album_ratings', 'album_ratings.album_id = albums.album_id', 'left outer');
			$this->db->group_by('albums.album_id');
			$this->db->order_by('AVG(album_ratings.rating) '.$direction.', 
				COUNT(album_ratings.rating) '.$direction.', albums.album_id DESC');
		}
		else
		{
			$this->db->order_by($order_by.' '.$direction.', album_id');
			$this->db->from('albums');
		}
	}
	
	public function albums_by_artist($artist_id, $limit, $offset, $order_by, $direction)
	{
		$this->order_by($order_by, $direction);
		$this->db->where('artist_id', $artist_id);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $this->build_albums($query, FALSE);
	}
	
	public function count_albums_by_artist($artist_id)
	{
		$query = $this->db->get_where('albums', array('artist_id' => $artist_id));
		return $query->num_rows();
	}
	
	public function albums_by_label($label_id, $limit, $offset, $order_by, $direction)
	{
		$this->order_by($order_by, $direction);
		$this->db->where('label_id', $label_id);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $this->build_albums($query, FALSE);
	}
	
	public function count_albums_by_label($label_id)
	{
		$query = $this->db->get_where('albums', array('label_id' => $label_id));
		return $query->num_rows();
	}
	
	public function albums_by_year($year, $limit, $offset, $order_by, $direction)
	{
		$this->order_by($order_by, $direction);
		$this->db->where('YEAR(release_date)', $year);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $this->build_albums($query, FALSE);
	}
	
	public function count_albums_by_year($year)
	{
		$query = $this->db->get_where('albums', array('YEAR(release_date)' => $year));
		return $query->num_rows();
	}
	
	public function albums_by_decade($decade, $limit, $offset, $order_by, $direction)
	{
		$decade = intval(substr($decade, 0, 4));
		$end = $decade + 10;
		$this->order_by($order_by, $direction);
		$this->db->where('YEAR(release_date) >= '.$decade.' AND YEAR(release_date) < '.$end);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $this->build_albums($query, FALSE);
	}
	
	public function count_albums_by_decade($decade)
	{
		$decade = intval(substr($decade, 0, 4));
		$end = $decade + 10;
		$this->db->where('YEAR(release_date) >= '.$decade.' AND YEAR(release_date) < '.$end);
		$query = $this->db->get('albums');
		return $query->num_rows();
	}
	
	private function prepare_albums_by_tag($tag_id)
	{
		$this->db->select(ALBUM_COLUMNS);
		$this->db->group_by('albums.album_id');
		$this->db->join('album_tags', 'album_tags.album_id = albums.album_id');
		$this->db->where('album_tags.tag_id', $tag_id);
	}
	
	public function albums_by_tag($tag_id, $limit, $offset, $order_by, $direction)
	{
		$this->prepare_albums_by_tag($tag_id);
		$this->order_by($order_by, $direction);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $this->build_albums($query, FALSE);
	}
	
	public function count_albums_by_tag($tag_id)
	{
		$this->prepare_albums_by_tag($tag_id);
		$this->db->from('albums');
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	private function prepare_albums_by_list($list_id)
	{
		$this->db->select(ALBUM_COLUMNS.', list_albums.blurb, list_albums.ordinal');
		$this->db->group_by('albums.album_id');
		$this->db->order_by('list_albums.ordinal', 'asc');
		$this->db->from('albums');
		$this->db->join('list_albums', 'list_albums.album_id = albums.album_id');
		$this->db->where('list_albums.list_id', $list_id);
	}
	
	public function albums_by_list($list_id, $limit, $offset)
	{
		$this->prepare_albums_by_list($list_id);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		return $this->build_albums($query, TRUE);
	}
	
	public function count_albums_by_list($list_id)
	{
		$this->prepare_albums_by_list($list_id);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	//finds the n top-rated albums
	public function top_albums($limit, $offset)
	{
		$sql = "SELECT ".ALBUM_COLUMNS." 
              	FROM albums JOIN album_ratings ON album_ratings.album_id = albums.album_id
              	GROUP BY albums.album_id
              	ORDER BY AVG(album_ratings.rating) DESC, COUNT(album_ratings.rating) DESC,
					albums.album_id DESC
				LIMIT ?, ?";
				
		$query = $this->db->query($sql, array($offset, $limit));
		return $this->build_albums($query, FALSE);
	}
	
	public function count_top_albums()
	{
		$sql = "SELECT ".ALBUM_COLUMNS." 
              	FROM albums JOIN album_ratings ON album_ratings.album_id = albums.album_id
              	GROUP BY albums.album_id";
				
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	
	public function recent_albums($limit, $offset)
	{
		$this->db->order_by('album_id', 'desc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get('albums');
		return $this->build_albums($query, FALSE);
	}
	
	public function count_albums()
	{
		return $query = $this->db->count_all_results('albums');
	}
	
	private function prepare_related_albums($id)
	{
		$temp_select = "SELECT lists.list_id 
						FROM lists 
						JOIN list_albums ON list_albums.list_id = lists.list_id
						WHERE list_albums.album_id = ".$id." AND lists.type != ".ListType::HeardIt."
						GROUP BY lists.list_id";
						
		return "SELECT ".ALBUM_COLUMNS." 
				FROM lists JOIN list_albums ON list_albums.list_id = lists.list_id
				JOIN albums ON albums.album_id = list_albums.album_id
				WHERE albums.album_id != ".$id." AND lists.list_id IN (".$temp_select.")
				GROUP BY album_id
				ORDER BY COUNT(lists.list_id IN (".$temp_select.")) DESC, albums.album_id DESC";
	}
	
	//returns albums similar to a given album
	public function related_albums($id, $limit, $offset)
	{
		$sql = $this->prepare_related_albums($id)." LIMIT ?, ?";

		$query = $this->db->query($sql, array($offset, $limit));
		return $this->build_albums($query, FALSE);
	}
	
	public function count_related_albums($id)
	{
		$sql = $this->prepare_related_albums($id);

		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	
	private function prepare_search_albums($terms)
	{
		$like = "";
		foreach($terms as $term)
		{
			$like = $like." AND (albums.title LIKE '%".$term."%'
								OR YEAR(albums.release_date) = '".$term."'
								OR artists.name LIKE '%".$term."%'
								OR labels.name LIKE '%".$term."%'
								OR tags.name LIKE '%".$term."%')";
		}
		
		return "SELECT ".ALBUM_COLUMNS." 
				FROM albums
				JOIN artists ON artists.artist_id = albums.artist_id
				JOIN labels ON labels.label_id = albums.label_id
				LEFT OUTER JOIN album_ratings ON album_ratings.album_id = albums.album_id
				LEFT OUTER JOIN album_tags ON album_tags.album_id = albums.album_id
				LEFT OUTER JOIN tags ON tags.tag_id = album_tags.tag_id
				WHERE albums.album_id != 0 ".$like." 
				GROUP BY albums.album_id";
	}
	
	public function search_albums($terms, $limit, $offset, $order_by, $direction)
	{
		$sql = $this->prepare_search_albums($terms);
		
		$sql = $sql." ORDER BY ";
		$sort_options = Album::sort_options();
		$order_by = $sort_options[$order_by];
		if($order_by == 'rating')
		{
			$sql = $sql.'AVG(album_ratings.rating) '.$direction.', COUNT(album_ratings.rating)';
		}
		else
		{
			$sql = $sql.$order_by;
		}
		$sql = $sql." ".$direction.', albums.album_id DESC';
		
		$sql = $sql." LIMIT ?, ?";
		
		$query = $this->db->query($sql, array($offset, $limit));
		return $this->build_albums($query, FALSE);
	}
	
	public function count_search_albums($terms)
	{
		$sql = $this->prepare_search_albums($terms);
		
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	
	private function prepare_trending_albums()
	{
		$this->db->select(ALBUM_COLUMNS);
		$this->db->from('albums');
		$this->db->join('list_albums', 'list_albums.album_id = albums.album_id');
		$this->db->join('lists', 'lists.list_id = list_albums.list_id');
		$this->db->where('lists.type !=', ListType::HeardIt);
		$this->db->group_by('albums.album_id');
		$this->db->order_by('COUNT(DISTINCT list_albums.list_id) desc, albums.album_id desc');
	}
	
	public function trending_albums($limit, $offset)
	{
		$this->prepare_trending_albums();
		$this->db->limit($limit, $offset);
		
		$query = $this->db->get();
		return $this->build_albums($query, FALSE);
	}
	
	public function count_trending_albums()
	{
		$this->prepare_trending_albums();
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	public function trending_albums_by_year($year, $limit, $offset)
	{
		$this->prepare_trending_albums();
		$this->db->where('YEAR(albums.release_date)', $year);
		$this->db->limit($limit, $offset);
		
		$query = $this->db->get();
		return $this->build_albums($query, FALSE);
	}
	
	public function count_trending_albums_by_year($year)
	{
		$this->prepare_trending_albums();
		$this->db->where('YEAR(albums.release_date)', $year);
		
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	private function prepare_recommended_albums($user_id)
	{
		/*
		1. get all albums from user's lists into temp_albums
		2. get all albums from user's heardit into ignore_albums
		3. get all lists that use any of the albums in temp_albums
		4. get all albums where in temp lists and not in temp albums and not in ignore albums
		5. group by album_id
		6. order by count lists in temp_list
		*/
		
		$user_lists = "SELECT lists.list_id
						FROM lists
						WHERE lists.user_id = ".$user_id." AND lists.type != ".ListType::HeardIt;
		
		$user_albums = "SELECT list_albums.album_id 
						FROM users 
						JOIN lists ON lists.user_id = users.user_id 
						JOIN list_albums ON list_albums.list_id = lists.list_id
						WHERE users.user_id = ".$user_id." AND lists.type != ".ListType::HeardIt."
						GROUP BY list_albums.album_id";
		
		$ignore_albums = "SELECT list_albums.album_id
							FROM users
							JOIN lists ON lists.user_id = users.user_id
							JOIN list_albums ON list_albums.list_id = lists.list_id
							WHERE users.user_id = ".$user_id." AND lists.type = ".ListType::HeardIt."
							GROUP BY list_albums.album_id";
							
		$ignore_lists = "SELECT lists.list_id
						  FROM lists
						  WHERE lists.type = ".ListType::HeardIt."";
		
		$temp_lists = "SELECT list_albums.list_id
						FROM list_albums
						WHERE list_albums.list_id NOT IN (".$user_lists.")
						AND list_albums.list_id NOT IN (".$ignore_lists.")
						AND list_albums.album_id IN (".$user_albums.")";
		
		return "SELECT ".ALBUM_COLUMNS."
				FROM albums
				JOIN list_albums ON list_albums.album_id = albums.album_id
				WHERE albums.album_id NOT IN (".$user_albums.")
				AND albums.album_id NOT IN (".$ignore_albums.")
				AND list_albums.list_id IN (".$temp_lists.")
				GROUP BY albums.album_id
				ORDER BY COUNT(list_albums.list_id IN (".$temp_lists.")) DESC, albums.album_id DESC";
	}
	
	public function recommended_albums($user_id, $limit, $offset)
	{
		$sql = $this->prepare_recommended_albums($user_id)." LIMIT ?, ?";
		
		$query = $this->db->query($sql, array($offset, $limit));
		return $this->build_albums($query, FALSE);
	}
	
	public function count_recommended_albums($user_id)
	{
		$sql = $this->prepare_recommended_albums($user_id);
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	
	private function order_by_rating_sql()
	{
		return 'ORDER BY AVG(album_ratings.rating)';
	}
	
	
	/*RETRIEVE/MODIFY DETAILS FOR A SPECIFIC ALBUM*/
	
	public function get_album($id)
	{
		$query = $this->db->get_where('albums', array('album_id' => $id));
		if($query->num_rows() == 0)
		{
			return false;
		}
		$albums = $this->build_albums($query, FALSE);
		return $albums[0];
	}
	
	//returns a list of links filtered by a link type
	private function links($id, $type)
	{
		$query = $this->db->get_where('album_links', array('album_id' => $id, 'type' => $type));
		$results = array();
		foreach($query->result() as $row){
			$results[] = new Link(
				$row->name,
				$row->url);
		}
		return $results;
	}
	
	//inserts a link given a link type
	private function insert_link($id, $link, $type)
	{
		$query = $this->db->get_where('album_links', 
			array('album_id' => $id, 'url' => $link->url));
		if($query->num_rows()>0)
		{
			$this->db->where(array('album_id' => $id, 'url' => $link->url));
			$this->db->update('album_links', array('name' => $link->name));
		}
		else
		{
			$this->db->insert('album_links', 
				array('album_id' => $id, 'name' => $link->name, 'url' => $link->url, 'type' => $type));
		}
	}
	
	public function review_links($id)
	{
		return $this->links($id, LinkType::Review);
	}
	
	public function insert_review_link($id, $link)
	{
		$this->insert_link($id, $link, LinkType::Review);
	}
	
	public function information_links($id)
	{
		return $this->links($id, LinkType::Information);
	}
	
	public function insert_information_link($id, $link)
	{
		$this->insert_link($id, $link, LinkType::Information);
	}
	
	public function stream_links($id)
	{
		return $this->links($id, LinkType::Stream);
	}
	
	public function insert_stream_link($id, $link)
	{
		$this->insert_link($id, $link, LinkType::Stream);
	}
	
	//gets a list of the tags associated with this album
	public function tags($id)
	{
		$this->db->from('tags');
		$this->db->join('album_tags', 'album_tags.tag_id = tags.tag_id');
		$this->db->where('album_tags.album_id', $id);
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
			$query = $this->db->get_where('album_tags', array('album_id' => $id, 'tag_id' => $tid));
			if($query->num_rows() > 0)
			{
				return -1; //album already has this tag, do nothing
			}
			else
			{
				$this->db->insert('album_tags', array('album_id' => $id, 'tag_id' => $tid));
				return 0; //added this tag to album
			}
		}
		else
		{
			$this->db->insert('tags', array('name' => $name));
			$query = $this->db->get_where('tags', array('name' => $name));
			$tid = $query->row(0)->tag_id;
			$this->db->insert('album_tags', array('album_id' => $id, 'tag_id' => $tid));
			return 0; //inserted the tag and added to album
		}
	}
	
	//averages all ratings for this album
	public function average_rating($id)
	{
		$count = $this->rating_count($id);
		if($count > 0)
		{
			$this->db->select('AVG(rating) AS average');
			$this->db->from('album_ratings');
			$this->db->where('album_id', $id);
			$query = $this->db->get();
			return round($query->row(0)->average, 2);
		}
		else
		{
			return 0;
		}
	}
	
	//counts the number of ratings given to this album
	public function rating_count($id)
	{
		$query = $this->db->get_where('album_ratings', array('album_id' => $id));
		return $query->num_rows();
	}
	
	//inserts a rating
	public function add_rating($id, $rating, $user_id)
	{
		$query = $this->db->get_where('album_ratings', array('album_id' => $id, 'user_id' => $user_id));
		if($query->num_rows() > 0)
		{
			$this->db->update('album_ratings', array('rating' => $rating),
				array('album_id' => $id, 'user_id' => $user_id));
		}
		else
		{
			$this->db->insert('album_ratings', array('album_id' => $id, 'rating' => $rating, 'user_id' 
				=> $user_id));
		}
	}
	
	
	/* ARTISTS AND LABELS AND TAGS OH MY */
	
	public function get_artist($id)
	{
		$query = $this->db->get_where('artists', array('artist_id' => $id));
		if($query->num_rows() > 0)
		{
			return new Artist($id, $query->row(0)->name);
		}
	}
	
	public function get_label($id)
	{
		$query = $this->db->get_where('labels', array('label_id' => $id));
		if($query->num_rows() > 0)
		{
			return new Label($id, $query->row(0)->name);
		}
	}
	
	public function get_tag($id)
	{
		$query = $this->db->get_where('tags', array('tag_id' => $id));
		if($query->num_rows() > 0)
		{
			return new Tag($id, $query->row(0)->name);
		}
	}
	
	public function trending_tags($limit, $offset)
	{
		$this->db->select('tags.tag_id, tags.name');
		$this->db->from('tags');
		$this->db->join('album_tags', 'album_tags.tag_id = tags.tag_id', 'left outer');
		$this->db->join('list_tags', 'list_tags.tag_id = tags.tag_id', 'left outer');
		$this->db->having('(COUNT(DISTINCT album_tags.album_id) + COUNT(DISTINCT list_tags.list_id) > 0)');
		$this->db->order_by('(COUNT(DISTINCT album_tags.album_id) + COUNT(DISTINCT list_tags.list_id))', 
			'desc');
		$this->db->group_by('tags.tag_id');
		$this->db->limit($limit, $offset);
		
		$query = $this->db->get();
		$results = array();
		foreach($query->result() as $row)
		{
			$results[] = new Tag($row->tag_id, $row->name);
		}
		return $results;
	}
	
	public function random()
	{
		$this->db->order_by('album_id', 'random');
		$query = $this->db->get('albums');
		if($query->num_rows() > 0)
		{
			return $query->row()->album_id;
		}
		else
		{
			return 0;
		}
	}
}
?>