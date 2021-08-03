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

$searchWord1 = $_GET['searchWord1'] ?? '';
$searchWord2 = $_GET['searchWord2'] ?? '';
$searchWord3 = $_GET['searchWord3'] ?? '';
$searchType1 = $_GET['searchType1'] ?? '';
$searchType2 = $_GET['searchType2'] ?? '';
$searchType3 = $_GET['searchType3'] ?? '';

$searchopp1 = $_GET['searchOp1'] ?? '';
$searchopp2 = $_GET['searchOp2'] ?? '';
$searchOp1 =  searchOperand($searchopp1);
$searchOp2 =  searchOperand($searchopp2);

//echo "변수"." ".$searchOp1." ".$searchOp2;
try {
     $conn = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {  
        echo("에러 내용: ".$e -> getMessage());
    }
?>
<!-- 검색페이지 구현 --> 
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="main.css"/>
    <title>BOOK LIST</title>
    <style>
        a {
            text-decoration: none;
        }
     </style>
</head>
<body>
    <header class="header">SongE's Library</header>
    <div class = "searchDiv">
        <form action="search.php">
            <div class = "searchFirst">
            <select name="searchType1" id="searchType1" onchange="clickText(this.id)">
                <option value="전체" selected="selected">전체</option>
                <option value="도서명">도서명</option>
                <option value="저자" >저자</option>
                <option value="출판사" >출판사</option>
                <option value="발행년도">발행년도</option>
            </select>
            <input type="text" name="searchWord1" id="searchWord1">
            <select name="searchOp1" id="searchOp1">
                <option value="AND">AND</option>
                <option value="OR">OR</option>
                <option value="NOT">NOT</option>
            </select>
            </div>
            <div class = "searchSecond">
            <select name="searchType2" id="searchType2" onchange="clickText(this.id)">
                <option value="전체" selected="selected">전체</option>
                <option value="도서명">도서명</option>
                <option value="저자" >저자</option>
                <option value="출판사" >출판사</option>
                <option value="발행년도">발행년도</option>
            </select>
            <input type="text" name="searchWord2" id="searchWord2">
            <select name="searchOp2" id="searchOp2" >
                <option value="AND">AND</option>
                <option value="OR">OR</option>
                <option value="NOT">NOT</option>
            </select>
            </div>
            
            <div class = "searchLast">
            <select name="searchType3" id="searchType3" onchange="clickText(this.id)">
                <option value="전체" selected="selected">전체</option>
                <option value="도서명">도서명</option>
                <option value="저자" >저자</option>
                <option value="출판사" >출판사</option>
                <option value="발행년도">발행년도</option>
            </select>
            <input type="text" name="searchWord3" id="searchWord3" onclick="clickText(this.id)">
            </div>
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
        <h2>도서목록</h2>
        <table class="bookTable">
        <thead>
        <tr>
            <th>도서번호</th>
            <th>도서명</th>
            <th>저자</th>
            <th>출판사</th>
            <th>대출상태</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $query1 = searchQuery($searchType1,1);
                $query2 = searchQuery($searchType2,2);
                $query3 = searchQuery($searchType3,3);
            
                //echo "<br>".$query1." ".$searchOp1." ".$query2." ".$searchOp2." ".$query3."</br>";
                //대출상태 구현 
                $stmt = $conn -> prepare($query1." ".$searchOp1." ".$query2." ".$searchOp2." ".$query3);
                $stmt -> bindParam(":searchWord1", $searchWord1);
                $stmt -> bindParam(":searchWord2", $searchWord2);
                $stmt -> bindParam(":searchWord3", $searchWord3);
                $stmt -> execute();

                while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                    if ($row['DATERENTED']!=null){
                        $rentState = '대출중';
                    }
                    else{
                        $rentState = '대출가능';
                    }
            ?>   
        <tr>
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?= $row['ISBN'] ?></a></td>  
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?= $row['TITLE'] ?></a></td>  
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?= $row['AUTHOR'] ?></a></td>
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?= $row['PUBLISHER'] ?></a></td>  
               <td><a href="bookview.php?ISBN=<?=$row['ISBN']?>"><?=$rentState?></a></td>     
        </tr>
        <?php  } ?>
        </tbody>
        </table>
    </div>
    <script src="click.js"></script>
</body>
</html>

<?php

    function searchQuery($searchType, $index){
        if ($index==1){
            $searchWord = ":searchWord1";
        }
        elseif($index ==2){
            $searchWord = ":searchWord2";
        }
        elseif($index ==3) {
            $searchWord = ":searchWord3";
        }
        if ($searchType == "전체") { // 전체조건을 이용하여 검색 
            $query = "SELECT ISBN,TITLE,PUBLISHER, DATERENTED, P_YEAR, LISTAGG(AUTHOR,',') WITHIN GROUP ( ORDER BY AUTHOR) AUTHOR 
            FROM (SELECT ISBN, TITLE, PUBLISHER, AUTHOR, DATERENTED , YEAR P_YEAR FROM EBOOK NATURAL JOIN AUTHORS)
            WHERE LOWER(TITLE) LIKE '%' || $searchWord  or  LOWER(AUTHOR) LIKE '%' || $searchWord  or  LOWER(PUBLISHER) LIKE '%' || $searchWord  or  EXTRACT(YEAR FROM P_YEAR) LIKE '%' || $searchWord
            GROUP BY ISBN, TITLE, PUBLISHER, DATERENTED, P_YEAR";
        }
        elseif ($searchType == "도서명") { //도서명 조건으로 검색 
            $query = "SELECT ISBN,TITLE,PUBLISHER, DATERENTED, P_YEAR, LISTAGG(AUTHOR,',') WITHIN GROUP ( ORDER BY AUTHOR) AUTHOR 
            FROM (SELECT ISBN, TITLE, PUBLISHER, AUTHOR, DATERENTED , YEAR P_YEAR FROM EBOOK NATURAL JOIN AUTHORS)
            WHERE LOWER(TITLE) LIKE '%' || $searchWord 
            GROUP BY ISBN, TITLE, PUBLISHER, DATERENTED, P_YEAR";
        }
        elseif ($searchType == "저자") { // 저자 조건으로 검색 
            $query = "SELECT ISBN,TITLE,PUBLISHER, DATERENTED, P_YEAR, LISTAGG(AUTHOR,',') WITHIN GROUP ( ORDER BY AUTHOR) AUTHOR 
            FROM (SELECT ISBN, TITLE, PUBLISHER, AUTHOR, DATERENTED , YEAR P_YEAR FROM EBOOK NATURAL JOIN AUTHORS)
            WHERE LOWER(AUTHOR) LIKE '%' || $searchWord 
            GROUP BY ISBN, TITLE, PUBLISHER, DATERENTED, P_YEAR";
        }
        elseif ($searchType == "출판사") { // 출판사 조건으로 검색 
            $query = "SELECT ISBN,TITLE,PUBLISHER, DATERENTED, P_YEAR, LISTAGG(AUTHOR,',') WITHIN GROUP ( ORDER BY AUTHOR) AUTHOR 
            FROM (SELECT ISBN, TITLE, PUBLISHER, AUTHOR, DATERENTED , YEAR P_YEAR FROM EBOOK NATURAL JOIN AUTHORS)
            WHERE LOWER(PUBLISHER) LIKE '%' || $searchWord 
            GROUP BY ISBN, TITLE, PUBLISHER, DATERENTED, P_YEAR";
        }
        elseif( $searchType == "발행년도") { // 발행년도 조건으로 검색 
            $query = "SELECT ISBN,TITLE,PUBLISHER, DATERENTED, P_YEAR, LISTAGG(AUTHOR,',') WITHIN GROUP ( ORDER BY AUTHOR) AUTHOR 
            FROM (SELECT ISBN, TITLE, PUBLISHER, AUTHOR, DATERENTED , YEAR P_YEAR FROM EBOOK NATURAL JOIN AUTHORS)
            WHERE EXTRACT(YEAR FROM P_YEAR) LIKE '%' || $searchWord 
            GROUP BY ISBN, TITLE, PUBLISHER, DATERENTED, P_YEAR";
        }
        else { //검색 없이 처음 불러오는 경우 
            $query = "SELECT ISBN,TITLE,PUBLISHER, DATERENTED, LISTAGG(AUTHOR,',') WITHIN GROUP ( ORDER BY AUTHOR) AUTHOR 
        FROM (SELECT ISBN, TITLE, PUBLISHER, AUTHOR, DATERENTED FROM EBOOK NATURAL JOIN AUTHORS)
        WHERE LOWER(TITLE) LIKE '%' || $searchWord     OR LOWER(PUBLISHER) LIKE '%' || $searchWord     OR LOWER(AUTHOR) LIKE '%' || $searchWord   
        GROUP BY ISBN, TITLE, PUBLISHER, DATERENTED";
        }
        return $query;
    }

    function searchOperand($searchOp){
        $op = 'UNION';
        if ($searchOp == "AND" ){
            $op =  "INTERSECT";
        }
        elseif($searchOp == "OR") {
            $op =  "UNION";
        }
        elseif($searchOp == "NOT") {
            $op =  "MINUS";
        }
        return $op;
    }
?>