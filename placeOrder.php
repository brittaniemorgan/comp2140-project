<?php
    session_start();
    require "DBManager.php";
    $host = 'localhost';
    $username = 'boardwalk_user';
    $password = 'password123';
    $dbname = 'cafeInfo';
    $db = new DBManager($host, $username, $password, $dbname);
    $conn = $db -> getConn();
    $db->addOrder($_POST['total'], $_POST['items'], $_POST['glocation'], $_POST['address'], $_SESSION['user'][0]);
    $_SESSION['user'][2] = [];
    
    
?>
