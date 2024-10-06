<content class="content">
   <section>
	   <div class="container">
            <div class="col">
               <?php 
               if ($admin) { ?>
                  <div class="row float-right">
                     <div class="text-right"><a href="<?php echo asset_url('blog/add'); ?>" class="btn blue">New <i class="fa fa-plus"></i></a></div>
                  </div>
                  <br>
               <?php } ?>

               <div class="col">
                  <div class="col">
                     <br>
                     <h1>Blog</h1>
                     <br>
                  </div>

                  <?php if (count($blog_posts) != 0) { ?>
                     <?php for ($i = 0; $i < count($blog_posts); $i++) { ?>
                        <a href="<?php echo asset_url('blog') . '/post/' . $blog_posts[$i]["slug"]?>">
                           <div class="row newsArticle">
                              <div class="col nopadding featuredImageWrapper">
                                 <img src='<?php echo $blog_posts[$i]["featured_image"]; ?>' alt="<?php echo $blog_posts[$i]["title"]; ?>">
                              </div>                           
                              <div class="col mx-3 px-3">
                                 <div class="row">
                                    <div class="col-auto nopadding">
                                    <h1 class="newsArticleTitle"><?php echo $blog_posts[$i]["title"]; ?></h1>
                                       <?php if ($admin) { 
                                          if ($blog_posts[$i]["published"] == 0) { ?>
                                             <i class="fa fa-circle-thin unpublished" aria-hidden="true"></i> 
                                          <?php } else { ?> 
                                             <i class="fa fa-circle published" aria-hidden="true"></i> 
                                          <?php } ?>
                                       <?php } ?>
                                       <span class="dateCreated"><?php $date = new DateTime($blog_posts[$i]["DATE_CREATED"]); echo date_format($date, "m/d/Y"); ?></span>
                                    </div>
                                    <div class="col nopadding">
                                       <?php if ($admin) { ?>
                                          <div class="col float-right">
                                             <div class="py-2 text-right">
                                                <button class="btn blue" onclick='function() {window.location.href="<?php echo asset_url('blog') . '/post/' . $blog_posts[$i]["slug"]?>";}'>Edit <i class="fa fa-pencil"></i>
                                                </button>
                                             </div>
                                          </div>
                                       <?php } ?> 
                                    </div>
                                 </div>

                                 <div class="row">
                                    <p class="excerpt"><?php echo strip_tags($blog_posts[$i]["excerpt"]); ?> Read more...</p>
                                    
                                 </div>
                              </div>
                           </div>
                        </a>
                        <br>
                     <?php } ?>
                  <?php } else { ?>
                     <center><h2>No blog posts available.</h2></center>
                  <?php } ?>
            </div>
            <br>
   </section>
</content>