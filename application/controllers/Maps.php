<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

class Maps extends CI_Controller {

	public function __construct () 	{
		parent::__construct();		
		$this->load->library('template');
		$this->load->model('maps_model');
	}

	 
	public function index() {
		$data = 'Google Maps';
		$this->template->set_layout(DEFAULT_LAYOUT)->build('maps/index', $data);
		
	}

	public function map_data() {
		$mapName = $_GET['name'];

		$data['mapOptions'] = $this->maps_model->get_map_options($mapName); // Retrieve all map data

		foreach ($data['mapOptions'] as $key => $mapOption) {
			// Access the markerList property of each object
			$markerList = explode(',', $mapOption->markerList); // Convert string to array

			// Explode the latitude string to get individual latitude values
			$latitudes = explode(',', $mapOption->latitude);

			// Explode the latitude string to get individual latitude values
			$longitudes = explode(',', $mapOption->longitude);

			// Explode the latitude string to get individual content values
			$content = explode(',', $mapOption->content);

			// Explode the latitude string to get individual content values
			$title = explode(',', $mapOption->title);
			
			// Initialize an empty array to store the marker data
			$markerData = array();

			// Loop through markerList array and create key-value pairs
			foreach ($markerList as $index => $marker) {
				$markerData[] = array(
					'markerList' => $marker,
					'markerLatitude' => $latitudes[$index],
					'markerLongitude' => $longitudes[$index],
					'content' => $content[$index],
					'title' => $title[$index]
				);
			
				foreach($mapOption->infoWindowData as $index => $infoWindowId) {
					if ($marker == $infoWindowId['id']) {
						$markerData[$index]['markerInfoWindowData']['id'] = $infoWindowId['id'];
						$markerData[$index]['markerInfoWindowData']['content'] = $infoWindowId['html_content'];
					}
				}
			}

			// Assign the markerData array to the current map option object
			$data['mapOptions'][$key]->markerData = $markerData;

			unset($data['mapOptions'][$key]->marker_names);
			unset($data['mapOptions'][$key]->latitude);
			unset($data['mapOptions'][$key]->infoWindowName);
			unset($data['mapOptions'][$key]->maxWidth);
			unset($data['mapOptions'][$key]->minWidth);
			unset($data['mapOptions'][$key]->minHeight);
			unset($data['mapOptions'][$key]->maxHeight);
			unset($data['mapOptions'][$key]->pixelOffset);
			unset($data['mapOptions'][$key]->position);
			unset($data['mapOptions'][$key]->autoClose);
			unset($data['mapOptions'][$key]->content);
		}

		echo json_encode($data);
    }
}