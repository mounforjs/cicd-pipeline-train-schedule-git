<content class="content">
   <section>
	   
	   <!-- FAQ Keyword Search Start -->
	   <?php include 'filter.php'; ?>
	   <!-- FAQ Keyword Search End -->

	   <div class="container">
            <?php if ($this->session->userdata("user_id") && getprofile()->usertype=='2') { ?>
                    <div class="row float-right">
                        <div class="text-right"><a href="<?php echo asset_url('admin/faq'); ?>" class="btn blue">Modify<i class="fa fa-plus"></i></a></div>
                    </div>
            <?php } ?>
            <br>
            <h1>Frequently Asked Questions</h1>
            <br>
            <?php $count = 0; ?>
            <?php for ($i = 0; $i < count($faq); $i++) { ?>
                
                <?php if (count($faq[$i]['faq']) > 0 ) { ?>
                    <?php if ($count % 3 == 0) { ?>
                        <div class="row">
                    <?php } ?>

                    <div class="col-md-4 text-left">
                        <h2 class="faqul1"><?php echo ucfirst($faq[$i]['category']); ?></h2>
                        <ul class="list-group list-group-flush">
                        <?php for($j = 0; $j < count($faq[$i]['faq']); $j++) { ?>
                            <li class="list-group-item"><a href="<?php echo asset_url('faq/question/' . $faq[$i]['faq'][$j]['id']); ?>"><?php echo $faq[$i]['faq'][$j]['question']; ?></a></li>
                        <?php } ?>

                            <li class="list-group-item faqall"><a href="<?php echo asset_url('faq/category/'. $faq[$i]['id']); ?>">View all questions <i class="fas fa-arrow-circle-right"></i></a></li>
                        </ul>
                    </div>
                    
                    <?php if (($count+1) % 3 == 0) { ?>
                        </div>
                        <br /><br /><br />
                    <?php } $count++; ?>
                <?php } ?>
        <?php } ?>
        </div>
        <br /><br /><br />
   </section>
</content>