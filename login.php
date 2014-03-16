<?php
    session_start();   
    include 'connect.php';
    function logout($connection){
        $date = date('Y-m-d');
        $ip = $_SERVER['REMOTE_ADDR'];
        mysql_query('UPDATE users SET IP="'.$ip.'", date_last_login="'.$date.'" WHERE login="'.$_SESSION['login'].'"', $connection);
        unset($_SESSION['login']);
        unset($_SESSION['session']);
        session_destroy();
        Header("Location: index.php");
        die();
    }
   
//Проверка login на случай sql-инъекции при подделке сессии
    if (strlen($_SESSION['login']) > 25 || strlen($_SESSION['login']) < 4){
        $error['login_max_limit'] = 'Login must be between 4 or 25 characters!';;                
    }            
    $re = '/^[a-zA-Z\_]{1}[a-zA-Z0-9\_]{3,24}$/';
    if (!preg_match($re, $_SESSION['login'])){
        $error['login_incorrect'] = 'You can use only alphanumeric characters and the underscore for login!';
    }
    
    if (!isset($error)){
    //Проверка существования login
        $query = mysql_query('SELECT user_id, id_session, role, username, IP, date_last_login, blocking FROM users WHERE login="'.$_SESSION['login'].'"', $connection);                
        $res = mysql_fetch_array($query, MYSQL_ASSOC);
        if ($res['blocking'] == 1){
             unset($_SESSION['login']);
             unset($_SESSION['session']);
             session_destroy();
        }
    }
    //Существует ли такой login и актуален ли идентификатор сессии 
    if (isset($res) && $res['id_session'] == $_SESSION['session']){
        //Все ок. 
        //logout
        if (isset($_POST['logout'])){
            logout($connection);
        }
        $date = date('Y-m-d');
        $role = $res['role'];
        $query1 = mysql_query('SELECT role FROM role WHERE id_role="'.$role.'"', $connection);                
        $res1 = mysql_fetch_array($query1, MYSQL_ASSOC);
        if ($role == 1){
            //Нажали кнопку сохранить роли
            if (isset($_POST['save_roles'])){
                $query2 = mysql_query('SELECT id_role, role FROM role', $connection);
                while($res2 = mysql_fetch_array($query2, MYSQL_ASSOC)){
                    $roles[$res2['role']] = $res2['id_role'];
                }
                
                $query2 = mysql_query('SELECT login FROM users', $connection);                
                while($res2 = mysql_fetch_array($query2, MYSQL_ASSOC)){                                        
                    mysql_query('UPDATE users SET role="'.$roles[$_POST[$res2['login']]].'" WHERE login="'.$res2['login'].'"', $connection);
                }
                
                $query = mysql_query('SELECT user_id, id_session, role, username, IP, date_last_login FROM users WHERE login="'.$_SESSION['login'].'"', $connection);                
                $res = mysql_fetch_array($query, MYSQL_ASSOC);
                $role = $res['role'];
                $query1 = mysql_query('SELECT role FROM role WHERE id_role="'.$role.'"', $connection);                
                $res1 = mysql_fetch_array($query1, MYSQL_ASSOC);
            }
            

            //нажали на кнопку сохранить попытки
            if (isset($_POST['save_attempts'])){
                $re = '/^[0-9]{1,11}$/';
                $query2 = mysql_query('SELECT id_user FROM entrance',$connection);
                while($res2 = mysql_fetch_array($query2, MYSQL_ASSOC)){
                    if (!preg_match($re,$_POST[$res2['id_user']])){
                        $error['attempts_incorrect'] = 'Attempts incorrect!';
                    }
                    if (!isset($error)){
                        $attempt = $_POST[$res2['id_user']];
                        if ($attempt == 0){
                            mysql_query('UPDATE entrance SET IP=NULL WHERE id_user="'.$res2['id_user'].'"',$connection);
                        }
                        mysql_query('UPDATE entrance SET failed_attempts="'.$attempt.'" WHERE id_user="'.$res2['id_user'].'"',$connection);
                        if ($attempt <= $ban_num){
                            mysql_query('UPDATE users SET blocking=0 WHERE user_id="'.$res2['id_user'].'"',$connection);
                        }else{
                            mysql_query('UPDATE users SET blocking=1 WHERE user_id="'.$res2['id_user'].'"',$connection);
                        }
                        
                    }
                    $error = NULL;
                }
                
                
            }
            
        }
        
      }else{
        unset($_SESSION['login']);
        unset($_SESSION['session']);
        session_destroy();
        Header("Location: index.php");
        die();
    }  
    
?>  

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">


    <title>Login page</title>

    <!-- Bootstrap core CSS -->
    <link href="static/bs/css/bootstrap.min.css" rel="stylesheet">
    
     
    <link href="static/navbar.css" rel="stylesheet"> 
     
    
     <script src="static/jquery.min.js"></script>
     <script src="static/bs/js/bootstrap.min.js"></script>
             
  </head>
  <body>

    <div class="container">
      <div class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
          <div class="navbar-header">            
            <a class="navbar-brand">
                Login: <?php echo $_SESSION['login']; ?>
            </a>
          </div>
         
          <div class="navbar-collapse collapse">           
            <ul class="nav navbar-nav navbar-right">
              <form class="navbar-form" role="search" action="./login.php" method="POST">                    
                    <input type="submit" class="btn btn-default" name="logout" value="Logout">
              </form>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </div>

      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron" style="background-color:white">        
        <p>Hello, <?php echo $res['username']; ?>! You are logged in <?php echo $date; ?>, 
            your IP-address: <?php echo $_SERVER['REMOTE_ADDR']; ?>.
            Your last visit was in <?php echo $res['date_last_login']; ?> with IP-addresses <?php echo $res['IP'] ?>. 
            Your role: <?php echo $res1['role'];?>.</p>        
<?php 
    if ($role == 1){
        $i = 1;
        
        echo '<table class="table table-hover">';
        echo    '<thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Login</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>';
        echo '<tbody>';
        $query2 = mysql_query('SELECT username, login, email, role FROM users',$connection);
        echo '<form role="form" action="./login.php" method="POST">';
        while($res2 = mysql_fetch_array($query2,MYSQL_ASSOC)){
            $query3 = mysql_query('SELECT id_role, role FROM role',$connection);
            echo '<tr>';
                        echo '<td>'.$i.'</td>';
                        echo '<td>'.$res2['username'].'</td>';
                        echo '<td>'.$res2['login'].'</td>';
                        echo '<td>'.$res2['email'].'</td>';                       
                        echo '  <td><select class="form-control" name="'.$res2['login'].'">';
                        while ($res3 = mysql_fetch_array($query3,MYSQL_ASSOC)){
                            if ($res3['id_role'] == $res2['role']){
                                echo '<option selected value="'.$res3['role'].'">';
                            }else{
                                echo '<option value="'.$res3['role'].'">';
                            }
                            echo $res3['role'];
                            echo '</option>';
                        }
                        echo   '</select>';
                        echo  '</td>';
            echo '</tr>';
            $i = $i+1;    
        }
        
        
        echo '<tr><td colspan=5 align="right">
                    <input type="submit" class="btn btn-default class="btn"" name="save_roles" value="Save roles">
                  </td>
              <tr>';
        echo '</form>';
        echo '</tbody></table>';
        
        
        
        $i=1;        
        echo '<table class="table table-hover">';
        echo    '<thead>
                    <tr>
                        <th>#</th>
                        <th>ID user</th>
                        <th>Login</th>
                        <th>IP</th>
                        <th>Login attempts</th>
                    </tr>
                </thead>';
        echo '<form role="form" action="./login.php" method="POST">';
        $query1 = mysql_query('SELECT id_user, entrance.IP, failed_attempts, login FROM entrance JOIN users ON id_user=user_id', $connection);
        while($res1 = mysql_fetch_array($query1,MYSQL_ASSOC)){
            echo '<tr>';
                        echo '<td>'.$i.'</td>';
                        echo '<td>'.$res1['id_user'].'</td>';
                        echo '<td>'.$res1['login'].'</td>';
                        echo '<td>'.$res1['IP'].'</td>'; 
                        echo '<td>                             
                                <input type="text" class="form-control" name="'.$res1['id_user'].'" value="'.$res1['failed_attempts'].'">
                           </td>';
                        $i = $i+1;
            echo '</tr>';
        }
        echo '<tr><td colspan=5 align="right">
                    <input type="submit" class="btn btn-default class="btn"" name="save_attempts" value="Save attempts">
                  </td>
              <tr>';    
        echo '</form>';
        echo '<tbody>';
        echo '</tbody></table>';
    }
?>      
                 
      </div>                
    </div> <!-- /container -->

   
  </body>
</html>