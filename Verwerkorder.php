<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";

function getNAW(){
    return array(
        "Postcode"=> $_GET[ "postalcode"],
        "plaats"=> $_GET[ "city"],
        "adres"=> $_GET [ "huisnr"]." ".$_GET [ "street"],
        "name"=> $_GET["fname"] . " " . $_GET["prefixes"] . $_GET["lname"],
        //"email"=> $_GET["email"],
        "tel"=> $_GET["phone"],
    );
}

$NAW=getNAW();
$customerID=getCustomer($NAW,$databaseConnection);
saveOrder($customerID,$databaseConnection);

session_unset();
?>
<script>
    window.location.replace("https://www.ideal.nl/demo/qr/?app=ideal")
</script>