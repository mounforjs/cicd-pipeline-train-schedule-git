<!doctype html>
<html>

<head>
  <meta charset="UTF-8">
</head>

<body bgcolor="#0e2735" style="margin: 0px;">
  <table border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center" style="max-width: 600px;">
    <tbody>
      <tr> <?php $this->load->view('emails/email-header-image'); ?> </tr>
      <tr>
        <td style="padding: 0px 20px 20px 20px;">
          <h1 style="color: #133F53; margin: 0px; font-family: Arial, Helveticsa, 'sans-serif'; font-size: 25px;">Feedback on <?php echo $page_url; ?></h1>
          <?php if (isset($category)) { ?>
            <h2 style="color: #397191; margin-top: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 19px; font-weight: 300;">Category: <?php echo $category; ?> - Rating: <?php echo $rating; ?>/10 - WinWinRating: <?php echo $winwinrating; ?>/10</h2>
          <?php } ?>
          <p style="color: #133F53; font-family: Arial, Helvetica, 'sans-serif'; font-size: 15px; text-align: left"><?php echo isset($feedback_description) ? $feedback_description : "No description provided."; ?></p><br>
        </td>
      </tr>

      <tr style="background-image:url('https://dg7ltaqbp10ai.cloudfront.net/fit-in/1597763138-emailfooter.jpg'); background-position: bottom; background-repeat: no-repeat; height: 128px; overflow-y: auto; display: flex;">
        <td style="padding: 0 20px;">
            <?php if (!empty($images)) {
                foreach($images as $key => $object) { ?>
                    <a href="<?php echo $object["feedback_image"]; ?>" target="_blank"><img src="<?php echo $object["feedback_image"]; ?>" alt="feedback_image_<?php echo $key;?>" style="width: 102px; height: 102px; margin: 2px; object-fit: cover; border-radius: 50%;"></a>
                <?php }
            } ?>
        </td>
      </tr>
    </tbody>
  </table><br>
  <br>
</body>

</html>