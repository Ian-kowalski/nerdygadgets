<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";

function getNAW(){
    return array(
        "Postcode"=> $_GET["postcode"],
        "plaats"=> $_GET["woonplaats"],
        "adres"=> $_GET ["straat"]." ".$_GET ["huisnr"],
        "name"=> $_GET["voornaam"] . " " . $_GET["achternaam"],
        "tel"=> $_GET["telefoonnummer"],
        "Gender"=>$_GET["gender"]
    );
}

saveOrder(getNAW(),$databaseConnection,$_SESSION['totaalPrijs'],$_SESSION['kortingID'],$_SESSION['GenKortingID']);
if($_SESSION['GenKortingID'] != NULL) {
    $statement = mysqli_prepare($databaseConnection, "
                    UPDATE GenDiscounts
                    SET Used = 'Yes'
                    WHERE UserID = (SELECT id FROM users WHERE username = ?)
                    ");
    mysqli_stmt_bind_param($statement ,"s", $_SESSION["username"]);
    mysqli_stmt_execute($statement);
}

session_unset();
?>
<!--<script>
    window.location.replace('https://www.ideal.nl/demo/qr/?app=ideal')
</script>-->
