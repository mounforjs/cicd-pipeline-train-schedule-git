<?php 
		$config = array();
		
		$config["suffix"] = !empty($filters) ? '/' . $this -> uri -> assoc_to_uri($filters) : '';
		$config['use_page_numbers'] = true;
		
		$config['attributes'] = array('class' => 'link');
		$config["num_links"] = 3;
		
		$config['full_tag_open'] = "<ul class=''>";
		$config['full_tag_close'] ="</ul>";
		$config['num_tag_open'] = '<li class="">';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class=" active"><a class="link" href="#">';
		$config['cur_tag_close'] = "</a></li>";
		$config['next_tag_open'] = '<li class="next">';
		$config['next_tag_close'] = "</li>";
		$config['prev_tag_open'] = '<li class="prev">';
		$config['prev_tag_close'] = "</li>";
		$config['prev_link'] = "Previous";
		$config['next_link'] = "Next";
		$config['first_link'] = "<i class='fa fa-arrow-left'></i>";
		$config['last_link'] = '<i class="fa fa-arrow-right"></i>';
		$config['first_tag_open'] = '<li class="">';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li class="">';
		$config['last_tag_close'] = '</li>';
?>