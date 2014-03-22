<html>
    <head>        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">        

    <title>Register</title>

    <!-- Bootstrap core CSS -->
    <link href="static/bs/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="static/registration_login_form.css" rel="stylesheet">
    <script src="static/jquery.js"></script>
    
    </head>
    <body>          
<?php
include 'randomstring.php';
include 'connect.php';
    $register_submit = $_POST['submit'];    
    if($register_submit){
        $username = htmlentities($_POST['username']);
        $login = htmlentities(strtolower($_POST['login']));
        $email = htmlentities($_POST['email']);
        $passwordrepeat = (htmlentities($_POST['repeatpassword']));
        $password = (htmlentities($_POST['password']));
        if ($username && $login && $email && $password && $passwordrepeat){
            //Проверки username
            if (strlen($username) > 25){
                $error['username_max_limit'] = 'Max limit for username are 25 characters!';
            }
            $re = '/^[a-zA-Z\_]{1}[a-zA-Z0-9\_\h]{3,24}$/';           
            if (!preg_match($re, $username)){
                $error['username_incorrect'] = 'You can use only alphanumeric characters and the underscore for username!';
            }
            
            
            //Проверки login
            if (strlen($login) > 25 || strlen($login) < 4){
                $error['login_max_limit'] = 'Login must be between 4 or 25 characters!';;                
            }
            
            $re = '/^[a-zA-Z\_]{1}[a-zA-Z0-9\_]{3,24}$/';
            if (!preg_match($re, $login)){
                $error['login_incorrect'] = 'You can use only alphanumeric characters and the underscore for login!';
            }
                                                
            //Проверка email            
            $re = '/^\w+([\.\w]+)*\w@\w((\.\w)*\w+)*\.\w{2,3}$/'; 
            if (!preg_match($re, $email)){
                $error['email_incorrect'] = 'Email is incorrect!';
            }
                
            //Проверки пароля
            if ($password != $passwordrepeat){
                $error['pas_not_equal']='Your passwords do not match!';                
            }                       
            if (strlen($password) > 25  || strlen($password) < 6){
                $error['pas_no_suitable'] = 'Password must be between 6 or 25 characters!';
            }         
            $re = '/^[a-zA-Z0-9\_]{1}[a-zA-Z0-9\_\!\@\#\$\%\^\&\*\(\)\-\+\=\;\:\,\.\/\?\\\|\`\~\[\]\{\}]{5,24}$/'; 
            if (!preg_match($re, $password)){
                $error['password_incorrect'] = 'You can use only alphanumeric characters and the underscore for password!';
            }
            $salt = random_string(15);
            $password = sha1($password.$salt);                       
                                    
            
            //проверка занятости логина                       
            if (mysql_num_rows(mysql_query("
                SELECT login FROM users WHERE login='$login'
            ",$connection)) != 0){
                $error['login_taken'] = 'This login is already taken!';
            }
            
             
            //регистрация пользователя 
            if (!$error){
                mysql_query("
                    INSERT INTO users VALUES ('','$username','$login','$password','$salt','','','$email','','')
                ",$connection);                               
                echo "<h2>You are successfully registered! Go to <a href='index.php'>login page</a></h2>";
                return;
            }
        }else{            
            $error['error_str']='Please fill all fields!';                        
        }
    }   
?>

 <div class="container" id="container">
<?php
if ($error){
      echo '<form class="form-signin" role="form" method="POST">';
      echo  '<h2 class="form-signin-heading">Registration new user:</h2>';
      echo  '<h4 id="error_str" style="color: #B94A48">';
              foreach ($error as $key => $value) {
                  echo $value.'<br>';
              }          
       echo '</h4>';
       echo '<input type="text" class="form-control" placeholder="User full name" name="username" required autofocus value="'.$username.'">
        <input type="text" class="form-control" placeholder="Login" name="login" required value="'.$login.'">
        <input type="text" class="form-control" placeholder="Email" name="email" required value="'.$email.'">
        <input type="password" class="form-control" placeholder="Password"  name="password" required value="'.$_POST['password'].'">
        <input type="password" class="form-control" placeholder="Repeat your password" name="repeatpassword" required value="'.$_POST['repeatpassword'].'">  
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" value="submit">Register</button>        
        </form>';
       
}else{
      echo '<form class="form-signin" role="form" method="POST">';
      echo  '<h2 class="form-signin-heading">Registration new user:</h2>';
      echo  '<h4 id="error_str" style="color: #B94A48">';                     
       echo '</h4>';
       echo '<input type="text" class="form-control" placeholder="User full name" name="username" required autofocus>
        <input type="text" class="form-control" placeholder="Login" name="login" required>
        <input type="text" class="form-control" placeholder="Email" name="email" required>
        <input type="password" class="form-control" placeholder="Password"  name="password" required>
        <input type="password" class="form-control" placeholder="Repeat your password" name="repeatpassword" required>  
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" value="submit">Register</button>        
        </form>';  
}
?>
          
      </div>      
    </body>
</html>