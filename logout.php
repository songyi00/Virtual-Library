<?php
    include 'session.php'; //로그인 세션이 저장되어 있는 php
    session_destroy(); //세션 파괴 
?>
<!--로그아웃 페이지 -->
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="start.css"/>
    <title>logout</title>
  </head>
  <body>
    <div class="wrapper">
    <div class="inputBox">
      <h3>LOGOUT</h3>
        <h3>로그아웃이 완료되었습니다.</h3>
        <button onclick="location.href='start.html'">시작화면으로 이동</button>
    </div>
    </div>
  </body>
</html>
