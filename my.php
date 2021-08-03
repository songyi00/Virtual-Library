<?php
$tns = "
    (DESCRIPTION=
        (ADDRESS_LIST= (ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))
        (CONNECT_DATA= (SERVICE_NAME=XE))  
    )
";
$dsn = "oci:dbname=".$tns.";charset=utf8";
$username = 'c##d201902666';
$password = 'd201902666';
$searchWord = $_GET['searchWord'] ?? '';
$searchType = $_GET['searchType'] ?? '';
try {
     $conn = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {  
        echo("에러 내용: ".$e -> getMessage());
    }
?>
<!-- 마이페이지 구현 --> 
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="main.css"/>
    <title>MY PAGE</title>
</head>
<body>
    <header class="header">SongE's Library</header>
    
    <h1 class = "menuText">MENU</h1>
    <div class="menu">
        <button onclick="location.href='main.php'">HOME</button>
        <br>
        <button onclick="location.href='search.php'">도서검색</button>
        <br>
        <button onclick="location.href='my.php'">마이페이지</button>
    </div>
    <div class="my_banner">
    <?php include 'session.php';
      if($login){ // 로그인이 되어있는 경우 메인 페이지에 사용자 이름이 뜬다.
        echo "<h2 class='userName'>".$_SESSION['userName'].'님(회원번호: '.$_SESSION['cno'].") </h2>";
        $route = '"logout.php"';
        echo "<button class ='logoutBtn' onclick='location.href=$route'>로그아웃</button>";
      ?>
      <br><br><br><br>
      <div class ="selectRentalDiv">
        <button id = "selectRental" onclick = "location.href='selectRental.php'">대출 도서 조회</button>
      </div>
      <div class="selectReserveDiv">
        <button id = "selectReserve" onclick = "location.href='selectReserve.php'">예약 도서 조회</button>
      </div>
      <?php
      }
      else{
          echo "<h2>"."로그인을 해주세요!"."</h2><br>";
          $route = '"start.html"';
          echo "<button onclick='location.href=$route'> 시작화면으로 이동</button>";
      }
    ?>
    </div>
    <script src="click.js"></script>
</body>
</html>