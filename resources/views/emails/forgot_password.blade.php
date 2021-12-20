<!DOCTYPE html>
<html>
<head>
    <title>Welcome Ion App</title>
</head>

<body>
<h2>Welcome {{$user['name']}}</h2>
<br/>
Your registered email-id is {{$user['email']}} 
<br>
Your WebsiteUrl is {{url($user['name'])}}
userName:- {{$user['email']}}
</body>

</html>
