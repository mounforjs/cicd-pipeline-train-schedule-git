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
          <h1 style="color: #133F53; margin: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 25px;">Your game has ended!</h1>
          <h2 style="color: #397191; margin-top: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 19px; font-weight: 300;">Congratulations!</h2>
          <p style="color: #133F53; font-family: Arial, Helvetica, 'sans-serif'; font-size: 15px; text-align: left">
            <?php echo ucfirst($username); ?>,<br><br>
            Your game, <?php echo ucfirst($name); ?>, supporting <?php echo ucfirst($charity); ?> has ended!
            <?php if (isset($review) && $review) { ?>
              <br><br>Your game contains questions which require your review. As such, you must have the final say in selecting your winners. Click on the button below to start selecting! 
            <?php } else { ?>
              <br><br>We will begin selecting winners very shortly! If your game rewards players with credits, then the process will be automatic. However, if you reward players with prizes, once they claim their prizes, you will have some additional work to do!
            <?php } ?>
          </p><br>
          <a href="<?php echo asset_url('games/' . (($review) ? "review/" : "show/completed/") . $slug); ?>" style="color: black; background-color: #ffa000; padding: 10px; font-size: 15px; font-family: Arial, Helvetica, 'sans-serif'; text-decoration: none; background: linear-gradient(0deg, rgba(255, 160, 0, 1) 49%, rgba(255, 177, 46, 1) 50%);"><?php echo ($review) ? "Review" : "Game"; ?></a><br><br>
        </td>
      </tr>
      <tr>
        <td style="background-image:url('https://dg7ltaqbp10ai.cloudfront.net/fit-in/1597763138-emailfooter.jpg'); background-position: bottom; background-repeat: no-repeat;">
        </td>
      </tr>
    </tbody>
  </table><br>
  <center>
		<a href="<?php echo asset_url(); ?>" style="color: #6090ae; font-family: Arial, Helvetica, 'sans-serif'; font-size: 13px; text-decoration: none;"><img src="<?php echo getLogoImage() ?>" style="vertical-align: middle;"> winwinlabs.org</a> <span style="color: #18384c"> | </span> <a href="<?php echo asset_url("profile/?tab=preferences"); ?>" style="color: #6090ae; font-family: Arial, Helvetica, 'sans-serif'; font-size: 13px; text-decoration: none;"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1597764102-unsubscribeemail.png" style="vertical-align: middle;"> Unsubscribe</a>
	</center><br>
  <br>
</body>

</html>