<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";


$cart=getCart();
print_r($cart);

function getNAW(){
    return array(
        "Postcode"=> $_GET[ "postalcode"],
        "plaats"=> $_GET[ "city"],
        "adres"=> $_GET [ "huisnr"]." ".$_GET [ "street"],
        "name"=> $_GET["fname"]." ".$_GET["prefixes"]." ".$_GET["lname"],
        "email"=> $_GET["email"],
        "Telefoonnummer"=> $_GET["phone"],
        /*"tel"=> $_GET["tel"]*/
    );
}
function getOrder(){

}
function saveCustomer($NAW, $databaseConnection)
{
    extract($NAW, EXTR_OVERWRITE);

    $statement = mysqli_prepare($databaseConnection, "
                SELECT MAX(CustomerID) + 1 AS CstId -- Fetch highest known ID and increase by 1, save as CstId
                FROM customers;");
    mysqli_stmt_execute($statement);
    $Result = mysqli_stmt_get_result($statement);
    $customerID = mysqli_fetch_all($Result, MYSQLI_ASSOC); //Fetch result from SQL query
    $customerID = $customerID[0]["CstId"]; //Retrieve customerID from fetched array

    $addToCustumer = mysqli_prepare($databaseConnection, "INSERT INTO customers
    (
        CustomerID,CustomerName,BillToCustomerID,CustomerCategoryID,
        PrimaryContactPersonID,
        DeliveryMethodID,DeliveryCityID,PostalCityID,
        AccountOpenedDate,
        StandardDiscountPercentage,
        IsStatementSent,
        IsOnCreditHold,
        PaymentDays,
        PhoneNumber,FaxNumber,WebsiteURL,
        DeliveryAddressLine1,DeliveryPostalCode,DeliveryLocation,
        PostalAddressLine1,PostalPostalCode,
        LastEditedBy,
        ValidFrom,
        ValidTo) 
        values(
        ?,?,?,1,
        1,
        2,776,776,
        CURRENT_TIMESTAMP,
        0.000,
        0,
        0,
        7,
        ?,
        ?,
        'www.windesheim.nl',
        ?,
        ?,
        ?,
        ?,
        ?,
        1,
        CURRENT_TIMESTAMP,
        '9999-12-31 23:59:59' 
        )");
    mysqli_stmt_bind_param($addToCustumer, 'isii',
        $customerID, $name, $customerID,
        $tel, $tel,
        $adres, $Postcode, $cords,
        $Postcode, $plaats
    );
    mysqli_stmt_execute($addToCustumer);
}
function saveOrder($order,$databaseConnection){
    extract($order, EXTR_OVERWRITE);

    //save cart in orderline and custemer orderlins in orders
}