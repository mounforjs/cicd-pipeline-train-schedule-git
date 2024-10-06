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
					<h1 style="color: #133F53; margin: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 25px;">User Signed Up!</h1>
					<p style="color: #133F53; font-family: Arial, Helvetica, 'sans-serif'; font-size: 15px; text-align: left">
						Their details are as follows:<br>
						Name: <?php echo $user->firstname.' '.$user->lastname; ?><br>
						Email: <?php echo $user->email; ?><br>
						Username: <?php echo $user->username; ?><br>
						Country: <?php echo $user->country; ?><br><br>

						Please make sure they have entered legitimate information!
					</p><br>
				</td>
			</tr>
		</tbody>
	</table><br>
	<br>
</body>

</html>