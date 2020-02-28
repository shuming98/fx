<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="/css/app.css">
</head>
<body>
<form action="https://api.weibo.com/oauth2/authorize" method="post">
	{{csrf_field()}}
	<input type="hidden" name="client_id" value="3495392613">
	<input type="hidden" name="redirect_uri" value="http://qingfeng.wicp.vip/weibo/center">
	<input class="form-control" type="submit" value="微博登录">
</form>
</body>
</html>