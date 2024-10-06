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
          <h1 style="color: #133F53; margin: 0px; font-family: Arial, Helvetica, 'sans-serif'; font-size: 20px;">Thank you for your Donation!</h1>
          <p style="color: #133F53; font-family: Arial, Helvetica, 'sans-serif'; font-size: 15px; text-align: left">
            <?php echo ucfirst($username); ?>,
            <br><br>
            Date: <?php echo $transaction_date; ?>
            <br>
            Reference ID: <?php echo $transaction_id; ?>
            <br>
            <?php if ($transaction_status): ?>
              Status: <?php echo ucfirst($transaction_status); ?>
            <?php endif; ?>
            <br>
            Subtotal: $<?php echo number_format($transaction_subtotal, 2); ?>
            <br>
            <?php echo ucfirst($transaction_vendor); ?> Fee: $<?php echo number_format($transaction_tax, 2); ?>
            <br>
            Total: $<?php echo number_format($transaction_total, 2); ?>
            <br>
            Method: <?php echo ucfirst($transaction_method); ?>
            <br><br>
            If you have any questions, or if you did not authorize the above donation, please reach out to support@wininlabs.org.
          </p><br>
        </td>
      </tr>
      <tr>
        <td style="background-image:url('https://dg7ltaqbp10ai.cloudfront.net/fit-in/1597763138-emailfooter.jpg'); background-position: bottom; background-repeat: no-repeat; height: 90px;">
        </td>
      </tr>
    </tbody>
  </table><br>
  <center>
    <a href="<?php echo asset_url(); ?>" style="color: #6090ae; font-family: Arial, Helvetica, 'sans-serif'; 
      font-size: 13px; text-decoration: none; height:30px;">
    <img src="<?php echo getLogoImage() ?>" style="vertical-align: middle;"> winwinlabs.org</a>
  </center><br>
  <br>
</body>

</html>