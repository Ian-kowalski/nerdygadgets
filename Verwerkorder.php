<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";

function getNAW(){
    return array(
        "Postcode"=> $_GET[ "postalcode"],
        "plaats"=> $_GET[ "city"],
        "adres"=> $_GET [ "huisnr"]." ".$_GET [ "street"],
        "name"=> $_GET["fname"] . " " . $_GET["prefixes"] . " ".$_GET["lname"],
        //"email"=> $_GET["email"],
        "tel"=> $_GET["phone"],
    );
}
function CustomerExsists($CustomerName,$databaseConnection){
    $Query = "select CustomerID from customers where CustomerName=?";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "s", $CustomerName);
    mysqli_stmt_execute($Statement);
    $result=mysqli_stmt_get_result($Statement);
    $CustomerID=mysqli_fetch_all($result,MYSQLI_ASSOC);
    return $CustomerID[0]["CustomerID"];
}
function getCustomer($databaseConnection){
    $NAW=getNAW();
    extract($NAW, EXTR_OVERWRITE);
    $customerID=CustomerExsists($name,$databaseConnection);
    if($customerID!=NULL){
        return $customerID;
    }
    else{
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
        mysqli_stmt_bind_param($addToCustumer, 'isisssssss',
            $customerID, $name, $customerID,
            $tel, $tel,/*$email,*/
            $adres, $Postcode, $plaats,
            $adres, $Postcode
        );
        mysqli_stmt_execute($addToCustumer);

        return $customerID;
    }
}
function saveOrder($customerID,$databaseConnection){
    $cart=getCart();

    $statement = mysqli_prepare($databaseConnection, "
                SELECT MAX(OrderID) + 1 AS OrId -- Fetch highest known ID and increase by 1, save as OrId
                FROM Orders;");
    mysqli_stmt_execute($statement);
    $Result = mysqli_stmt_get_result($statement);
    $OrderID = mysqli_fetch_all($Result, MYSQLI_ASSOC); //Fetch result from SQL query
    $OrderID = $OrderID[0]["OrId"]; //Retrieve oderID from fetched array

    $addOrder = mysqli_prepare($databaseConnection, "INSERT INTO orders(
                      OrderID,CustomerID,SalespersonPersonID,ContactPersonID,
                      OrderDate, ExpectedDeliveryDate,
                      IsUndersupplyBackordered,
                      LastEditedBy,LastEditedWhen
                      )
values(?,?,1,1,
       current_date,(current_date+1),
       1,
       1,current_date
        )");
    mysqli_stmt_bind_param($addOrder, 'ii',$OrderID,$customerID);
    mysqli_stmt_execute($addOrder);


    $statement = mysqli_prepare($databaseConnection, "
                SELECT MAX(OrderLineID) + 1 AS OrLId -- Fetch highest known ID and increase by 1, save as OrId
                FROM OrderLines;");
    mysqli_stmt_execute($statement);
    $Result = mysqli_stmt_get_result($statement);
    $OrderLineID = mysqli_fetch_all($Result, MYSQLI_ASSOC); //Fetch result from SQL query
    $OrderLineID = $OrderLineID[0]["OrLId"]; //Retrieve oderID from fetched array

    foreach ($cart as $productID => $Quantity) {
            $StockItem = getStockItem($productID, $databaseConnection);
            $price = $StockItem["UnitPrice"];
            $tax= $StockItem["TaxRate"];
            $Description = $StockItem["SearchDetails"];
            $Package= $StockItem["UnitPackageID"];

            $addOrder = mysqli_prepare($databaseConnection, "INSERT INTO orderlines(
                OrderLineID, OrderID, StockItemID, 
                Description, PackageTypeID, 
                Quantity, UnitPrice, TaxRate, 
                PickedQuantity, 
                LastEditedBy, LastEditedWhen)
    values(?,?,?,
           ?,?,
           ?,?,?,
           0,1,current_date)");
            mysqli_stmt_bind_param($addOrder, 'iiisiidd',$OrderLineID,$OrderID,$productID,$Description,$Package,$Quantity,$price,$tax);
            mysqli_stmt_execute($addOrder);

            $OrderLineID++;
        }
    //save cart in orderline and custemer orderlins in orders
    // make oder
    // make for evry item a orderline
}

$customerID=getCustomer($databaseConnection);
saveOrder($customerID,$databaseConnection);

print($customerID);