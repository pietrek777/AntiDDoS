<?php
      $nickname = strtolower($_POST['nickname']);
      //Connecting with database
      $pdo->query("use ".$dbname);
      //Saving user in database
      sendSuccess($success);
    /*! normalize.css v7.0.0 | MIT License | github.com/necolas/normalize.css */
    /*!Custom styles*/
  <div class="container">
    <main role="main">
      <div class="form-box">
        <?php
          //A box with error messages.
          if(isset($_SESSION['error'])){
            echo '<div class="success">'.$_SESSION['success'].'</div>';