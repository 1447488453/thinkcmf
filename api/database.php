<?php


if(file_exists(ROOT_PATH."data/conf/database.php")){
    $database=include ROOT_PATH."data/conf/database.php";
}else{
    $database=[];
}

return $database;
