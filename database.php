<?php
    $db_server = "sql102.infinityfree.com";
    $db_user = "if0_41829471";
    $db_pass = "********";
    $db_name = "if0_41829471_omar_marking_system";

    $conn = false;

    try {
        $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    } catch (mysqli_sql_exception $e) {
        $conn = false;
    }
?>