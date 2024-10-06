<!-- Content Start -->
<content class="content adminpage">
    <div class="container-fluid">
        <h1 class="mt-5">Add Map</h1>
        <form action="<?php echo base_url('admin/insert_map');?>" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="MapName">Map Name:</label>
                        <input type="text" class="form-control" id="MapName" name="MapName" required>
                    </div>

                    <div class="form-group">
                        <label for="Zoom">Zoom:</label>
                        <input type="number" class="form-control" id="Zoom" name="Zoom" required>
                    </div>

                    <div class="form-group">
                        <label for="CenterLat">Center Latitude:</label>
                        <input type="text" class="form-control" id="CenterLat" name="CenterLat" required>
                    </div>

                    <div class="form-group">
                        <label for="CenterLon">Center Longitude:</label>
                        <input type="text" class="form-control" id="CenterLon" name="CenterLon" required>
                    </div>

                    <div class="form-group">
                        <label for="MapType">Map Type:</label>
                        <input type="text" class="form-control" id="MapType" name="MapType" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="GestureHandling">Gesture Handling:</label>
                        <input type="text" class="form-control" id="GestureHandling" name="GestureHandling">
                    </div>

                    <div class="form-group">
                        <label for="StreetViewControlPosition">Street View Control Position:</label>
                        <input type="text" class="form-control" id="StreetViewControlPosition" name="StreetViewControlPosition">
                    </div>

                    <div class="form-group">
                        <label for="ZoomControlPosition">Zoom Control Position:</label>
                        <input type="text" class="form-control" id="ZoomControlPosition" name="ZoomControlPosition">
                    </div>

                    <div class="form-group">
                        <label for="MapTypeControlPosition">Map Type Control Position:</label>
                        <input type="text" class="form-control" id="MapTypeControlPosition" name="MapTypeControlPosition">
                    </div>

                    <div class="form-group">
                        <label for="FullscreenControlPosition">Fullscreen Control Position:</label>
                        <input type="text" class="form-control" id="FullscreenControlPosition" name="FullscreenControlPosition">
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</content>