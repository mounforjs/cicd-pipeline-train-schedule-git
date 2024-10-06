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
          <h1 style="color: #133F53; margin: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 25px;">Password Reset</h1>
          <p style="color: #133F53; font-family: Arial, Helvetica, 'sans-serif'; font-size: 15px; text-align: left">You recently requested that your WinWinLabs password be reset. <br><br> If you did not request a password reset you can safely ignore this email.</p><br>
          <a style="color: #FFFFFF; padding: 10px; font-size: 15px; font-weight: 600; font-family: 'Oswald', Arial, Helvetica, 'sans-serif'; text-decoration: none; background: linear-gradient( 0deg, rgba(161, 31, 26, 1) 8%, rgba(161, 31, 26, 1) 49%, rgba(181, 33, 27, 1) 50% );" href="<?php echo asset_url('reset_verify') . '?token=' . $token . '&key=' . $key . '&sess=' . $upass; ?>">Reset Password</a><br><br>
        </td>
      </tr>
    </tbody>
  </table><br>
</body>

</html>

