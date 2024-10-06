<!doctype html>
<html>
<head>

<?php $this->load->view('layouts/meta_tags');?>

<!-- Start VWO Async SmartCode -->
<link rel="preconnect" href="https://dev.visualwebsiteoptimizer.com" />
<script type='text/javascript' id='vwoCode'>
window._vwo_code || (function() {
var account_id=902765,
version=2.1,
settings_tolerance=2000,
hide_element='body',
hide_element_style = 'opacity:0 !important;filter:alpha(opacity=0) !important;background:none !important',
/* DO NOT EDIT BELOW THIS LINE */
f=false,w=window,d=document,v=d.querySelector('#vwoCode'),cK='_vwo_'+account_id+'_settings',cc={};try{var c=JSON.parse(localStorage.getItem('_vwo_'+account_id+'_config'));cc=c&&typeof c==='object'?c:{}}catch(e){}var stT=cc.stT==='session'?w.sessionStorage:w.localStorage;code={use_existing_jquery:function(){return typeof use_existing_jquery!=='undefined'?use_existing_jquery:undefined},library_tolerance:function(){return typeof library_tolerance!=='undefined'?library_tolerance:undefined},settings_tolerance:function(){return cc.sT||settings_tolerance},hide_element_style:function(){return'{'+(cc.hES||hide_element_style)+'}'},hide_element:function(){if(performance.getEntriesByName('first-contentful-paint')[0]){return''}return typeof cc.hE==='string'?cc.hE:hide_element},getVersion:function(){return version},finish:function(e){if(!f){f=true;var t=d.getElementById('_vis_opt_path_hides');if(t)t.parentNode.removeChild(t);if(e)(new Image).src='https://dev.visualwebsiteoptimizer.com/ee.gif?a='+account_id+e}},finished:function(){return f},addScript:function(e){var t=d.createElement('script');t.type='text/javascript';if(e.src){t.src=e.src}else{t.text=e.text}d.getElementsByTagName('head')[0].appendChild(t)},load:function(e,t){var i=this.getSettings(),n=d.createElement('script'),r=this;t=t||{};if(i){n.textContent=i;d.getElementsByTagName('head')[0].appendChild(n);if(!w.VWO||VWO.caE){stT.removeItem(cK);r.load(e)}}else{var o=new XMLHttpRequest;o.open('GET',e,true);o.withCredentials=!t.dSC;o.responseType=t.responseType||'text';o.onload=function(){if(t.onloadCb){return t.onloadCb(o,e)}if(o.status===200){_vwo_code.addScript({text:o.responseText})}else{_vwo_code.finish('&e=loading_failure:'+e)}};o.onerror=function(){if(t.onerrorCb){return t.onerrorCb(e)}_vwo_code.finish('&e=loading_failure:'+e)};o.send()}},getSettings:function(){try{var e=stT.getItem(cK);if(!e){return}e=JSON.parse(e);if(Date.now()>e.e){stT.removeItem(cK);return}return e.s}catch(e){return}},init:function(){if(d.URL.indexOf('__vwo_disable__')>-1)return;var e=this.settings_tolerance();w._vwo_settings_timer=setTimeout(function(){_vwo_code.finish();stT.removeItem(cK)},e);var t;if(this.hide_element()!=='body'){t=d.createElement('style');var i=this.hide_element(),n=i?i+this.hide_element_style():'',r=d.getElementsByTagName('head')[0];t.setAttribute('id','_vis_opt_path_hides');v&&t.setAttribute('nonce',v.nonce);t.setAttribute('type','text/css');if(t.styleSheet)t.styleSheet.cssText=n;else t.appendChild(d.createTextNode(n));r.appendChild(t)}else{t=d.getElementsByTagName('head')[0];var n=d.createElement('div');n.style.cssText='z-index: 2147483647 !important;position: fixed !important;left: 0 !important;top: 0 !important;width: 100% !important;height: 100% !important;background: white !important;';n.setAttribute('id','_vis_opt_path_hides');n.classList.add('_vis_hide_layer');t.parentNode.insertBefore(n,t.nextSibling)}var o='https://dev.visualwebsiteoptimizer.com/j.php?a='+account_id+'&u='+encodeURIComponent(d.URL)+'&vn='+version;if(w.location.search.indexOf('_vwo_xhr')!==-1){this.addScript({src:o})}else{this.load(o+'&x=true')}}};w._vwo_code=code;code.init();})();
</script>
<!-- End VWO Async SmartCode -->

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i) {w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KSLM9S7');</script>
<!-- End Google Tag Manager -->

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400..800&display=swap" rel="stylesheet">

	<script src="https://kit.fontawesome.com/b2582bc9e2.js" crossorigin="anonymous"></script>

	<!-- Selectize plugin head elements -CSS -->
	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css"> -->
	<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.1/css/selectize.default.css'>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" ></script>

	<!-- Google Charts for Dashboard -->
	<?php if ( $this->uri->segment(1) == 'dashboard') { ?>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<?php } ?>

	<!-- jquery - load once or dataTables breaks - load after popper.js? causing issues if loaded before-->
	<script src="<?php echo asset_url('assets/js/jquery.min.js'); ?>"></script>

	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

	<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/style.css'); ?>" />
	<script src="<?php echo asset_url('assets/js/common.js'); ?>"></script>

	<!-- Common head elements -CSS/JS -->
	<?php if ( $this->uri->segment(4) != '' AND $this->uri->segment(2)=='show') { ?>
	<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/owl.carousel.min.css'); ?>" />
	<!-- Flexslider for Pize -->
	<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/flexslider.css'); ?>" />
	<?php } ?>


	<?php if ( $this->uri->segment(1) == '') { ?>
	<!-- owl carousel CSS -->
	<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/owl.carousel.min.css'); ?>" />
	<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/flexslider.css'); ?>" />
	<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/owl.theme.default.min.css'); ?>" />
	<!-- HOMEPAGE SLIDE -->
	<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/da-slide.css'); ?>" />
	<script type="text/javascript" src="assets/js/modernizr.js"></script>
		<noscript>
			<link rel="stylesheet" type="text/css" href="assets/css/nojs.css" />
		</noscript>
	<!-- END HOMEPAGE SLIDE -->
	<!-- TESTIMONIAL SLIDE -->
	<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
	<!-- END TESTIMONIAL SLIDE -->
	<?php } ?>

	<?php if ( $this->uri->segment(1) == 'seegames') { ?>
	<!-- Infinite Scroll -->
	<link href="<?php echo asset_url('assets/css/perfect-scrollbar.css'); ?>" rel="stylesheet">
    <script src="<?php echo asset_url('assets/js/perfect-scrollbar.js'); ?>"></script>
	<!-- End Infinite Scroll -->
	<?php } ?>

	<?php if ( $this->uri->segment(1) == 'login' || $this->uri->segment(1) == 'register' || $this->uri->segment(1) == 'reset' || $this->uri->segment(1) == 'reset_verify') { ?>
	<link href="<?php echo asset_url('assets/css/gsdk-bootstrap-wizard.css'); ?>" rel="stylesheet" />

	<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/loginpage.css'); ?>" />

	<?php } ?>

	<style type="text/css">
	/* Popup box BEGIN */
	.timeout{
		background:rgba(0,0,0,.4);
		cursor:pointer;
		display:none;
		height:100%;
		position:fixed;
		text-align:center;
		top:0;
		width:100%;
		z-index:10000;
	}
	.timeout .helper{
		display:inline-block;
		height:100%;
		vertical-align:middle;
	}
	.timeout > div {
		background-color: #fff;
		box-shadow: 10px 10px 60px #555;
		display: inline-block;
		height: auto;
		max-width: 551px;
		min-height: 100px;
		vertical-align: middle;
		width: 60%;
		position: relative;
		border-radius: 8px;
		padding: 15px 5%;
	}
	/*.popupCloseButton {
		background-color: #fff;
		border: 3px solid #999;
		border-radius: 50px;
		cursor: pointer;
		display: inline-block;
		font-family: arial;
		font-weight: bold;
		position: absolute;
		top: -20px;
		right: -20px;
		font-size: 25px;
		line-height: 30px;
		width: 30px;
		height: 30px;
		text-align: center;
	}
	.popupCloseButton:hover {
		background-color: #ccc;
	}*/
	.trigger_popup_fricc {
		cursor: pointer;
		font-size: 20px;
		margin: 20px;
		display: inline-block;
		font-weight: bold;
	}
/* Popup box BEGIN */
	</style>
	</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KSLM9S7"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php $this->load->view('layouts/header');?>

<?php echo $template['body']; ?>

<?php $this->load->view('layouts/footer'); ?>
	
<!-- Script files include -->
<script src="<?php echo asset_url('assets/js/include_file.js'); ?>"></script>
<!--  Plugin for Sweet Alert -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="<?php echo asset_url('assets/js/sweet_alert_function.js'); ?>"></script>

<?php if ( $this->uri->segment(1) == 'faq' ) { ?>
<script src="<?php echo asset_url('assets/js/faq.js'); ?>"></script>
<?php } ?>

<?php if ( $this->uri->segment(1) == 'faq' ) { ?>
<script src="<?php echo asset_url('assets/js/faq.js'); ?>"></script>
<?php } ?>

<?php if ( $this->uri->segment(1) == 'dashboard' ) { ?>
<!-- Fundraiders on dashboard -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.1/js/standalone/selectize.min.js'></script>
<script src="<?php echo asset_url('assets/js/jquery.validate.min.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/fundraiser.js'); ?>"></script>
<!-- -->
	
<script src="<?php echo asset_url('assets/js/dashboard.js'); ?>"></script>

<link rel='stylesheet' href='https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css'>
<script src='https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js'></script>
<script src='https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.js'></script>
<script src='https://cdn.datatables.net/buttons/2.1.0/js/buttons.bootstrap.min.js'></script>
<script type="text/javascript" src="<?php echo asset_url('assets/js/table.js'); ?>"></script>
<script type="text/javascript" src="<?php echo asset_url('assets/js/tableConfigs.js'); ?>"></script>
<?php } ?>

<!-- fetch notifications js -->
<?php if ($this->session->userdata('user_id') ) { ?>
<script src="<?php echo asset_url('assets/js/fetchNotification.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/idleTimeout.js'); ?>"></script>
<?php } ?>
<!-- fetch notifications js -->

<?php if ( $this->uri->segment(1) == '' ) { ?>
	<script type="text/javascript" src="<?php echo asset_url('assets/js/jquery.cslider.js'); ?>"></script>
	<script type="text/javascript">
		$(function() {
			
				$('#da-slider').cslider({
					autoplay	: false,
					bgincrement	: 50
				});
			
			});
	</script>
<script>
		$('.owl-carousel').owlCarousel({
			loop:true,
			margin:10,
			dots:true,
			animateIn: 'flipInX',
			stagePadding: 5,
			responsive:{
				0:{
					items:1
				},
				600:{
					items:1
				},
				1000:{
					items:3
				}
			}
		})
	</script>
<script src="<?php echo asset_url('assets/js/custom.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/jquery.flexslider.js'); ?>"></script>
<?php } ?>

<?php if ($this->uri->segment(4) != '' AND $this->uri->segment(2)=='show') { ?>
<script src="<?php echo asset_url('assets/js/owl.carousel.min.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/custom.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/jquery.flexslider.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/jquery.validate.min.js'); ?>"></script>

<?php } ?>


<?php if ( $this->uri->segment(1) == '' || ($this->uri->segment(1) == 'games' && $this->uri->segment(2) != 'create' && $this->uri->segment(2) != 'edit' && $this->uri->segment(4) =='')) { ?>
<script src="<?php echo asset_url('assets/js/game_list.js'); ?>"></script>
<?php } ?>

<?php if ( ($this->uri->segment(1) == 'games' && $this->uri->segment(4) !='')) { ?>
<script src="<?php echo asset_url('assets/js/game_detail.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/timezones.full.js') ?>"></script>
<script src="<?php echo asset_url('assets/js/flag.js'); ?>"></script>
<?php } ?>

<?php if ($this->uri->segment(1) == 'buycredits') { ?>
<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<script src="<?php echo asset_url('assets/js/buy_credits.js'); ?>"></script>
<?php } ?>

<?php if ( $this->uri->segment(1) == 'cashout') { ?>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

<script src="<?php echo asset_url('assets/js/cash_credits.js'); ?>"></script>
<?php } ?>



<?php if ( $this->uri->segment(1) == 'login' || $this->uri->segment(1) == 'register' || $this->uri->segment(1) == 'reset' || $this->uri->segment(1) == 'reset_verify' || $this->uri->segment(1) == 'profile') { ?>
<!--   Core JS Files   -->
	<script src="<?php echo asset_url('assets/js/jquery-2.2.4.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo asset_url('assets/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo asset_url('assets/js/jquery.bootstrap.wizard.js'); ?>" type="text/javascript"></script>

	<!--  Plugin for the Wizard -->
	<script src="<?php echo asset_url('assets/js/gsdk-bootstrap-wizard.js'); ?>"></script>

	<!--  More information about jquery.validate here: http://jqueryvalidation.org/	 -->
	<script src="<?php echo asset_url('assets/js/jquery.validate.min.js'); ?>"></script>


<?php } ?>

<?php if ( $this->uri->segment(1) == 'login' || $this->uri->segment(1) == 'register' || $this->uri->segment(1) == 'reset_verify' || $this->uri->segment(1) == 'profile') { ?>

<script src="<?php echo asset_url('assets/js/password-validate-toggle.js'); ?>"></script>

<?php } ?>



<!-- Selectize plugin head elements -JS -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.1/js/standalone/selectize.min.js'></script>


<?php if ($this->session->flashdata('message')) { ?>
	<?php if ($this->session->flashdata('confirm')) { ?>
		<script type="text/javascript">
			showSweetUserConfirm('<?php echo $this->session->flashdata('message');?>', '<?php echo $this->session->flashdata('prompt_title');?>', '<?php echo $this->session->flashdata('icon');?>');
		</script>
	<?php } else { ?>
		<script type="text/javascript">
			showSweetAlert('<?php echo $this->session->flashdata('message');?>', '<?php echo $this->session->flashdata('prompt_title');?>', '<?php echo $this->session->flashdata('icon');?>');
		</script>
	<?php } ?>
<?php } ?>


<?php if ( $this->uri->segment(1) == 'transactions') { ?>
<link rel='stylesheet' href='https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css'>

<script src='https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js'></script>
<script src='https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.js'></script>
<script src='https://cdn.datatables.net/buttons/2.1.0/js/buttons.bootstrap.min.js'></script>
<script type="text/javascript" src="<?php echo asset_url('assets/js/tableConfigs.js'); ?>"></script>
<script type="text/javascript" src="<?php echo asset_url('assets/js/table.js'); ?>"></script>
<?php }?>

<?php if ( $this->uri->segment(1) == 'profile') { ?>
<script src="<?php echo asset_url('assets/js/bootstrap-tagsinput.js'); ?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/bootstrap-tagsinput.css'); ?>" />
<script type="text/javascript">
	$('.tags').tagsinput({
		unique: true
	})
</script>
<script src="<?php echo asset_url('assets/js/profile-validate.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/fundraiser.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/form-unload-confirmation.js'); ?>"></script>
<?php $this->load->view('home/w9_modal');?>
<?php }?>

<?php if ( $this->uri->segment(1) == 'fundraisers' || ($this->uri->segment(1) == 'fundraisers' && $this->uri->segment(2) == 'show')) { ?>
<script src="<?php echo asset_url('assets/js/jquery.validate.min.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/fundraiser.js'); ?>"></script>
<?php } ?>

<?php if ( $this->uri->segment(1) == 'admin') { ?>
  <link rel='stylesheet' href='https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css'>
  <script src='https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js'></script>
  <script src='https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js'></script>
  <script src='https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.js'></script>
  <script src='https://cdn.datatables.net/buttons/2.1.0/js/buttons.bootstrap.min.js'></script>
  <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/css/bootstrap4-toggle.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url('assets/js/adminTableConfigs.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo asset_url('assets/js/table.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo asset_url('assets/js/admin.js'); ?>"></script>
  
  <?php if ( $this->uri->segment(2) == 'faq' ) { ?>
		<script src="<?php echo asset_url('assets/js/editFAQ.js'); ?>"></script>
	<?php } ?>
<?php } ?>

<?php if ( $this->uri->segment(1) == 'challenge') { ?>
  <link rel='stylesheet' href='https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css'>
  <script src='https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js'></script>
  <script src='https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js'></script>
  <script src='https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.js'></script>
  <script src='https://cdn.datatables.net/buttons/2.1.0/js/buttons.bootstrap.min.js'></script>
  <script type="text/javascript" src="<?php echo asset_url('assets/js/tableConfigs.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo asset_url('assets/js/table.js'); ?>"></script>
<?php } ?>

<?php if ($this->uri->segment(2) == 'create' || $this->uri->segment(2) == 'edit') { ?>
	<script src="<?php echo asset_url('assets/js/bootstrap-tagsinput.js'); ?>"></script>
	<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/bootstrap-tagsinput.css'); ?>"/>
	<script type="text/javascript">
		$('.tags').tagsinput({
			unique: true
		})
	</script>
	<script src="<?php echo asset_url('assets/js/jquery.validate.min.js'); ?>"></script>

	<script src="<?php echo asset_url('assets/js/create_game.js'); ?>"></script>
	<script src="<?php echo asset_url('assets/js/form-unload-confirmation.js'); ?>"></script>
	<link rel='stylesheet' href='https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css'>
	<script src='https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js'></script>
	<script src='https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js'></script>
	<script src='https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.js'></script>
	<script src='https://cdn.datatables.net/buttons/2.1.0/js/buttons.bootstrap.min.js'></script>
	<script src="<?php echo asset_url('assets/js/profile-validate.js'); ?>"></script>
	<script src="<?php echo asset_url('assets/js/fundraiser.js'); ?>"></script>

	<script type="text/javascript" src="<?php echo asset_url('assets/js/tableConfigs.js'); ?>"></script>
  	<script type="text/javascript" src="<?php echo asset_url('assets/js/table.js'); ?>"></script>
<?php } ?>

<?php if ($this->uri->segment(2) == 'create' || $this->uri->segment(2) == 'show' || $this->uri->segment(2) == 'edit') { ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="<?php echo asset_url('assets/js/dateTimePicker.js') ?>"></script>
<script src="<?php echo asset_url('assets/js/timezones.full.js') ?>"></script>

<?php } ?>

<?php if ( $this->uri->segment(1) == 'games' && $this->uri->segment(2) == 'review') { ?>
  <link rel='stylesheet' href='https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css'>
  <script src='https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js'></script>
  <script src='https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js'></script>
  <script src='https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.js'></script>
	<script src='https://cdn.datatables.net/buttons/2.1.0/js/buttons.bootstrap.min.js'></script>
  
  <script type="text/javascript" src="<?php echo asset_url('assets/js/tableConfigs.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo asset_url('assets/js/table.js'); ?>"></script>
<?php } ?>

<?php if(isset($_SESSION["nu"]) && $_SESSION["nu"]  === '1') { 
        unset($_SESSION["nu"]); 
        $this->load->view('home/new_registration_modal'); 
} ?>

<?php $this->load->view('home/feedback_modal');?>
<script src="<?php echo asset_url('assets/js/feedback_modal.js'); ?>"></script>

<script src="<?php echo asset_url('assets/js/donation.js'); ?>"></script>


<?php $this->load->view('home/terms_modal');?>
<?php $this->load->view('home/policy_modal');?>

  <!-- Tinymce Editor plugin -->
  <!-- <script src="https://cdn.tiny.cloud/1/dxatm899ntqwhv2wierx8anwwddkjao2g60huha0a83y3rah/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> -->
  <script src="<?php echo asset_url('assets/tinymce/tinymce.min.js'); ?>"></script>
  <script src="<?php echo asset_url('assets/js/tinycustom.js'); ?>"></script>
  <link rel="stylesheet" href="<?php echo asset_url('assets/css/tinymce.css'); ?>">

</body>



</html>
