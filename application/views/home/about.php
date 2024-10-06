<div class="container-fluid">
   <div class="row" style="background-image: url(https://dg7ltaqbp10ai.cloudfront.net/fit-in/1598032781-aboutbg.jpg); background-size: cover;">
      <div class="col-lg-6"></div>
      <div class="col-lg-6">
         <div class="about-mission">
            <h2>Our Mission</h2>
            <p>We created a unique fundraising system to help people and organizations thrive in good times and bad. We fund other non-profits, innovative projects and ideas, personal causes and educational opportunities, for the benefit of all.</p>
            <a href="<?php echo asset_url('mission'); ?>" class="btn">Learn More <i class="fas fa-chevron-right"></i></a>
         </div>
      </div>
   </div>
</div>
<content class="content">
   <div class="container">
      <div class="row">
         <div class="col-lg-12">
            <br>
            <br>
            <h1 class="text-center">Learn About Our Operations</h1>
            <br>
            <div class="container">
               <div class="row report">
                  <div class="col-sm-12 col-lg-4 p-2">
                     <h2>
                        GOVERNANCE <!-- <a href="#">SEE ALL  <i class="fas fa-chevron-right"></i></a> -->
                     </h2>
                     <ul class="fa-ul iconlist">
						 <li><a href="<?php echo asset_url("board"); ?>"><span class="fa-li"><i class="fa fa-users"></i></span> Board of Directors</a></li>
						 <li><a href="assets/pdfs/ConflictofInterestPolicy.pdf" target="_blank"><span class="fa-li"><i class="fas fa-file-pdf"></i></span> Conflict of Interest Policy</a></li>
						 <li><a href="assets/pdfs/WinWinLabsBylaws20201209.pdf" target="_blank"><span class="fa-li"><i class="fas fa-file-pdf"></i></span> By-Laws of WinWinLabs</a></li>
                   <li><a href="assets/pdfs/Form_990-N.pdf" target="_blank"><span class="fa-li"><i class="fas fa-file-pdf"></i></span> Tax Exempt Organization</a></li>
					  </ul>
					  
                     <!-- <a href="<?php echo asset_url('governance'); ?>">See More</a> -->
                  </div>

                  <div class="col-sm-12 col-lg-5 p-2">
                     <h2>
                        RECENT NEWS <!-- <a href="#">SEE ALL  <i class="fas fa-chevron-right"></i></a> -->
                     </h2>
                     <?php if (count($article) == 1) { ?>
                        <a href="<?php echo asset_url('news') . '/article/' . $article[0]["slug"]?>">
                           <div class="row aboutArticle">
                              <div class="col nopadding aboutImgWrapper">
                                 <img src="<?php echo $article[0]["featured_image"]; ?>" alt="<?php echo $article[0]["title"]; ?>">
                              </div>
                                 
                              <div class="col mx-3 px-3">
                                 <div class="row">
                                    <div class="col nopadding">
                                       <h3 class="aboutArticleTitle"> <?php echo $article[0]["title"]; ?></h3>
                                       <small><?php $date = new DateTime($article[0]["DATE_CREATED"]); echo date_format($date, "m/d/Y"); ?></small>
                                    </div>
                                 </div>

                                 <div class="row">
                                    <p class="line3"><?php echo strip_tags($article[0]["excerpt"]); ?></p>
                                 </div>
                              </div>
                           </div>
                        </a>
                     <?php } else { ?>
                        <h3 class="text-center">No articles available.</h3>
                     <?php } ?>

                     <br>
                     <center>
                        <a href="<?php echo asset_url("news"); ?>" class="btn">See More  <i class="fas fa-chevron-right" aria-hidden="true"></i></a>
                     </center>
                  </div>

                  <div class="col-sm-12 col-lg-3 p-2">
                     <h2>HISTORY</h2>
                     <p class="line6">We began as a small team of passionate <i>FIRST</i> Robotics ® mentors, alumni and students...</p>
                     <center>
                        <a href="<?php echo asset_url('history'); ?>" class="btn">Read More  <i class="fas fa-chevron-right"></i></a>
                     </center>
                  </div>
                  <br>
               </div>
            </div>
         </div>
      </div>
      <br><br>
   </div>
</content>
