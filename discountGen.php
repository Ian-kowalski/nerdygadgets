<?php
//Alle code hieronder is onderdeel van mijn (Leroy Vlug) nieuwe code voor de individuele herkansing

include __DIR__ . '/header.php';
include "discountFunctions.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$statement = mysqli_prepare($databaseConnection, "
                    SELECT GenDiscountID, UserID, ValidTo
                    FROM  GenDiscounts
                    join users on id=UserID 
                    where username = ?");
mysqli_stmt_bind_param($statement ,"s", $_SESSION["username"]);
mysqli_stmt_execute($statement);
$Result = mysqli_stmt_get_result($statement);
$row = mysqli_fetch_all($Result, MYSQLI_ASSOC);

if($row == NULL) {
    $statement = mysqli_prepare($databaseConnection, "
                     INSERT INTO GenDiscounts (GenCode, GenDiscountPercentage, UserID, Used, ValidTo)
                     VALUES('None',0,(SELECT id FROM users WHERE username = ?),'Yes',current_time)");
    mysqli_stmt_bind_param($statement ,"s", $_SESSION["username"]);
    mysqli_stmt_execute($statement);
}
?>
<h3>Je kortingscode voor vandaag</h3>

<?php

$n=10;

$ValidToStatement = mysqli_prepare($databaseConnection, "
                    SELECT GenCode,GenDiscountPercentage,Used,ValidTo
                    FROM  GenDiscounts
                    join users on id=UserID 
                    where username = ?");
mysqli_stmt_bind_param($ValidToStatement ,"s", $_SESSION["username"]);
mysqli_stmt_execute($ValidToStatement);
$ValidToResult = mysqli_stmt_get_result($ValidToStatement);
$ValidToRow = mysqli_fetch_all($ValidToResult, MYSQLI_ASSOC);

if(isset($_GET['personalDiscount']) && $ValidToRow[0]['ValidTo'] < date("Y-m-d H:i:s")) {
    if ($_GET['personalDiscount']) {
        $codeGen = getCode($n);
        $codeGenPercentage = rand(5, 15)/100;
        echo ($codeGen . ": " . $codeGenPercentage * 100 . "%");
        $statement = mysqli_prepare($databaseConnection, "
                    UPDATE GenDiscounts
                    SET GenCode = ?, Used = 'No', GenDiscountPercentage = ?, ValidFrom = current_time, ValidTo = CURRENT_TIME()+INTERVAL 1 DAY
                    WHERE UserID = (SELECT id FROM users WHERE username = ?)
                    ");
        mysqli_stmt_bind_param($statement ,"sds", $codeGen, $codeGenPercentage, $_SESSION["username"]);
        mysqli_stmt_execute($statement);
        echo("<br>Vergeet niet om je code te kopiëren!");
    }
} elseif($ValidToRow[0]['ValidTo'] > date("Y-m-d H:i:s") && $ValidToRow[0]['Used'] == "No") {
    echo("Je hebt je dagelijkse code al gegenereerd!");
    echo("<br>De code die je eerder had gegenereerd is: " . $ValidToRow[0]['GenCode'] . " voor " . $ValidToRow[0]['GenDiscountPercentage'] * 100 . "% korting.");
    echo("<br>Vergeet niet om je code te kopiëren!");
} elseif($ValidToRow[0]['Used'] == "Yes") {
    echo("Je hebt je code al gebruikt! <br>Kom morgen weer terug om een nieuwe te genereren!");
} else {
        echo("Je hebt nog geen code gegenereerd. Klik op de knop hieronder om dat te doen!");
}
?>

<form method="get" class="mt-3">
    <input type="submit" class='button button1' value="genereer code" name="personalDiscount">
</form>

