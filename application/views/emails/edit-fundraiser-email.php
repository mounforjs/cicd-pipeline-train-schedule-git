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
					<h1 style="color: #133F53; margin: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 25px;">Beneficiary Edit Requested!</h1>
					<p style="color: #133F53; font-family: Arial, Helvetica, 'sans-serif'; font-size: 15px; text-align: left">
						<?php echo ucfirst($username); ?>,<br><br>
						You have requested an edit to your created beneficiary, "<?php echo ucfirst($beneficiary->name); ?>".
						<br><br>
						Our support team will look into it, and get back to you if we need more information. Please contact us at <a href="mailto:support@winwinlabs.org">support@winwinlabs.org</a> if you have any questions.
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