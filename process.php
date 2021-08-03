<?php
include "session.php";

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

$state = $_GET['state'];
$isbn = $_GET['isbn']; 
$rentalIsPossible = FALSE; 
if (!isset($_SESSION['cno'])){ //로그인 여부 
    echo ("<script language=javascript> alert('로그인이 필요한 서비스입니다.'); location.href='search.php'</script>");
}
elseif ($state === 'rental'){ // 대여처리
    $cno = $_SESSION['cno'];
    $stmt = $conn -> prepare("select cno from ebook where isbn = :isbn");
    $stmt -> execute(array($isbn));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    //print_r($row['CNO']);
    if ($row['CNO']==null){ // 대출가능한 도서인 경우 
        $stmt = $conn -> prepare("select count(cno) cnt from ebook where cno = :cno");
        $stmt -> execute(array($cno));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        
        if ($row['CNT']>=3){
            echo ("<script language=javascript> alert('대출가능한 최대권수는 3권입니다.'); location.href='search.php'</script>");
        }
        else {   
            $cno = $_SESSION['cno'];
            $stmt = $conn -> prepare("update ebook set 
                                    daterented = SYSDATE,
                                    datedue = trunc(SYSDATE) + 11,
                                    cno = :cno
                                    where isbn = :isbn");
            $stmt -> bindParam(":cno", $cno);
            $stmt -> bindParam(":isbn", $isbn);
            $stmt -> execute();
            
            echo ("<script language=javascript> alert('대출이 완료되었습니다.'); location.href='search.php'  </script>");
        }
    }
    else{ // 대출중인 도서인 경우 
        echo ("<script language=javascript> alert('이미 대출중인 도서입니다.'); location.href='search.php' </script>");
    }
}

elseif ($state === 'reserve'){ //예약처리
    $cno = $_SESSION['cno'];
    $stmt = $conn -> prepare("select cno from ebook where isbn = :isbn");
    $stmt -> execute(array($isbn));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    //print_r($row['CNO']);
    if ($row['CNO']==null){ // 대출가능한 도서인 경우 
        echo ("<script language=javascript> alert('대출이 가능한 도서입니다.'); location.href='search.php' </script>");
    }
    else { //예약 가능한 경우 
        $stmt = $conn -> prepare("select cno, isbn from reserve where isbn = :isbn and cno = :cno"); 
        $stmt -> bindParam(":cno", $cno);
        $stmt -> bindParam(":isbn", $isbn);
        $stmt -> execute();
        $row = $stmt -> fetch(PDO::FETCH_ASSOC); 
        $row['CNO'] = $row['CNO'] ?? '';
        $row['ISBN'] = $row['ISBN'] ?? '';
        //echo $row['CNO']." ".$row['ISBN']."하이"; 
        if ($row['CNO'] == $cno and $row['ISBN'] == $isbn){ //이미 예약한 도서
            echo ("<script language=javascript> alert('이미 예약하신 도서입니다.'); location.href='search.php' </script>");
        }
        else { //예약접수
            $stmt = $conn -> prepare("select count(cno) cnt from reserve where cno = :cno");
            $stmt -> execute(array($cno));
            $row = $stmt -> fetch(PDO::FETCH_ASSOC);
            if ($row['CNT'] >= 3){ // 예약권수가 3권을 넘어서는 경우 
                echo ("<script language=javascript> alert('예약권수는 3권을 넘어갈 수 없습니다.'); location.href='search.php' </script>");
            }
            else {
                $stmt = $conn -> prepare("insert into reserve values (:isbn, :cno, SYSDATE)");
                $stmt -> bindParam(":cno", $cno);
                $stmt -> bindParam(":isbn", $isbn);
                $stmt -> execute(); 
                echo ("<script language=javascript> alert('예약이 접수되었습니다.'); location.href='search.php' </script>");
            }
        }
    }
}

elseif ($state == 'return'){ //반납처리
    $cno = $_SESSION['cno'];
    $stmt = $conn -> prepare("update ebook set cno = null, daterented = null, datedue = null, exttimes = 0 where isbn = :isbn");
    $stmt -> bindParam(":isbn", $isbn);
    $stmt -> execute();

    $stmt = $conn -> prepare("select count(cno) cnt from reserve where isbn = :isbn");
                $stmt -> bindParam(":isbn", $isbn);
                $stmt -> execute(); 
                $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    if ($row['CNT'] >=1 ) { //===========반납시 예약한 사용자에게 이메일처리 ==============
        $stmt = $conn -> prepare("select isbn, title,to_char(datetime,'YYYY-MM-DD HH24:Mi:SS'),cno,email
                                    from resinfo
                                    where isbn = :isbn
                                    order by datetime");
        $stmt -> bindParam(":isbn", $isbn);
        $stmt -> execute();
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        $email = 'ksl2950@gmail.com';
        $subject = "예약하신 도서가 반납되었습니다.";
        $title = $row['TITLE'];
        $content = "[$title] 도서가 반납되었습니다. 다음날까지 대출해주세요.";
        $headers = "From: leecr1215@naver.com\r\n";
        //echo $email;
        $result = mail($email,$subject,$content,$headers);
        if($result){
            echo "mail success";
            error_log($email, 0);  // mailto 변수를 서버의 에러로그에 찍는다.
            error_log($subject, 0);

        }else  {
            echo "mail fail";
        }
    }
    echo ("<script language=javascript> alert('반납이 완료되었습니다.'); location.href='selectRental.php' </script>");
}

elseif ($state == 'extend'){ //연장처리
    $stmt = $conn -> prepare("select count(cno) cnt from reserve where isbn = :isbn");
                $stmt -> bindParam(":isbn", $isbn);
                $stmt -> execute(); 
                $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    if ($row['CNT'] >=1 ) { //연장할 수 없는 경우(예약된 도서 존재)
        echo ("<script language=javascript> alert('예약된 도서 : 연장이 불가합니다.'); location.href='selectRental.php' </script>");
    }
    else {
        $stmt = $conn -> prepare("select exttimes from ebook where isbn = :isbn");
        $stmt -> bindParam(":isbn", $isbn);
        $stmt -> execute();
        $row = $stmt -> fetch(PDO::FETCH_ASSOC); 
        if ($row['EXTTIMES']==2){ //연장을 2번한경우 
            echo ("<script language=javascript> alert('연장은 2번까지 가능합니다.'); location.href='selectRental.php' </script>");
        }
        else { //연장수행 
            $stmt = $conn -> prepare("update ebook set datedue = trunc(datedue) + 11, exttimes = exttimes+1 where isbn = :isbn");
            $stmt -> bindParam(":isbn", $isbn);
            $stmt -> execute();
            echo ("<script language=javascript> alert('기간이 연장되었습니다.'); location.href='selectRental.php' </script>");
        }
    }
}
elseif ($state = 'cancel'){ //예약취소
    $cno = $_SESSION['cno'];
    $stmt = $conn -> prepare("delete from reserve where isbn = :isbn and cno = :cno");
    $stmt -> bindParam(":isbn", $isbn);
    $stmt -> bindParam(":cno", $cno);
    $stmt -> execute();
    echo ("<script language=javascript> alert('예약이 취소되었습니다.'); location.href='selectReserve.php' </script>");
}
?>