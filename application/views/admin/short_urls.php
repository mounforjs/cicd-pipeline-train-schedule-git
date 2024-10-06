<!-- Content Start -->
<content class="content adminpage">
   <div class="container-fluid">

      <div class="row">
         <div class="col-12 p-4">
            <h1>Manage Shortened URLs</h1>
            <form id="short_url-form" autocomplete="off">
                <input type="hidden" id="selected_short" name="selected_short"/>
                <div class="row justify-content-between p-2">
                    <div class="col-lg-8">
                        <div class="row p-0">
                            <div class="col"><input type="url" class="form-control" id="original-url" name="original-url" placeholder="<?php echo base_url(); ?>this/is/a/long/url/" required></div>
                            <div class="col"><input type="url" class="form-control" id="short-url" name="short-url" placeholder="<?php echo base_url(); ?>shorturl" required></div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <button class="btn" id="addShortUrl" type="button" ><i class="fas fa-plus-circle"></i> New</button>
                        <button class="btn orange d-none" id="editShortUrl" type="button" disabled><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                        <button class="btn red d-none" id="deleteShortUrl" type="button" disabled><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                    </div>
                </div>
            </form>
            <div class="carddivider"></div>
            <input id="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
            <table id="shortURLTable" class="table table-striped table-bordered dt-responsive nowrap selectHover" width="100%" data-type="short_urls">
            <thead>
                <tr>
                    <th>Creator</th>
                    <th>URL</th>
                    <th>Short URL</th>
                    <th>Created at</th>
                </tr>
                </thead>
                    <tbody>
                    <?php 
                    foreach ($short_urls as $key => $row) :
                    ?>
                    <tr class="select" data-id="<?php echo $row->id; ?>">
                        <td>
                            <?php echo $row->user_id . " - " . $row->username; ?>
                        </td>
                        <td>
                            <a href="<?php echo base_url($row->url); ?>" target="_blank"><?php echo $row->url; ?></a>
                        </td>
                        <td>
                            <a href="<?php echo base_url($row->short_url); ?>" target="_blank"><?php echo $row->short_url; ?></a>
                        </td>
                        <td>
                            <?php echo $row->created_at; ?>
                        </td>
                                                
                    </tr>
                <?php  endforeach; ?>

            </tbody>
            </table>
         </div>
      </div>
   </div>
   </div>
   </div>
</content>
<!-- Content End -->
