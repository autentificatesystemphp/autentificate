<?php

            $connection = mysql_connect("localhost","root","") or die("Невозможно установить соединение: ". mysql_error());
            mysql_select_db("autentifcation");
            //Количество неудачных попыток
            $ban_num = 4;
?>
