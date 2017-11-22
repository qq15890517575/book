<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>凯恩书店</title>
</head>
<body>

<p>您好，感谢您在{{ $m3_email->subject }}注册帐户！激活帐户需要点击下面的链接:</p>
<p>{{ $m3_email->content }}</p>
</body>
</html>