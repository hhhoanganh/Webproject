Hello: {{$email_data['name']}}
<h2>Chào mừng bạn</h2>
<p>Chọn vào đường link</p><br>
<?php $url = env('APP_URL'); ?>
<a href="<?php echo $url ?>/api/v1/verify?token={{$email_data['verification_code']}}">Click here</a>
<br>
<p>Cảm ơn</p>

