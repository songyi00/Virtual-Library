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
    <title>SongE's Library</title>
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
    <div class="banner"> <!-- 메인 페이지 --> 
        <h2 class="rule">온라인 도서관 사용규칙</h2>
        <img class ="bannerImage" src="images/banner.png" alt="banner">
        <h3>-대출-</h3>
        <p> 한 회원의 대출 가능한 최대 권수는 3권입니다.</p>
        <p> 대출기간은 대출일 다음날부터 10일입니다.</p>
        <p> 대출한 도서에 대해 대출 기간 내에 최대 2회 10일씩 연장이 가능하나, 예약이 되어 있을 경우 대출 연장은 불가합니다.</p>
        <p> 대출 가능한 도서에 대해서만 대출이 가능합니다.</p>
        <p>반납 기일이 도래한 도서는 자정이 되면 자동적으로 반납 처리가 됩니다.</p>
        <br>
        <h3>-예약-</h3>
        <p>한 회원 당 3권까지 예약이 가능합니다.</p>
        <p>3권 범위 안에서 기존 예약 도서를 취소하고 다른 도서를 예약할 수 있습니다.</p>
        <p>한 도서에 대해 여러 회원이 예약을 할 수 있고 예약한 도서가 반납이 되면, 제일 앞 순위의 회원 이메일로 통보됩니다.</p>
        <p>다음날까지 대출하지 않으면 예약은 취소되며 다음 순위자에게 통보됩니다.</p>
    </div>
    <script src="click.js"></script>
</body>
</html>