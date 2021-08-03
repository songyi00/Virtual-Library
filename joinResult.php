<?php
    $cno = $_GET['cno'];
?>
<!-- 회원가입 결과 --> 
<!DOCTYPE html>
  <html>
        <head>
                <title>welcome</title>
                <meta charset = "utf-8">
                <link rel="stylesheet" href="start.css"/>
        </head>
        <body>
            <div class = "wrapper">
            <div class  ="inputBox" >
                <p>회원가입이 완료되었습니다!</p>
                <p>회원번호는 <?=$cno?> 입니다.</p>
                <br>
                <button onclick="location.href='login.php'">로그인하러가기</button>
                <button onclick="location.href='main.php'">홈으로 이동</button>
            </div>
            </div>
        </body>
</html>