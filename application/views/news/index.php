<content class="content">
   <section>
	   <div class="container">
            <div class="col">
               <?php 
               if ($admin) { ?>
                  <div class="row float-right">
                     <div class="text-right"><a href="<?php echo asset_url('news/add'); ?>" class="btn blue">New <i class="fa fa-plus"></i></a></div>
                  </div>
                  <br>
               <?php } ?>

               <div class="col">
                  <div class="col">
                     <br>
                     <h1>News</h1>
                     <br>
                  </div>

                  <?php if (count($articles) != 0) { ?>
                     <?php for ($i = 0; $i < count($articles); $i++) { ?>
                        
                        <a href="<?php echo asset_url('news') . '/article/' . $articles[$i]["slug"]?>">
                           <div class="row newsArticle">
                              <div class="col nopadding featuredImageWrapper">
                                 <img src='<?php echo $articles[$i]["featured_image"]; ?>' alt="<?php echo $articles[$i]["title"]; ?>">
                              </div>
                                 
                              <div class="col mx-3 px-3">
                                 <div class="row">
                                    <div class="col nopadding">
                                       <h1 class="newsArticleTitle"> <?php echo $articles[$i]["title"]; ?></h1>
                                       <?php if ($admin) { 
                                          if ($articles[$i]["published"] == 0) { ?>
                                             <i class="fa fa-circle-thin unpublished" aria-hidden="true"></i> 
                                          <?php } else { ?> 
                                             <i class="fa fa-circle published" aria-hidden="true"></i> 
                                          <?php } ?>
                                       <?php } ?>
                                       <span class="dateCreated"><?php $date = new DateTime($articles[$i]["DATE_CREATED"]); echo date_format($date, "m/d/Y"); ?></span>
                                    </div>
                                    <div class="col nopadding">
                                       <?php if ($admin) { ?>
                                          <div class="col float-right">
                                             <div class="py-2 text-right">
                                                <button class="btn blue" onclick='function() {window.location.href="<?php echo asset_url('news') . '/article/' . $articles[$i]["slug"]?>";}'>Edit <i class="fa fa-pencil"></i>
                                                </button>
                                             </div>
                                          </div>
                                       <?php } ?> 
                                    </div>
                                 </div>

                                 <div class="row">
                                    <p class="excerpt"><?php echo strip_tags($articles[$i]["excerpt"]); ?> Read more...</p>
                                    
                                 </div>
                              </div>
                           </div>
                        </a>
                        <br>
                     <?php } ?>
                  <?php } else { ?>
                     <center><h2>No articles available.</h2></center>
                  <?php } ?>
            </div>
            <br>
   </section>
</content>