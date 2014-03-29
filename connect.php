<?php

            $connection = mysql_connect("localhost","dimp","pass123")
                    or die("Невозможно установить соединение: ". mysql_error());
            mysql_select_db("autentification");
            //Количество неудачных попыток
            $ban_num = 4;
?>
