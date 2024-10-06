
<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Sitemap extends CI_Controller {


    /**
     * Index Page for this controller.
     *
     */
    public function index()
    {
        $this->load->database();
        $query = $this->db->get("tbl_sitemap");
        $data['allpages'] = $query->result();

        $this->load->view('sitemap', $data);
    }
}
?>
