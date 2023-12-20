Hello: {{$email_data['name']}}
<h2>Chào mừng bạn</h2>
<p>Chọn vào đường link để xác nhận đơn hàng</p><br>
<?php $url = env('APP_URL'); ?>
{{$email_data['order']}}
<a href="<?php echo $url ?>/api/v1/verifyOrder?token={{$email_data['verification_code']}}">Click here</a>
<br>
<p>Cảm ơn</p>

