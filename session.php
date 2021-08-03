<?php
//******블로그 참고하여 만든 코드 ********
 //로그인 세션을 저장하고 있는 php
  session_start();//세션 열기
    if (isset($_SESSION['userName'])){ // 세션에 사용자 이름 저장되어있으면
      $login = true; //login 변수는 true
    }
    else { //세션에 사용자 이름 저장되어있지 않으면
      $login = false;//login 변수는 false
    }
 ?>
