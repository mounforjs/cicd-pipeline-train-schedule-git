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
          <h1 style="color: #133F53; margin: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 25px;">A portion of winners have been reselected!</h1>
          <h2 style="color: #397191; margin-top: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 19px; font-weight: 300;">Previous winners didn't respond!</h2>
          <p style="color: #133F53; font-family: Arial, Helvetica, 'sans-serif'; font-size: 15px; text-align: left">
            <?php echo ucfirst($username); ?>,<br><br>
            We had to reselect a portion of winners for your game, <?php echo ucfirst($name); ?>. <br><br>We attempted to contact your previous winners for a week prior to the end of your game, but received no response. Your new winners will also have a week to respond and claim their prize. 
            <br><br>However, we were not able to find enough winners to fulfill the number of winners listed for your game.
            <br><br>To ensure all is well, your game will be manually reviewed by a staff member. We apologize for any inconvience and hope to have the issue resolved quickly.
            <br><br>If you have any questions, don't hesitate to contact us at: <a href="mailto:support@winwinlabs.org">support@winwinlabs.org</a>.
          </p><br>
          <a href="<?php echo asset_url('dashboard?tab=prizes'); ?>" style="color: black; background-color: #ffa000; padding: 10px; font-size: 15px; font-family: Arial, Helvetica, 'sans-serif'; text-decoration: none; background: linear-gradient(0deg, rgba(255, 160, 0, 1) 49%, rgba(255, 177, 46, 1) 50%);">Dashboard</a><br><br>
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