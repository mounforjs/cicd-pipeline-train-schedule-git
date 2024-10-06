
<!-- Content Start -->
<content class="content adminpage">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12 p-4">
                <h1>Manage News Articles</h1>
                <div class="row justify-content-between p-2">
                    <div class="col-lg-8"></div>
                    <div class="col-lg-4 text-right">
                        <a href="<?php echo asset_url('news')?>" class="btn blue">View All</a>
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
                        foreach ($articles as $key => $article) :
                                $image="";
                                if ($article['featured_image']) {
                                    $file = $article['featured_image'];
                                    $file_headers = @get_headers($file);

                                    if ($file_headers[0] == 'HTTP/1.0 404 Not Found') {
                                        $image = "https://dg7ltaqbp10ai.cloudfront.net/1605360274_R2D2_icon.png";
                                    } else {
                                        $image = $article['featured_image'];
                                    }
                                } else {
                                    $image = "https://dg7ltaqbp10ai.cloudfront.net/1605360274_R2D2_icon.png";
                                }
                        ?>
                            <tr>
                            <td id='img_<?php echo $article['id'] ?>' class="crd">
                                <img width="50px" height="50px" src='<?php echo $image; ?>' alt="<?php echo $article["title"]?>">
                            </td>

                            <td id='title_<?php echo $article['id'] ?>' class="crd">
                                <?php echo $article["title"]?>
                            </td>
                            <td id='slug_<?php echo $article['id']?>' class="crd">
                                <?php echo $article["slug"]?>
                            </td>
                            <td id='exrpt_<?php echo $article['id']?>' class="crd">
                                <?php echo $article["excerpt"]?>
                            </td>
                            <td id='text_<?php echo $article['id']?>' class="crd newsLimit">
                                <?php echo $article["text"]?>
                            </td>

                            <td id='authr_<?php echo $article['id']?>' class="crd">
                                <?php echo $article["firstname"] . " " . $article["lastname"]; ?>
                            </td>

                            <td id='pub_<?php echo $article['id']?>' class="crd">
                                <?php if ($article['published']== 1) { ?>
                                    <i class="fa fa-circle published" aria-hidden="true"></i> 
                                <?php } else { ?>
                                    <i class="fa fa-circle-thin unpublished" aria-hidden="true"></i> 
                                <?php } ?>
                            </td>
                            
                            <td id='create_<?php echo $article['id']?>' class="crd">
                                <?php echo $article['DATE_CREATED'] ?>
                            </td>

                            <td id='edit_<?php echo $article['id']?>' class="crd">
                                <?php echo $article['DATE_EDITED'] ?>
                            </td>

                            <td>
                                <a href="<?php echo asset_url('news') . '/article/' . $article["slug"]?>" class="btn blue" value="<?php echo $article['id']; ?>">Edit <i class="fa fa-pencil"></i></a>
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
