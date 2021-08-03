
<!DOCTYPE html>
  <html>
        <head>
                <title> 회원가입</title>
                <meta charset = "utf-8">
                <link rel="stylesheet" href="start.css"/>
        </head>
        <body>
            <div class = "wrapper">
          <!--회원가입 폼-->
                <form action = "userCheck.php?mode=join" method="post" >
                  <div class  ="inputBox" >
                  <h2> welcome! </h2>
                    <label for="name">Name :</label>
                    <input type="text" id="name" name="name" required/>
                    <br>
                    <label for="email">EMAIL :</label>
                    <input type="email" name="email" id="email" required />
                    <br>
                    <label for="pw">PASSWORD : </label>
                    <input type="password" name="passwd" id="passwd" required/>
                    <br>
                    <br>
                    <input type="submit" value="ok"/>
              </div>
            </div>
        </body>
</html>
