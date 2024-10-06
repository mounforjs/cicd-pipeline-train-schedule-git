<content class="content">
   <section>
	   <div class="container">
            <?php if ($admin) { ?>
            <form id='blog-form' method='POST' enctype='multipart/form-data' novalidate='novalidate'>
            <?php } ?>
            <div id="blog" class="col">
                <div class="row titleBar">
                    <div class="col">
                        <div class="row <?php echo ($admin) ? "reverse" : ""; ?>">
                            <?php if ($admin) { ?>
                                <div class="adminblogbtns">
                                    <div class="ml-auto"><button id="applyChanges" type="button" name="edit" class="btn blue" style="display: none">Apply <i class="fa fa-share"></i></button></div>
                                    <div class="ml-auto"><button id="cancelChanges" type="button" name="edit" class="btn blue" style="display: none">Cancel <i class="fa fa-ban"></i></button></div>
                                    <div class="ml-auto"><button id="deleteBlogPost" type="button" name="edit" class="btn blue">Delete <i class="fa fa-trash-o"></i></button></div>
                                    <div class="ml-auto"><button id="editBlogPost" type="button" name="edit" class="btn blue">Edit <i class="fa fa-pencil"></i></button></div>
                                </div>
                            <?php } ?>
                            <h1 id="title" data-id="<?php echo $blog_post[0]["id"]; ?>"> <?php echo $blog_post[0]["title"]; ?> </h1>
                        </div>

                        <div class="row">
                            <div class="col nopadding">
                                <?php if ($admin) { 
                                    if ($blog_post[0]["published"] == 0) { ?>
                                        <i class="fa fa-circle-thin unpublished" aria-hidden="true"></i> 
                                    <?php } else { ?> 
                                        <i class="fa fa-circle published" aria-hidden="true"></i> 
                                    <?php } ?>
                                <?php } ?>

                                <h4 class="author"> By: <?php echo $blog_post[0]["firstname"] . " " . $blog_post[0]["lastname"]; ?></h4>
                                <span class="dateCreated">  on <?php $date = new DateTime($blog_post[0]["DATE_CREATED"]); echo date_format($date, (($admin) ? "m/d/Y H:i:s" : "m/d/Y")); ?></span>
                                <?php if ($admin && isset($blog_post[0]["DATE_EDITED"])) { ?>
                                    <span class="dateCreated"> (edited on <?php $date = new DateTime($blog_post[0]["DATE_EDITED"]); echo date_format($date, "m/d/Y H:i:s"); ?>)</span>
                                <?php } ?>
                                <span class="share"><a class="btn-share" title="Share" data-short="<?php echo (isset($blog_post[0]["short_url"]) ? true : ""); ?>" href="<?php echo base_url(((isset($blog_post[0]["short_url"]) ? $blog_post[0]["short_url"] : ("blog/post/" . $blog_post[0]["slug"])))); ?>"><i class="fa fa-share-alt"></i></a></span>

                                <?php if ($admin) { ?>
                                    <div id="published" style="display: none">
                                        <label for="published">Published: <input name="published" type="radio" <?php echo ($blog_post[0]['published'] == 1) ? "checked='checked'" : ""; ?> value="1"></label> - 
                                        <label for="unpublished">Unpublished: <input name="unpublished" type="radio" <?php echo ($blog_post[0]['published'] == 1) ? "" : "checked='checked'"; ?> value="0"></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php if ($admin) { ?>
                        <div id="excerpt" style="display: none;">
                            <div class="col-sm-3 nopadding featuredImageWrapper">
                                <img src='<?php echo $blog_post[0]["featured_image"]; ?>' alt="<?php echo $blog_post[0]["title"]; ?>">
                            </div>
                            
                            <div class="col ml-3 px-3">
                                <div id="excerptContent" class="row">
                                    <div class="col">
                                        <?php echo $blog_post[0]["excerpt"]; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div id="post">
                        <?php echo $blog_post[0]["text"]; ?>
                    </div>
                </div>
                <br>
            </div>
            <?php if ($admin) { ?>
                </form>
                <br>
            <?php } ?> 

            <div class="col">
                <div class="row">
                    <h3><a href="<?php echo asset_url("blog"); ?>">Back</a></h3>
                </div>
            </div>
            <br>
        </div>
   </section>
</content>

<?php if ($admin) { ?>
    <script src=<?php echo asset_url("assets/js/editBlog.js"); ?>></script>
<?php } ?>