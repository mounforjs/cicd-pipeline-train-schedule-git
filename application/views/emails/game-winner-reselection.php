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
          <h1 style="color: #133F53; margin: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 25px;">We've had to select another winner..</h1>
          <h2 style="color: #397191; margin-top: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 19px; font-weight: 300;">We've been trying to reach you!</h2>
          <p style="color: #133F53; font-family: Arial, Helvetica, 'sans-serif'; font-size: 15px; text-align: left">
            <?php echo ucfirst($username); ?>,<br><br>
            We've been trying to reach you regarding a game, <?php echo ucfirst($name); ?>, you won. <br><br>We tried contacting you for a week prior to the game ending, but we've unfortunately had to move on and select another winner.
            <br><br>If you have any questions, don't hesitate to contact us at: <a href="mailto:support@winwinlabs.org">support@winwinlabs.org</a>.
          </p><br>
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