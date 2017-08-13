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
    header('WWW-Authenticate: Basic realm="Restricted Section"');
    header('HTTP/1.0 401 Unauthorized');
    die ("Please enter your username and password");
  }

//Connect to MySQL database  
  require_once 'sib_login.php';
  $connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);

  if ($connection->connect_error) die($connection->connect_error);

//Delete user
  if (isset($_POST['delete']) && isset($_POST['login']))
  {
    $login   = get_post($connection, 'login');
    $query  = "DELETE FROM users WHERE login='$login'";
    $result = $connection->query($query);

  	if (!$result) echo "DELETE failed: $query<br>" .
      $connection->error . "<br><br>";
  }
//Add user
  if (isset($_POST['login'])       &&
      isset($_POST['password'])    &&
      isset($_POST['fname'])       &&
      isset($_POST['lname'])       &&
      isset($_POST['bday'])        &&
      isset($_POST['info'])        &&
      isset($_POST['add']))
  {
    $login    = get_post($connection, 'login');
    $password = get_post($connection, 'password');
    $password = hash('ripemd128', $password);
    $fname    = get_post($connection, 'fname');
    $lname    = get_post($connection, 'lname');
    $bday     = get_post($connection, 'bday');
    $info     = get_post($connection, 'info');
    $query    = "INSERT INTO users VALUES" .
      "('$login', '$password', '$fname', '$lname', '$bday', '$info')";
    $result   = $connection->query($query);

  	if (!$result) echo "INSERT failed: $query<br>" .
      $connection->error . "<br><br>";
  }
//Edit user, form parameters from edit_record.php
  if (isset($_POST['login'])       &&
      isset($_POST['password'])    &&
      isset($_POST['fname'])       &&
      isset($_POST['lname'])       &&
      isset($_POST['bday'])        &&
      isset($_POST['info'])        &&
      isset($_POST['edit']))
      
  {
    $login    = get_post($connection, 'login');
    $password = get_post($connection, 'password');
    $password = hash('ripemd128', $password);
    $fname    = get_post($connection, 'fname');
    $lname    = get_post($connection, 'lname');
    $bday     = get_post($connection, 'bday');
    $info     = get_post($connection, 'info');
    $query    = "UPDATE users SET login = '$login', password = '$password', fname = '$fname', lname = '$lname', bday = '$bday', info = '$info'
                 WHERE login = '$login'";
    $result   = $connection->query($query);

  	if (!$result) echo "INSERT failed: $query<br>" .
      $connection->error . "<br><br>";
  }
//Add user form
  echo <<<_END
  <form action="users_manager.php" method="post"><pre>
    Login      <input type="text" name="login">
    Password   <input type="text" name="password">
    First name <input type="text" name="fname">
    Last name  <input type="text" name="lname">
    Birthday   <input type="date" name="bday">
    Info       <textarea name = 'info' rows = "6" cols = "40"> </textarea>
           <input type = "hidden" name = "add" value = "yes">
           <input type="submit" value="ADD RECORD">
  </pre></form>
_END;
//Sort users list form
  echo <<<_END
  <form name="sort" action="users_manager.php" method="post">
  <select name="order">
   <option value="choose">Make A Selection</option>
   <option value="login">Login</option>
   <option value="fname">First name</option>
   <option value="lname">Last name</option>
  </select>
  <input type="submit" value=" - Sort - " />
  </form> 
_END;
//Sort
  if (isset($_POST["order"]) == false)
  {
   $sort = "login";
  }
  else
  {
   $sort=$_POST["order"]!="choose"?$_POST["order"]:"login";
  }
//Pagination
  $results_per_page = 2;
  if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
  $start_from = ($page-1) * $results_per_page;
  $query_for_tot_rows = "SELECT COUNT(*) AS total FROM users";
  $tot_rows = $connection->query($query_for_tot_rows); 
     if (!$tot_rows) echo "PAGINATION failed: $query<br>" . $connection->error . "<br><br>";
  $rows_quantity = $tot_rows -> fetch_assoc();
  $total_pages = ceil($rows_quantity["total"] / $results_per_page);
  echo 'Page: ';
  for ($i=1; $i<=$total_pages; $i++) { 
    echo "<a href='users_manager.php?page=".$i."'>".$i."</a> "; 
    }; 
 
  $query = "SELECT * FROM users ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;

  $result = $connection->query($query);
  if (!$result) die ("Database access failed: " . $connection->error);

//Uer records output
  $rows = $result->num_rows;
 
  for ($j = 0 ; $j < $rows ; ++$j)
  {
    $result->data_seek($j);
    $row = $result->fetch_array(MYSQLI_NUM);

    echo <<<_END
  <pre>
     Login      $row[0]
     Password   $row[1]
     First Name $row[2]
     Last Name  $row[3]
     Birthday   $row[4]

  </pre>
  <form action="users_manager.php" method="post">
  <input type="hidden" name="delete" value="yes">
  <input type="hidden" name="login" value="$row[0]">
  <input type="submit" value="DELETE RECORD"></form>
  
  <form action = "edit_record.php" method = "post">
  <input type = "hidden" name = "edit" value = "yes">
  <input type = "hidden" name = "login" value = "$row[0]">
  <input type = "submit" value = "EDIT RECORD"></form>

  <form action = "view_info.php" method = "post">
  <input type  = "hidden" name = "info_b" value = "yes">
  <input type = "hidden" name = "login" value = "$row[0]">
  <input type = "submit" value = "VIEW INFO"></form>
_END;
  }

  $result->close();
  $connection->close();
 
//SQL request sanitation
  function get_post($connection, $var)
  {
    return $connection->real_escape_string($_POST[$var]);
  }
?>
