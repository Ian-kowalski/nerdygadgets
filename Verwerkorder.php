<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";

function getNAW(){
    return array(
        "Postcode"=> $_GET["postcode"],
        "plaats"=> $_GET["woonplaats"],
        "adres"=> $_GET ["huisnr"]." ".$_GET ["straat"],
        "name"=> $_GET["voornaam"] . " " . $_GET["achternaam"],
        "tel"=> $_GET["telefoonnummer"],
    );
}

saveOrder(getNAW(),$databaseConnection);

session_unset();
?>
