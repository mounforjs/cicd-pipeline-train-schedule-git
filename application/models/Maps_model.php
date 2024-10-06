<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Maps_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function insert_map_data($data) {
        // Insert data into the 'maps_maps' table
        $this->db->insert('tbl_maps', $data);

        // Check if insertion was successful
        return $this->db->affected_rows() > 0;
    }

    public function get_map_options($mapName) {
        $this->db->select('m.*, m.markerList AS markerList, mm.content, GROUP_CONCAT(mm.markerName) AS marker_names,  
            GROUP_CONCAT(mm.latitude) AS latitude, GROUP_CONCAT(mm.longitude) AS longitude,
            GROUP_CONCAT(mm.content) AS content, GROUP_CONCAT(mm.title) AS title,
            GROUP_CONCAT(
                CONCAT_WS("|", 
                    iw.id, 
                    iw.windowContent, 
                    iw.maxWidth, 
                    iw.minWidth,
                    iw.maxHeight,
                    iw.minHeight,
                    iw.pixelOffset,
                    iw.position,
                    iw.autoClose
                )
            SEPARATOR "DASH") AS infoWindowData'
        );

            $this->db->from('tbl_maps m');
            $this->db->where('m.MapName', $mapName);
            $this->db->join('tbl_maps_markers mm', 'FIND_IN_SET(mm.id, m.markerList)');
            $this->db->join('tbl_maps_infowindows iw', 'mm.infoWindowId = iw.id', 'left'); // Use LEFT JOIN instead of RIGHT JOIN
            $this->db->group_by('m.id, m.markerList');

            $query = $this->db->get();
            $result = $query->result();

           foreach ($result as $index => $row) {
            $htmlParts = explode('DASH', $row->infoWindowData);
            
            // Initialize an array to store parsed data
            $parsedData = [];
            
            // Iterate over each HTML part
            if (count($htmlParts) > 0) {
                foreach ($htmlParts as $htmlPart) {
                      // Split the concatenated string by commas
                    $data = explode('|',  $htmlPart);
                    if(count($data) >1){
                        $parsedData[] = [
                            'id' => $data[0],
                            'html_content' => $data[1]
                        ];
                    }
                }
                $result[$index]->infoWindowData = $parsedData;
            }

            // Check if query was successful
            if ($query) {
                // Return the result set as an array of objects
                return  $result; //$query->result();
            } else {
                // Return false if query failed
                return false;
            }
        }
    }

    public function get_map_markers() {
        // Select all columns from the maps table
        $query = $this->db->get('tbl_maps_markers');

        // Check if query was successful
        if ($query) {
            // Return the result set as an array of objects
            return $query->result();
        } else {
            // Return false if query failed
            return false;
        }
    }

    public function get_map_infowindows() {
        // Select all columns from the maps table
        $query = $this->db->get('tbl_maps_infowindows');

        // Check if query was successful
        if ($query) {
            // Return the result set as an array of objects
            return $query->result();
        } else {
            // Return false if query failed
            return false;
        }
    }

}
