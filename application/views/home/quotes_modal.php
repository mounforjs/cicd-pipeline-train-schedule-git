
<div id="carouselContent" class="carousel slide" data-ride="carousel">
	<div class="carousel-inner" role="listbox">
			
	<?php $i=1; 
	$quotes_list = quotes_list();
	 foreach ($quotes_list as $key => $quote) {?>

	  <div class="carousel-item <?php if ($i==1)echo 'active'?> text-center p-4"><?php $i++;?>
		<p id="quote-description" style="font-weight: bold"><?php echo $quote->description; ?></p>
		<p id="quote-source"><font style="color:#990000; font-size:14px; text-transform: uppercase; font-weight: bold; font-style:italic"><?php echo $quote->source; ?></font></p>

	 
	  </div>
	<?php }?>

				
	</div>
	
</div>
