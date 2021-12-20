<!DOCTYPE html>
<html>
<head>
    <title>Welcome Ion App</title>
</head>

<body>
<h2>Welcome {{$user['name']}}</h2>
<br/>
Your registered email-id is {{$user['email']}} 
Your verification code is  {{$user['verify_code']}}
<br>
</body>

</html>
