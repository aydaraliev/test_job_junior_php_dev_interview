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
  
  if (isset($_POST['info_b']) && isset($_POST['login']))
  {
    $login = get_post($connection, 'login');
    $query = "SELECT info FROM users WHERE login = '$login'";
    $result = $connection->query($query);
//info output  	
    echo mysqli_fetch_array($result)['info'];
      
      if (!$result) echo "DELETE failed: $query<br>" .
      $connection->error . "<br><br>";
  }


  function get_post($connection, $var)
  {
    return $connection->real_escape_string($_POST[$var]);
  }

?>
