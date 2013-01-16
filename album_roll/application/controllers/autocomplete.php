<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Autocomplete extends MY_Controller
{
	function Autocomplete()
	{
		parent::MY_Controller();
	}
	
	public function artists()
	{
		$term = $this->input->post('term');
		$results = $this->autocomplete_model->search_artists($term);
		echo json_encode($results);
	}
	
	public function labels()
	{
		$term = $this->input->post('term');
		$results = $this->autocomplete_model->search_labels($term);
		echo json_encode($results);
	}
	
	public function tags()
	{
		$term = $this->input->post('term');
		$results = $this->autocomplete_model->search_tags($term);
		echo json_encode($results);
	}
	
	public function albums()
	{
		$term = $this->input->post('term');
		$results = $this->autocomplete_model->search_albums($term);
		echo json_encode($results);
	}
	
	public function all()
	{
		$term = $this->input->post('term');
		$results = $this->autocomplete_model->search_all($term);
		echo json_encode($results);
	}
}
?>