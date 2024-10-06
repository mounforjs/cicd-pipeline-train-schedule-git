
<!-- Content Start -->
<content class="content adminpage">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12 p-4">
                <h1>Manage Blog Posts</h1>
                <div class="row justify-content-between p-2">
                    <div class="col-lg-8"></div>
                    <div class="col-lg-4 text-right">
                        <a href="<?php echo asset_url('blog')?>" class="btn blue">View All</a>
                    </div>
                </div>

                <table id="newsArticleTable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" >
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Excerpt</th>
                            <th>Text</th>
                            <th>Author</th>
                            <th>Published</th>
                            <th>Date Created</th>
                            <th>Date Edited</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($posts as $key => $post) :
                                $image="";
                                if ($post['featured_image']) {
                                    $file = $post['featured_image'];
                                    $file_headers = @get_headers($file);

                                    if ($file_headers[0] == 'HTTP/1.0 404 Not Found') {
                                        $image = "https://dg7ltaqbp10ai.cloudfront.net/1605360274_R2D2_icon.png";
                                    } else {
                                        $image = $post['featured_image'];
                                    }
                                } else {
                                    $image = "https://dg7ltaqbp10ai.cloudfront.net/1605360274_R2D2_icon.png";
                                }
                        ?>
                            <tr>
                            <td id='img_<?php echo $post['id'] ?>' class="crd">
                                <img width="50px" height="50px" src='<?php echo $image; ?>' alt="<?php echo $post["title"]?>">
                            </td>

                            <td id='title_<?php echo $post['id'] ?>' class="crd">
                                <?php echo $post["title"]?>
                            </td>
                            <td id='slug_<?php echo $post['id']?>' class="crd">
                                <?php echo $post["slug"]?>
                            </td>
                            <td id='exrpt_<?php echo $post['id']?>' class="crd">
                                <?php echo $post["excerpt"]?>
                            </td>
                            <td id='text_<?php echo $post['id']?>' class="crd newsLimit">
                                <?php echo $post["text"]?>
                            </td>

                            <td id='authr_<?php echo $post['id']?>' class="crd">
                                <?php echo $post["firstname"] . " " . $post["lastname"]; ?>
                            </td>

                            <td id='pub_<?php echo $post['id']?>' class="crd">
                                <?php if ($post['published']== 1) { ?>
                                    <i class="fa fa-circle published" aria-hidden="true"></i> 
                                <?php } else { ?>
                                    <i class="fa fa-circle-thin unpublished" aria-hidden="true"></i> 
                                <?php } ?>
                            </td>
                            
                            <td id='create_<?php echo $post['id']?>' class="crd">
                                <?php echo $post['DATE_CREATED'] ?>
                            </td>

                            <td id='edit_<?php echo $post['id']?>' class="crd">
                                <?php echo $post['DATE_EDITED'] ?>
                            </td>

                            <td>
                                <a href="<?php echo asset_url('blog') . '/post/' . $post["slug"]?>" class="btn blue" value="<?php echo $post['id']; ?>">Edit <i class="fa fa-pencil"></i></a>
                            </td>
                            </tr>
                        <?php  endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</content>
<!-- Content End -->
