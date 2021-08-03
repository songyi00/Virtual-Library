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

// 시간되면 자동 반납 처리 
$stmt = $conn -> prepare("select extract(month from datedue)|| '/' || extract(day from datedue)|| '/' || extract(year from datedue) newDate, 
                        title,cno,daterented,datedue,exttimes,isbn 
                        from ebook
                        where datedue is not null");
$stmt -> execute();
date_default_timezone_set('Asia/Seoul'); // 서버시간
$nowTime = date("Y-m-d");

while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        //echo $nowTime." ".date( 'Y-m-d', strtotime($row['NEWDATE']))."\n";
        if ($nowTime >= date( 'Y-m-d', strtotime($row['NEWDATE']))){
        $subStmt = $conn-> prepare("update ebook set 
                                    daterented = null,
                                    datedue = null,
                                    cno = null,
                                    exttimes = 0
                                    where isbn = :isbn");
        $subStmt->bindParam(":isbn",$row['ISBN']);
        $subStmt->execute();

        }
    }
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="main.css"/>
    <title>대출내역 조회</title>
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

        $stmt = $conn -> prepare("select count(cno) cnt from ebook where cno = :cno");
        $stmt -> execute(array($cno));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        echo "<div class='rentalNum'><h3>대출권수 : ".$row['CNT']."</h3></div>";
      ?>
      <h3>대출내역</h3>
      <table class="bookTable">
        <thead>
            <tr>
            <th>도서명</th>
            <th>대출날짜</th>
            <th>반납기한</th>
            <th>대출연장횟수</th>
            <th>반납</th>
            <th>연장</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                 $stmt = $conn -> prepare("select extract(month from datedue)|| '/' || extract(day from datedue)|| '/' || extract(year from datedue) newDate, 
                                            title,cno,daterented,datedue,exttimes,isbn 
                                            from ebook 
                                            where cno = :cno");
                 $stmt -> execute(array($cno));
                 while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) { // 대출 내역 조회
            ?>
            <tr>
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?= $row['TITLE'] ?></a></td>  
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?= $row['DATERENTED'] ?></a></td>  
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?= $row['DATEDUE'] ?></a></td>
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?= $row['EXTTIMES'] ?></a></td>
               <td><button onclick="location.href='process.php?state=return&isbn=<?=$row['ISBN']?>'">반납하기</button></td>
               <td><button onclick="location.href='process.php?state=extend&isbn=<?=$row['ISBN']?>'">연장하기</button></td>
            </tr>
            <?php 
                }
            ?>
        </tbody>
      </table>
    </div>
</body>
</html>