<?php
//функция генерирует случайную строку длины n+1
function random_string($num_chars){
    if ((is_numeric($num_chars))  && ($num_chars > 0)  && (! is_null($num_chars))){
        $password = "";
        $accepted_chars = "abcdefghijklmnopqrstuvwxyzl234567890";
        for  ($i=0;  $i<=$num_chars;  $i++)   {
        $random_number = rand(0,   (strlen($accepted_chars)-1)); $password .= $accepted_chars[$random_number];
    }
    return $password;
    }
}
?>
