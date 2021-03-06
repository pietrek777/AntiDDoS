<?php

  require_once 'resources/Utils.php';
  session_start();
  
  try{
	  $pdo = getConnection();
  } catch(Exception $e){
	  header('HTTP/1.1 500 Internal Server Error');
	  die("<h1>Couldn't establish connection with database. Check your database credentials in resources/verification.php and try again</h1>");
  }
  if(isset($_POST['submit'])){
    try{
      if(empty($_POST['nickname'])) sendError($nickerr);

      $nickname = strtolower($_POST['nickname']);
      if(preg_match('/[^A-Za-z0-9.#\\-$]/', $nickname)){
        sendError($charerr);
      }
      if(strlen($nickname) < 2 || strlen($nickname) > 16){
        sendError($lengtherr);
      }
      $userIp = getClientIp();
      if(!$userIp) sendError($iperr);

      $captcha=$_POST['g-recaptcha-response'];
      if(!$captcha) sendError($captchaerr);
      $response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretkey."&response=".$captcha), true);
      if(!$response['success']) sendError($captchaerr);

      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $pdo->query("CREATE DATABASE IF NOT EXISTS ".$dbname);
      $pdo->query("use ".$dbname);
      $pdo->query("CREATE TABLE IF NOT EXISTS ".$tablename." (
        `user_id` INT AUTO_INCREMENT NOT NULL,
        `nickname` varchar(100) NOT NULL UNIQUE,
        `ip_address` varchar(45) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT NOW(),
        PRIMARY KEY (`user_id`))
        CHARACTER SET utf8 COLLATE utf8_general_ci");

      $insert_statement = $pdo->query("INSERT INTO `users` (`nickname`, `ip_address`) VALUES ('$nickname', '$userIp') ON DUPLICATE KEY UPDATE ip_address='$userIp';");
      sendSuccess($success);
    }catch(PDOException $e){
      echo $e;
      $_SESSION['error'] = $serverr;
    }catch(Exception $e2){}
  }
  unset($pdo);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AntiBots verification</title>
  <script src='https://www.google.com/recaptcha/api.js'></script>
  <style>
    /*!Google fonts*/
    @import url('https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400&subset=latin-ext');

    /*! normalize.css v7.0.0 | MIT License | github.com/necolas/normalize.css */
    button,hr,input{overflow:visible}audio,canvas,progress,video{display:inline-block}progress,sub,sup{vertical-align:baseline}[type=checkbox],[type=radio],legend{box-sizing:border-box;padding:0}html{line-height:1.15;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}article,aside,details,figcaption,figure,footer,header,main,menu,nav,section{display:block}h1{font-size:2em;margin:.67em 0}figure{margin:1em 40px}hr{box-sizing:content-box;height:0}code,kbd,pre,samp{font-family:monospace,monospace;font-size:1em}a{background-color:transparent;-webkit-text-decoration-skip:objects}abbr[title]{border-bottom:none;text-decoration:underline;text-decoration:underline dotted}b,strong{font-weight:bolder}dfn{font-style:italic}mark{background-color:#ff0;color:#000}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative}sub{bottom:-.25em}sup{top:-.5em}audio:not([controls]){display:none;height:0}img{border-style:none}svg:not(:root){overflow:hidden}button,input,optgroup,select,textarea{font-family:sans-serif;font-size:100%;line-height:1.15;margin:0}button,select{text-transform:none}[type=reset],[type=submit],button,html [type=button]{-webkit-appearance:button}[type=button]::-moz-focus-inner,[type=reset]::-moz-focus-inner,[type=submit]::-moz-focus-inner,button::-moz-focus-inner{border-style:none;padding:0}[type=button]:-moz-focusring,[type=reset]:-moz-focusring,[type=submit]:-moz-focusring,button:-moz-focusring{outline:ButtonText dotted 1px}fieldset{padding:.35em .75em .625em}legend{color:inherit;display:table;max-width:100%;white-space:normal}textarea{overflow:auto}[type=number]::-webkit-inner-spin-button,[type=number]::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}[type=search]::-webkit-search-cancel-button,[type=search]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}[hidden],template{display:none}

    /*!Custom styles*/
    .container,.logo{text-align:center}*{box-sizing:inherit;font-family:inherit;font-size:inherit}body{font-family:'Roboto Condensed',sans-serif;background-color:#fcfcfc;box-sizing:border-box;font-weight:200}.logo,.title{font-weight:300}a{color:#2ecc71;text-decoration:none;outline:0}a:focus,a:hover{color:#0eaa4f;text-decoration:underline}.header{background-color:#2ecc71;width:100%;height:70px;padding:10px}.error,.success{padding:10px;background-color:#fcfcfc;margin-bottom:20px}.logo{position:relative;top:-20px;left:10px;color:#fcfcfc;letter-spacing:2px;width:100%}.container{margin-top:100px;width:20%;margin-left:auto;margin-right:auto;animation-name:appear;animation-duration:1s}.error,.form,.form>*,.success{width:100%}.success{color:#2ecc71;border:1px solid #2ecc71}.error{color:#d35400;border:1px solid #d35400}.form>*{margin-bottom:15px}.title{color:#333;font-size:24px}.form>input[type=submit],.form>input[type=text]{font-family:inherit;font-size:inherit;padding:10px;outline:0}.form>input[type=text]{border:1px solid #999;background-color:#fff}.form>input[type=text]:focus{background-color:#eafaf1;color:#229152;border:1px solid #229152;-webkit-box-shadow:0 0 28px -8px rgba(20,201,74,1);-moz-box-shadow:0 0 28px -8px rgba(20,201,74,1);box-shadow:0 0 28px -8px rgba(20,201,74,1)}.form>input[type=text]:hover{-webkit-box-shadow:0 0 28px -8px rgba(77,77,77,1);-moz-box-shadow:0 0 28px -8px rgba(77,77,77,1);box-shadow:0 0 28px -8px rgba(77,77,77,1)}.form>input[type=submit]{background-color:#2ecc71;color:#fcfcfc;border-top:1px solid #23ad5e;border-right:1px solid #23ad5e;border-bottom:#2ecc71;border-left:#2ecc71}.form>input[type=submit]:focus,.form>input[type=submit]:hover{-webkit-box-shadow:0 0 28px 0 rgba(6,201,65,.53);-moz-box-shadow:0 0 28px 0 rgba(6,201,65,.53);box-shadow:0 0 28px 0 rgba(6,201,65,.53)}.form>input[type=submit]:hover{cursor:pointer}::-webkit-input-placeholder{text-transform:uppercase;color:#999;opacity:.7;font-weight:200}:-moz-placeholder{text-transform:uppercase;color:#999;opacity:.7;forn-weight:200}::-moz-placeholder{text-transform:uppercase;color:#999;opacity:.7;font-weight:200}:-ms-input-placeholder:focus{text-transform:uppercase;color:#999;opacity:.7;font-weight:200}input:focus::-webkit-input-placeholder{color:#2cba69}input:focus:-moz-placeholder{color:#2cba69}input:focus::-moz-placeholder{color:#2cba69}input:focus:-ms-input-placeholder{color:#2cba69}@keyframes appear{from{opacity:0;margin-top:30px}to{margin-top:100px;opacity:1;top:50%}}@media (max-width:1250px){.container{width:25%}}@media (max-width:750px){.container{width:40%}}
  </style>
</head>
<body>
  <header class="header">
    <h1 class="logo"><?php echo $header; ?></h1>
  </header>
  <div class="container">
    <main role="main">
      <div class="form-box">
        <?php
          if(isset($_SESSION['error'])){
            echo '<div class="error">'.$_SESSION['error'].'</div>';
            unset($_SESSION['error']);
          }

          if(isset($_SESSION['success'])){
            echo '<div class="success">'.$_SESSION['success'].'</div>';
            unset($_SESSION['success']);
          }
        ?>
        <h2 class="title"><?php echo $title; ?></h2>
        <form method="post" class="form">
          <input placeholder="<?php echo $placeholder; ?>" id="nickname" name="nickname" type="text" required autofocus>
          <div class="g-recaptcha" data-sitekey="<?php echo $sitekey ?>"></div>
          <input type="submit" id="submit" name="submit" value="<?php echo $submit; ?>">
        </form>
        <a href="https://github.com/pietrek777/AntiBots" title="Visit project page">Powered by AntiBots Plugin</a>
      </div>
    </main>
  </div>
</body>
</html>
