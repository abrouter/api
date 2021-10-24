<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div>
        <h3>Reset Password Notification. </h3>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <a href="{{url('/en/resetpassword', $resetData['token']).'?'.http_build_query(['email' => $resetData['email']], null, '?', PHP_QUERY_RFC3986)}}">Reset Password</a>
        <p>If you did not request a password reset, no further action is required.</p>
    </div>
</body>
</html>