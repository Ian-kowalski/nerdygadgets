<?php
function connectToDatabase() {
    $Connection = null;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set MySQLi to throw exceptions
    try {
        $Connection = mysqli_connect("localhost", "root", "", "nerdygadgets", "3306");
        mysqli_set_charset($Connection, 'latin1');
        $DatabaseAvailable = true;
    } catch (mysqli_sql_exception $e) {
        $DatabaseAvailable = false;
    }
    if (!$DatabaseAvailable) {
        die();
    }
    return $Connection;
}

$databaseConnection = connectToDatabase();
while(True){
    $now = date("Y-m-d H:i:s");                 // 2001-03-10 17:16:18 (the MySQL DATETIME format)
    $temp = rand(250, 350)/100;                         // rand voor een fake tempratuur

    mysqli_begin_transaction($databaseConnection);
    try {
        $coldroom_update=mysqli_prepare($databaseConnection, "
        UPDATE coldroomtemperatures 
        SET RecordedWhen = ?, Temperature = ?, ValidFrom = ? 
        WHERE coldRoomSensorNumber = 5;");              //de query voor het schrijven naar de data base
        mysqli_stmt_bind_param($coldroom_update, 'sds', $now , $temp, $now );
        mysqli_stmt_execute($coldroom_update);          // write temp to database

        mysqli_commit($databaseConnection);
    }catch (mysqli_sql_exception $exception) {
    $mysqli->rollback();
    throw $exception;
    }

    print($temp."\n");
    sleep(3);
}