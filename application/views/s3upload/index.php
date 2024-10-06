<!DOCTYPE html>
<html>
<head>
    <title>Upload Image</title>
</head>
<body>

<?php if (isset($imgpath)) { ?>
    <p>Image uploaded successfully!</p>
    <p>Image URL: <?php echo $imgpath; ?></p>
    <img src="<?php echo $imgpath; ?>" alt="Uploaded Image">
<?php } ?>

<form action="<?php echo base_url('upload/upload_image'); ?>" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>

</body>
</html>