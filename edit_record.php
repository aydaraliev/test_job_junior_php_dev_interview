<?php
//Authentification
  $username = 'admin';
  $password = 'letmein';

  if (isset($_SERVER['PHP_AUTH_USER']) &&
      isset($_SERVER['PHP_AUTH_PW']))
  {
    if ($_SERVER['PHP_AUTH_USER'] == $username &&
        $_SERVER['PHP_AUTH_PW']   == $password)
          echo "You are now logged in";
    else die("Invalid username / password combination");
  }
  else
  {
    die("Please <a href='users_manager.php'>click here</a> to log in.");
  }
  
//Database connection
  require_once 'sib_login.php';
  $connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);

  if ($connection->connect_error) die($connection->connect_error);
   

  if (isset($_POST['login']) && isset($_POST['edit']))
  {
    $login  = get_post($connection, 'login');
    $query  = "SELECT * FROM users WHERE login='$login'";
    $result = $connection->query($query);
  	if (!$result) echo "EDIT failed: $query<br>" . $connection->error . "<br><br>";
    $row = mysqli_fetch_assoc($result);
  }
  

  function get_post($connection, $var)
  {
    return $connection->real_escape_string($_POST[$var]);
  }
?> 
<!-- Form with predefined values -->
  <form action="users_manager.php" method="post"><pre>
    Login      <input type="text" name="login" value="<?=$row['login'];?>">
    Password   <input type="text" name="password" value="<?=$row['password'];?>">
    First name <input type="text" name="fname" value="<?=$row['fname'];?>">
    Last name  <input type="text" name="lname" value="<?=$row['lname'];?>">
    Birthday   <input type="date" name="bday" value="<?=$row['bday'];?>">
    Info       <textarea name = 'info' rows = "6" cols = "40"> <?=$row['info'];?> </textarea>
               <input type = "hidden" name = "edit" value = "yes">
           <input type="submit" value="EDIT RECORD">
  </pre></form>



