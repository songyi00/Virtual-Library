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
<!-- 예약내역조회 --> 
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="main.css"/>
    <title>예약내역 조회</title>
    <style>
        a {
            text-decoration: none;
        }
     </style>
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
        echo "<h1 class='userName'>".$_SESSION['userName'].'님 '."</h1>";
        $route = '"logout.php"';
        $cno = $_SESSION['cno'];
        echo "<button class ='logoutBtn' onclick='location.href=$route'>로그아웃</button>";

        $stmt = $conn -> prepare("select count(cno) cnt from reserve where cno = :cno");
        $stmt -> execute(array($cno));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        echo "<div class='rentalNum'><h3>예약권수 : ".$row['CNT']."</h3></div>";
      ?>
      <h3>예약내역</h3>
      <table class="bookTable">
        <thead>
            <tr>
                <th>도서번호</th>
                <th>도서명</th>
                <th>예약접수날짜</th>
                <th>예약취소</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                 $stmt = $conn -> prepare("select reserve.isbn isbn, ebook.title title, reserve.datetime datetime
                 from reserve, ebook
                 where reserve.cno = :cno and reserve.isbn = ebook.isbn");
                 $stmt -> execute(array($cno));
                 while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr>
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?= $row['ISBN'] ?></a></td>  
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?= $row['TITLE'] ?></a></td>  
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?= $row['DATETIME'] ?></a></td>
               <td><button onclick = "location.href='process.php?state=cancel&isbn=<?=$row['ISBN']?>'">예약취소</button></td>
            </tr>
            <?php 
                 }
                 ?>
        </tbody>
      </table>
    </div>
</body>
</html>