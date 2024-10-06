	<header>

	 <!-- Header Start -->
	<?php include "nav.php" ?>
	<!-- Header End -->

	 <!-- Breadcrumb Start -->
	<?php
	if ( $this->session->userdata('user_id') AND $this->uri->segment(1) !='') {
	$this->load->view('layouts/breadcrumb');
	} ?>
	<!--Breadcrumb End -->

	<!-- CARD FILTER Start -->
	<?php if ($this->uri->segment(1) == 'games' && 

	($this->uri->segment(3) == 'play' ||
	$this->uri->segment(3) == 'drafted' ||
	$this->uri->segment(3) == 'published' ||
	$this->uri->segment(3) == 'live' ||
	$this->uri->segment(3) == 'review' ||
	$this->uri->segment(3) == 'completed' ||
	$this->uri->segment(3) == 'wishlist' ||
	$this->uri->segment(3) == 'played')

	&& $this->uri->segment(4) == '') { ?>
		<div class="container-lg">
			<div class="searchcardcontainer">
				<?php $this->load->view('layouts/filters/cardfilters');?>
			</div>
		</div>
	<?php } ?>
	<!-- CARD FILTER End -->

	<!-- Header Start -->
  	<?php
	if ($this->uri->segment(1)=='admin' AND $this->uri->segment(2) !='') {
	$this->load->view('admin/nav');
	}
	?>
  	<!-- Header End -->

	</header>
