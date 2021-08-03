<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="start.css"/>
    <title>로그인</title>
  </head>
  <body>
    <!--로그인 폼 -->
    <div class="wrapper">
    <div class="inputBox">
      <h3>LOGIN</h3>
      <form  action="userCheck.php?mode=login" method="post" >
        <label for="id">회원번호: </label>
        <input type="text" id = "cno" name="cno" value="">
        <br>
        <label for="pw">pw: </label>
        <input type="password" id="passwd" name="passwd" value="">
        <br><br>
        <input type="submit" name="" value="로그인">
      </form>
    </div>
    </div>
  </body>
</html>
