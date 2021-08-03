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
$isbn = $_GET['ISBN'] ?? '';
try {
     $conn = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {  
        echo("에러 내용: ".$e -> getMessage());
    }
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="main.css"/>
    <title>BOOK DETAILS</title>
</head>
<body>
    <header class="header">SongE's Library</header>
    <div class = "searchDiv">
        <form action="search.php">
            <input type="text" name="searchWord" id="searchWord">
            <input type="submit" name="searchBtn" id="searchBtn" value="검색">
        </form>
    </div>
    <h1 class = "menuText">MENU</h1>
    <div class="menu">
        <button onclick="location.href='main.php'">HOME</button>
        <br>
        <button onclick="location.href='search.php'">도서검색</button>
        <br>
        <button onclick="location.href='my.php'">마이페이지</button>
    </div>
    <div class="banner">
        <br><br>
         <div class="bookImage">
         <img src="images/book2.png" alt="book">
         </div>
         <div class="bookInfo"> <!-- 책 상세 페이지 --> 
         <?php $stmt = $conn -> prepare("SELECT ISBN,TITLE,PUBLISHER, DATERENTED, EXTRACT(YEAR FROM P_YEAR) P_YEAR, LISTAGG(AUTHOR,',') WITHIN GROUP ( ORDER BY AUTHOR) AUTHOR 
                    FROM (SELECT ISBN, TITLE, PUBLISHER, AUTHOR, DATERENTED , YEAR P_YEAR FROM EBOOK NATURAL JOIN AUTHORS)
                    WHERE ISBN = :isbn
                    GROUP BY ISBN, TITLE, PUBLISHER, DATERENTED, P_YEAR");
                $stmt -> execute(array($isbn));
                $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        ?> 
            <h4>도서명 : <?php echo $row['TITLE'];?></h4>
            <h4>저자 : <?php echo $row['AUTHOR'];?></h4>
            <h4>출판사 : <?php echo $row['PUBLISHER'];?></h4>
            <h4>발행년도 : <?php echo $row['P_YEAR'];?></h4>
         </div>
         <br><br>
         <div class="bookBtn">
             <button id = "rental" onclick="location.href='process.php?state=rental&isbn=<?=$row['ISBN']?>'">대여하기</button>
             <button id = "reserve" onclick="location.href='process.php?state=reserve&isbn=<?=$row['ISBN']?>'">예약하기</button>
         </div>
    </div>
</body>
</html>