<content class="content">
   <section>
      <!-- FAQ Keyword Search Start -->
      <?php include "filter.php" ?>
      <!-- FAQ Keyword Search End -->
      <div class="container">
         <h1>FAQ - <?php echo ucfirst($faq[0]["category"]); ?></h1>
         <br>
            <div class="accordion" id="faqaccordion">
            <?php if (count($noSCategory[0]['faq']) > 0) { ?>
                <div class="card">
                    <div class="card-header" id="heading<?php echo $i; ?>">
                        <h2 class="mb-0">
                            <i class="fas fa-chevron-right"></i> <a type="button" data-toggle="collapse" data-target="#collapse-1" aria-expanded="false" aria-controls="collapse-1" class="collapsed">
                            Uncategorized
                            </a>
                        </h2>
                    </div>
                    <div id="collapse-1" class="collapse" aria-labelledby="heading-1" data-parent="#faqaccordion">
                        <div class="card-body">
                            <?php for($i = 0; $i < count($noSCategory[0]["faq"]); $i++) { ?>
                                <a class="categoryA" href="<?php echo asset_url('faq/question/' . $noSCategory[0]["faq"][$i]['id']); ?>"><h2><?php echo $noSCategory[0]["faq"][$i]['question']; ?></h2></a>
                            <?php } ?>
                            <br>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php for($i = 0; $i < count($faq[0]['subCategories']); $i++) { ?>
                <?php if (count($faq[0]['subCategories'][$i]['questions']) != 0) { ?>
                    <div class="card">
                        <div class="card-header" id="heading<?php echo $i; ?>">
                            <h2 class="mb-0">
                                <i class="fas fa-chevron-right"></i> <a type="button" data-toggle="collapse" data-target="#collapse<?php echo $i; ?>" aria-expanded="false" aria-controls="collapse<?php echo $i; ?>" class="collapsed">
                                <?php echo ucfirst($faq[0]['subCategories'][$i]['type']); ?>
                                </a>
                            </h2>
                        </div>
                        <div id="collapse<?php echo $i; ?>" class="collapse" aria-labelledby="heading<?php echo $i; ?>" data-parent="#faqaccordion">
                            <div class="card-body">
                                <?php for($j = 0; $j < count($faq[0]['subCategories'][$i]['questions']); $j++) { ?>
                                    <a class="categoryA" href="<?php echo asset_url('faq/question/' . $faq[0]['subCategories'][$i]['questions'][$j]['id']); ?>">
                                        <h4>
                                            <?php echo $faq[0]['subCategories'][$i]['questions'][$j]['question']; ?>
                                        </h4>
                                    </a>
                                <?php } ?>
                                <br>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
         </div>
      </div>

      <?php include "footer.php"; ?>
   </section>
</content>