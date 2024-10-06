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
					<h1 style="color: #133F53; margin: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 25px;">New Beneficiary Created!</h1>
					<h2 style="color: #397191; margin-top: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 19px; font-weight: 300;">Attention required.</h2>
					<p style="color: #133F53; font-family: Arial, Helvetica, 'sans-serif'; font-size: 15px; text-align: left">
						<?php echo ucfirst($adminUsername); ?>,<br><br>
						A user, <?php echo ucfirst($username); ?>, winner has has created a new beneficiary, <?php echo ucfirst($beneficiary->name); ?>.
						<br><br>
						If you have any questions, you can contact them at: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>.
					</p><br>
					<a href="<?php echo asset_url('fundraisers/showForEdit/' . $beneficiary->slug); ?>" style="color: black; background-color: #ffa000; padding: 10px; font-size: 15px; font-family: Arial, Helvetica, 'sans-serif'; text-decoration: none; background: linear-gradient(0deg, rgba(255, 160, 0, 1) 49%, rgba(255, 177, 46, 1) 50%);">See Edit</a>
				</td>
			</tr>
			<tr>
				<td style="background-image:url('https://dg7ltaqbp10ai.cloudfront.net/fit-in/1597763138-emailfooter.jpg'); background-position: bottom; background-repeat: no-repeat;">
				</td>
			</tr>
		</tbody>
	</table><br>
	<center>
		<a href="<?php echo asset_url(); ?>" style="color: #6090ae; font-family: Arial, Helvetica, 'sans-serif'; font-size: 13px; text-decoration: none;"><img src="<?php echo getLogoImage() ?>" style="vertical-align: middle;"> winwinlabs.org</a>
	</center><br>
	<br>
</body>

</html>