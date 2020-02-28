<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="/css/app.css">
  </head>
  <body class="container">
      <h1>订单支付</h1>
      <form action="{{url('pay')}}" method="post">
          {{csrf_field()}}
          <input type="hidden" name="oid" value="{{$oid}}">
          <input type="submit" value="立即支付">
      </form>
  </body>
</html>
