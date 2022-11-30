<?php
$nawgegevens= array(
    "land" => $_GET[ "country"],
    "Postcode"=> $_GET[ "postalcode"],
    "Huisnummer & toevoeging"=> $_GET [ "huisnr"],
    "plaats"=> $_GET[ "city"],
    "straatnaam"=> $_GET [ "street"],
);

print_r($nawgegevens);
//helo
