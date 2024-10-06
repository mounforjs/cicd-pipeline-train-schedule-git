<content class="content">
   <section>
	   <div class="container">
            <form id="article-form" method="POST" enctype="multipart/form-data" novalidate="novalidate">
                <div class="col">
                    <div class="row titleBar">
                        <div class="col">
                            <div class="row reverse">
                                <div class="adminblogbtns">
                                    <div class="ml-auto"><button id="addArticle" type="button" name="add" class="btn blue">Add <i class="fa fa-plus"></i></button></div>
                                    <div class="ml-auto"><button data-url='<?php echo asset_url("news"); ?>' id="cancelChanges" type="button" class="btn blue">Cancel <i class="fa fa-ban"></i></button></div>
                                </div>
                                <h1 id="title" data-id="<?php ; ?>"> <input class='articleTitle' type='text' name='title' value='Article Title'> </h1>
                            </div>

                            <div class="row">
                                <div class="col nopadding">
                                    <h4 class="author"> By: <?php echo $user[0]["firstname"]; echo $user[0]["lastname"]; ?></h4>
                                    <span class="dateCreated"> on <?php $date = new DateTime(); echo $date->format("m/d/y H:i:s")  ?></span>
                                    <span class="share">
                                        <a class="btn-share" title="Share" href=""><i class="fa fa-share-alt"></i></a>
                                        <label for="name">
                                            <?php echo base_url(); ?>
                                            <input class="shorturl" id="shorten_url" name="shorten_url"type="text" value="" placeholder=""/>
                                        </label>
                                    </span>

                                    <div id="published" class="float-right">
                                        <label for="published">Published: </label><input name="published" type="radio" value="1"> - 
                                        <label for="unpublished">Unpublished: </label><input name="unpublished" type="radio" checked="checked" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div id="excerpt">
                            <div class='col nopadding featuredImageWrapper'>
                                <img src='https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/news.png' alt="NEWS"/>
                                <div class='row'>
                                    <div id='articleImage' class='col'>
                                        <input type='file' id='imageUpload' accept='.png, .jpg, .jpeg' name='articleImage_img_path' class='newsArticleUploader' preview-at='.featuredImageWrapper img'>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col mx-3 px-3">
                                <div id="excerptContent" class="row">
                                    <div class="col">
                                        <textarea class="form-control excerptEditor answer-text" name="add_excerpt" rows="10" cols="30" id="add_excerpt" maxlength="500" placeholder='Write a short summary here..' required></textarea>
                                        <label class='error' for='add_excerpt' id='add_excerpt-error' style='display: none;'></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="article">
                            <textarea class="form-control newsEditor answer-text" name="add_article" rows="5" cols="30" id="add_article" maxlength="2000" placeholder='Write your news article here..' required></textarea>
                            <label class='error' for='add_article' id='add_article-error' style='display: none;'></label>
                        </div>
                    </div>
                    <br>
                </div>
            </form>

            <div class="col">
                <div class="row">
                    <h3><a href="<?php echo asset_url("news"); ?>">Back</a></h3>
                </div>
            </div>
            <br>
        </div>
   </section>
</content>

<script src=<?php echo asset_url("assets/js/addNews.js"); ?>></script>