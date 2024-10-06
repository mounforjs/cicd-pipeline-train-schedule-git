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
          <h1 style="color: #133F53; margin: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 25px;">You need to select new prize winners!</h1>
          <p style="color: #133F53; font-family: Arial, Helvetica, 'sans-serif'; font-size: 15px; text-align: left">
            <?php echo ucfirst($username); ?>,<br><br>
            <?php echo $reselectCount; ?> of your previously chosen winners for your game, <?php echo ucfirst($name); ?>, have failed to claim their prize in time. You need to select <?php echo $reselectCount; ?> new <?php echo (($reselectCount > 1) ? " winners": " winner"); ?>.
            <br><br>If you have any questions, don't hesitate to contact us at: <a href="mailto:support@winwinlabs.org">support@winwinlabs.org</a>.
          </p><br><br>
          <a href="<?php echo asset_url('games/review/' . $slug); ?>" style="color: black; background-color: #ffa000; padding: 10px; font-size: 15px; font-family: Arial, Helvetica, 'sans-serif'; text-decoration: none; background: linear-gradient(0deg, rgba(255, 160, 0, 1) 49%, rgba(255, 177, 46, 1) 50%);">Select winners</a><br><br>
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