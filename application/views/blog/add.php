<content class="content">
   <section>
	   <div class="container">
            <form id="article-form" method="POST" enctype="multipart/form-data" novalidate="novalidate">
                <div class="col">
                    <div class="row titleBar">
                        <div class="col">
                            <div class="row reverse">
                                <div class="adminblogbtns">
                                    <div class="text-right"><button id="addBlogPost" type="button" name="add" class="btn blue">Add <i class="fa fa-plus"></i></button></div>
                                    <div class="text-right"><button data-url='<?php echo asset_url("blog"); ?>' type="button" id="cancelChanges" class="btn blue">Cancel <i class="fa fa-ban"></i></button></div>
                                </div>
                                <h1 id="title" data-id="<?php ; ?>"> <input class='articleTitle' type='text' name='title' value='Blog Post Title'> </h1>
                            </div>

                            <div class="row">
                                <div class="col nopadding">
                                    <h4 class="author"> By: <?php echo $user[0]["firstname"]; echo $user[0]["lastname"]; ?></h4>
                                    <span class="dateCreated"> on <?php $date = new DateTime(); echo $date->format("m/d/y H:i:s")  ?></span>
                                    <span class="share">
                                        <a class="btn-share" title="Share" href=""><i class="fa fa-share-alt"></i></a>
                                        <label for="shorten_url">
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
                                <img src='https://neilpatel.com/wp-content/uploads/2018/10/blog.jpg	'/>
                                <div class='row'>
                                    <div id='articleImage' class='col'>
                                        <input type='file' id='imageUpload' accept='.png, .jpg, .jpeg' name='articleImage_img_path' class='newsArticleUploader' preview-at='.featuredImageWrapper img'>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col ml-3 pl-3">
                                <div id="excerptContent" class="row">
                                    <div id="excerptContent" class="col">
                                        <textarea class='form-control excerptEditor answer-text' name='add_excerpt' rows='10' cols='30' id='add_excerpt' placeholder='Write a short summary here..' required></textarea>
                                        <label class='error' for='add_excerpt' id='add_excerpt-error' style='display: none;'></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="article">
                            <textarea class='form-control newsEditor answer-text' name='add_blog' rows='10' cols='30' id='add_blog' placeholder='Write your news article here..' required></textarea>
                            <label class='error' for='add_blog' id='add_blog-error' style='display: none;'></label>
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

<script src=<?php echo asset_url("assets/js/addBlog.js"); ?>></script>