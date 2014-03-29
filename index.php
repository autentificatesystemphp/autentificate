<?php
session_start();
include 'randomstring.php';
include 'connect.php';

//редирект если уже залогинены и не заблокированы
if (isset($_SESSION['login']) && isset($_SESSION['session'])){
    //Проверить
   $query = mysql_query('SELECT id_session, blocking FROM users
       INNER JOIN entrance ON user_id = id_user 
       WHERE login="'.$_SESSION['login'].'" AND 
           entrance.IP="'.$_SERVER['REMOTE_ADDR'].'"', $connection);                
   $res = mysql_fetch_array($query, MYSQL_ASSOC);
   if ($res['id_session'] == $_SESSION['session']){
       if ($res['blocking'] == 0){ 
            Header("Location: login.php");
            die();       
       }else{
           $error['ban'] = 'You have exceeded the maximum number of login attempts!';
       }   
   }   
}
//по нажатию кнопки submit
$login_submit = $_POST['submit'];    
    if($login_submit){        
        $login = htmlentities(strtolower($_POST['login']));                
        $password = (htmlentities($_POST['password']));
        if ($login && $password){                                   
            //Проверки login
            if (strlen($login) > 25 || strlen($login) < 4){
                $error['login_max_limit'] = 'Login must be between 4 or 25 characters!';;                
            }
            
            $re = '/^[a-zA-Z\_]{1}[a-zA-Z0-9\_]{3,24}$/';
            if (!preg_match($re, $login)){
                $error['login_incorrect'] = 'You can use only alphanumeric characters and the underscore for login!';
            }
                                                                           
            //Проверки пароля                                 
            if (strlen($password) > 25  || strlen($password) < 6){
                $error['pas_no_suitable'] = 'Password must be between 6 or 25 characters!';
            }         
            $re = '/^[a-zA-Z0-9\_]{1}[a-zA-Z0-9\_\!\@\#\$\%\^\&\*\(\)\-\+\=\;\:\,\.\/\?\\\|\`\~\[\]\{\}]{5,24}$/';
            if (!preg_match($re, $password)){
                $error['password_incorrect'] = 'You can use only alphanumeric characters and the underscore for password!';
            }
            
            if (!$error){    
                //ПРоверяем существует ли пользователь.                                
                if (mysql_num_rows(mysql_query('SELECT user_id FROM users WHERE login="'.$login.'"',$connection)) != 0){
                    
                    $query = mysql_query('SELECT user_id, password, salt FROM users WHERE login="'.$login.'"', $connection);                
                    $res = mysql_fetch_array($query, MYSQL_ASSOC);
                    //Вытаскиваем блокировку отдельно т.к. при первом входе ее может еще не быть для данного IP
                    $query1= mysql_query('SELECT failed_attempts, blocking FROM entrance WHERE id_user="'.$res['user_id'].'" AND IP="'.$_SERVER['REMOTE_ADDR'].'"',$connection);
                    if ($query1)
                        $res1 = mysql_fetch_array($query1, MYSQLI_ASSOC);
                    if (!$res1){
                        mysql_query("INSERT INTO entrance VALUES('', '".$res['user_id']."', '".$_SERVER['REMOTE_ADDR']."','0','0')",$connection);
                        $res1['failed_attempts'] = 0;
                        $res1['blocking'] = 0;
                    }
                    if ($res1['blocking'] == 0){
                        if (($res['password'] == sha1($password.$res['salt']))){                        
                        //Логинимся
                            $_SESSION['session'] = sha1(random_string(40));
                            $_SESSION['login'] = $login;                            
                            mysql_query('UPDATE users SET id_session="'.$_SESSION['session'].'" WHERE login="'.$login.'"', $connection);                                                                                                    
                            mysql_query('UPDATE entrance SET failed_attempts="0" WHERE id_user="'.$res['user_id'].'" AND IP="'.$_SERVER['REMOTE_ADDR'].'"',$connection);
                            Header("Location: login.php");
                            die();
                        }else{                            
                            $query = mysql_query('SELECT id, failed_attempts, blocking FROM entrance WHERE id_user="'.$res['user_id'].'" AND IP="'.$_SERVER['REMOTE_ADDR'].'"',
                                    $connection);
                            $res = mysql_fetch_assoc($query);
                            if ($res['blocking'] == 0){
                               $res['failed_attempts'] =$res['failed_attempts']+1;
                               if ($res['failed_attempts'] > $ban_num){
                                   mysql_query('UPDATE entrance SET blocking="1" WHERE id="'.$res['id'].'"',$connection);
                               }else{
                                   mysql_query('UPDATE entrance SET failed_attempts="'.($res['failed_attempts']).'" WHERE id="'.$res['id'].'"',$connection);                                
                               }
                            }                            
                            $error['aut_failed'] = 'Autentifcation failed!';
                        }                
                    }else{
                        $error['ban'] = 'You have exceeded the maximum number of login attempts!';
                    }
                }else{
                    $error['user_not_exist'] = 'User with such login does not exist!';
                }            
            }            
        }else{            
            $error['error_str']='Please fill all fields!';                        
        }   
    }      
?>
<!DOCTYPE html>
<html>
    <head>        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">        

        <title>Login</title>

        <!-- Bootstrap core CSS -->
        <link href="static/bs/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="static/registration_login_form.css" rel="stylesheet">
    </head>
    <body>          
      <div class="container">
<?php
     if ($error){
        echo '<form class="form-signin" role="form" method="POST" action="index.php">';
        echo  '<h2 class="form-signin-heading">Registration new user:</h2>';
        echo  '<h4 id="error_str" style="color: #B94A48">';
              foreach ($error as $key => $value) {
                  echo $value.'<br>';
              }          
        echo '</h4>';
        echo '<h2 class="form-signin-heading">Please login:</h2>
                <input type="text" class="form-control" placeholder="Login" required autofocus name="login">
                <input type="password" class="form-control" placeholder="Password" required name="password">        
                <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" value="submit">Log in</button>
                <a class="btn btn-lg btn-primary btn-block" href="register.php">Register</a></form>';
      }else{
          echo '<form class="form-signin" role="form" method="POST" action="index.php">
                <h2 class="form-signin-heading">Please login:</h2>
                <input type="text" class="form-control" placeholder="Login" required autofocus name="login">
                <input type="password" class="form-control" placeholder="Password" required name="password">        
                <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" value="submit">Log in</button>
                <a class="btn btn-lg btn-primary btn-block" href="register.php">Register</a></form>';                   
      }
?>          
      </div>
    </body>   
</html>
<?php
    unset($_SESSION['login']);
    unset($_SESSION['session']);
    session_destroy();
?>