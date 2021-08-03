<?php

$tns= "
    (DESCRIPTION=
        (ADDRESS_LIST= (ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))
        (CONNECT_DATA= (SERVICE_NAME=XE))
    )
";

$dsn = "oci:dbname=".$tns.";charset=utf8";
$username = 'c##d201902666';
$password = 'd201902666';
$conn = new PDO($dsn, $username, $password);
$loginState = FALSE; 

switch($_GET['mode']){
    case'join': // 회원가입 처리
        $stmt = $conn->prepare("INSERT into customer (CNO, NAME, PASSWD, EMAIL) VALUES ((SELECT NVL(MAX(CNO), 0) + 1 FROM CUSTOMER), :name, :passwd, :email)");
        //$stmt = $conn->prepare("select * from customer");
        $stmt -> bindParam(":name",$name);
        $stmt -> bindParam(":passwd",$passwd);
        $stmt -> bindParam(":email", $email);
        $name = $_POST['name'];
        $passwd = $_POST['passwd'];
        $email = $_POST['email'];
        $stmt -> execute();

        $stmt = $conn->prepare("SELECT NVL(MAX(CNO), 0) CNO FROM CUSTOMER");
        $stmt -> execute();
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        $cno = $row['CNO'];
        //echo $cno;
        header("Location: joinResult.php?cno=$cno");
        break;
    case'login': // 로그인 처리
        $stmt= $conn->prepare("SELECT CNO, NAME, EMAIL, PASSWD FROM CUSTOMER WHERE CNO = :cno AND PASSWD = :passwd" );
        $stmt -> bindParam(":cno", $cno);
        $stmt -> bindParam(":passwd", $passwd);
        $cno = $_POST['cno'];
        $passwd = $_POST['passwd'];
        $stmt -> execute(); 
        
        $result = array();
        while($row = $stmt -> fetch()) {
            $result[] = $row;
        }
        if (count($result)!=0){
            $cno = $result[0][0];
            $name = $result[0][1];
            include "session.php";
            $_SESSION['cno'] = $cno;
            $_SESSION['userName'] = $name;
            header("Location: main.php");
        }
    break;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="start.css"/>
    <title>사용자체크</title>
  </head>
  <body>
    <div class="wrapper">
    <div class="inputBox">
    <h3>연결 실패</h3>
        <button onclick="location.href='login.php'">다시 로그인</button>
        <button onclick="location.href='join.php'">다시 회원가입</button>
    </div>
    </div>
  </body>
</html>

