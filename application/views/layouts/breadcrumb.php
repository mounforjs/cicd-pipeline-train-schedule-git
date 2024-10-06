<?php 
	$segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
	if ((isset($segments[0]) && $segments[0] =='games') && (isset($segments[1]) && $segments[1] == 'show') && (!isset($segments[2]) || (isset($segments[2]) && $segments[2]!== '')) && ((!isset($segments[3]) || (isset($segments[3]) && $segments[3]== '')))) {
?>

<div class="container-lg breadcontainer">
	<div class="row">
		<div id="orangeUI">
			<div class="breadcrumbs">
				<ul>
					<?php foreach($template['breadcrumbs'] as $index=>$breadcrumb) {?>
					<li><a href="<?php echo $breadcrumb['uri'];?>" class="<?php if ( key(array_slice($template['breadcrumbs'], -1, 1, true)) == $index) echo 'current';?>"><?php echo ucfirst($breadcrumb['name']);?></a></li>
			        <?php }?>
				</ul>
			</div>
		</div>
	</div>
</div>

 <?php } ?>