<!-- dit bestand bevat alle code die verbinding maakt met de database -->
<?php

function connectToDatabase() {
    $Connection = null;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set MySQLi to throw exceptions
    try {
        $Connection = mysqli_connect("localhost", "root", "", "nerdygadgets");
        mysqli_set_charset($Connection, 'latin1');
        $DatabaseAvailable = true;
    } catch (mysqli_sql_exception $e) {
        $DatabaseAvailable = false;
    }
    if (!$DatabaseAvailable) {
        ?><h2>Website wordt op dit moment onderhouden.</h2><?php
        die();
    }

    return $Connection;
}

function getHeaderStockGroups($databaseConnection) {
    $Query = "
                SELECT StockGroupID, StockGroupName, ImagePath
                FROM stockgroups 
                WHERE StockGroupID IN (
                                        SELECT StockGroupID 
                                        FROM stockitemstockgroups
                                        ) AND ImagePath IS NOT NULL
                ORDER BY StockGroupID ASC";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $HeaderStockGroups = mysqli_stmt_get_result($Statement);
    return $HeaderStockGroups;
}

function getStockGroups($databaseConnection) {
    $Query = "
            SELECT StockGroupID, StockGroupName, ImagePath
            FROM stockgroups 
            WHERE StockGroupID IN (
                                    SELECT StockGroupID 
                                    FROM stockitemstockgroups
                                    ) AND ImagePath IS NOT NULL
            ORDER BY StockGroupID ASC";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $Result = mysqli_stmt_get_result($Statement);
    $StockGroups = mysqli_fetch_all($Result, MYSQLI_ASSOC);
    return $StockGroups;
}

function getStockItem($id, $databaseConnection) {
    $Result = null;

    $Query = " 
           SELECT SI.StockItemID, 
            (round(RecommendedRetailPrice*(1+(TaxRate/100)),2)) AS SellPrice, 
            StockItemName,
            QuantityOnHand,
            SearchDetails, 
            TaxRate,
            UnitPrice,
            UnitPackageID,
            IsChillerStock,
            (CASE WHEN (RecommendedRetailPrice*(1+(TaxRate/100))) > 50 THEN 0 ELSE 6.95 END) AS SendCosts, MarketingComments, CustomFields, SI.Video,
            (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath   
            FROM stockitems SI 
            JOIN stockitemholdings SIH USING(stockitemid)
            JOIN stockitemstockgroups ON SI.StockItemID = stockitemstockgroups.StockItemID
            JOIN stockgroups USING(StockGroupID)
            WHERE SI.stockitemid = ?
            GROUP BY StockItemID";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    if ($ReturnableResult && mysqli_num_rows($ReturnableResult) == 1) {
        $Result = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC)[0];
    }

    return $Result;
}

function getStockItemImage($id, $databaseConnection) {
    $Query = "
                SELECT ImagePath
                FROM stockitemimages 
                WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $Result = mysqli_stmt_get_result($Statement);
    $Result = mysqli_fetch_all($Result, MYSQLI_ASSOC);

    return $Result;
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
function updateCustumer($customerID,$NAW,$databaseConnection){
    extract($NAW, EXTR_OVERWRITE);
    $addToCustumer = mysqli_prepare($databaseConnection, "
                UPDATE customers
                SET PhoneNumber=?,FaxNumber=?,DeliveryAddressLine1=?,DeliveryPostalCode=?,DeliveryLocation=?,PostalAddressLine1=?,PostalPostalCode=?,Gender=?
                WHERE CustomerID=?;
"
    );
    mysqli_stmt_bind_param($addToCustumer, 'ssssssssi', $tel, $tel,$adres, $Postcode, $plaats, $adres, $Postcode,$Gender,$customerID);
    mysqli_stmt_execute($addToCustumer);
}
function insurtCustumer($NAW,$databaseConnection){
    extract($NAW, EXTR_OVERWRITE);
    $statement = mysqli_prepare($databaseConnection, "
                    SELECT MAX(CustomerID) + 1 AS CstId -- Fetch highest known ID and increase by 1, save as CstId
                    FROM customers;");
    mysqli_stmt_execute($statement);
    $Result = mysqli_stmt_get_result($statement);
    $customerID = mysqli_fetch_all($Result, MYSQLI_ASSOC); //Fetch result from SQL query
    $customerID = $customerID[0]["CstId"]; //Retrieve customerID from fetched array

    $addToCustumer = mysqli_prepare($databaseConnection, "
            INSERT INTO customers(CustomerID,CustomerName,BillToCustomerID,CustomerCategoryID,PrimaryContactPersonID,DeliveryMethodID,DeliveryCityID,PostalCityID,AccountOpenedDate,StandardDiscountPercentage,IsStatementSent,IsOnCreditHold,PaymentDays,PhoneNumber,FaxNumber,WebsiteURL,DeliveryAddressLine1,DeliveryPostalCode,DeliveryLocation,PostalAddressLine1,PostalPostalCode,LastEditedBy,ValidFrom,ValidTo,Gender) 
            values(?,?,?,1,1,2,776,776,CURRENT_TIMESTAMP,0.000,0,0,7,?,?,'www.windesheim.nl',?,?,?,?,?,1,CURRENT_TIMESTAMP,'9999-12-31 23:59:59',? )"
    );
    mysqli_stmt_bind_param($addToCustumer, 'isisssssss', $customerID, $name, $customerID, $tel, $tel,$adres, $Postcode, $plaats, $adres, $Postcode,$Gender);
    mysqli_stmt_execute($addToCustumer);

    return $customerID;
}
function getCustemer($NAW,$databaseConnection){
        extract($NAW, EXTR_OVERWRITE);
        $customerID = CustomerExsists($name, $databaseConnection);
        if ($customerID == NULL) {
            $customerID=insurtCustumer($NAW,$databaseConnection);
        }else{
            updateCustumer($customerID,$NAW,$databaseConnection);
        }
        return $customerID;

}
function saveOrder($NAW,$databaseConnection){
    $cart=getCart();
    mysqli_begin_transaction($databaseConnection);
    try { //aanmaken costumer
        $customerID=getCustemer($NAW,$databaseConnection);
        $statement = mysqli_prepare($databaseConnection, "
                    SELECT MAX(OrderID) + 1 AS OrId -- Fetch highest known ID and increase by 1, save as OrId
                    FROM Orders;");
        mysqli_stmt_execute($statement);
        $Result = mysqli_stmt_get_result($statement);
        $OrderID = mysqli_fetch_all($Result, MYSQLI_ASSOC); //Fetch result from SQL query
        $OrderID = $OrderID[0]["OrId"]; //Retrieve oderID from fetched array

        $statement = mysqli_prepare($databaseConnection, "
                    SELECT MAX(OrderLineID) + 1 AS OrLId -- Fetch highest known ID and increase by 1, save as OrId
                    FROM OrderLines;");
        mysqli_stmt_execute($statement);
        $Result = mysqli_stmt_get_result($statement);
        $OrderLineID = mysqli_fetch_all($Result, MYSQLI_ASSOC); //Fetch result from SQL query
        $OrderLineID = $OrderLineID[0]["OrLId"]; //Retrieve oderID from fetched array

        $addOrder = mysqli_prepare($databaseConnection,
            "INSERT INTO orders(
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
        mysqli_stmt_bind_param($addOrder, 'ii', $OrderID, $customerID);
        mysqli_stmt_execute($addOrder);

        foreach ($cart as $productID => $Quantity) {
            $StockItem = getStockItem($productID, $databaseConnection);
            $price = $StockItem["UnitPrice"];
            $tax = $StockItem["TaxRate"];
            $Description = $StockItem["SearchDetails"];
            $Package = $StockItem["UnitPackageID"];

            $addOrderLine = mysqli_prepare($databaseConnection, "
                INSERT INTO orderlines(OrderLineID, OrderID, StockItemID, Description, PackageTypeID, Quantity, UnitPrice, TaxRate, PickedQuantity, LastEditedBy, LastEditedWhen)
                values(?,?,?,?,?,?,?,?,0,1,current_date);"
            );

            mysqli_stmt_bind_param($addOrderLine, 'iiisiidd', $OrderLineID, $OrderID, $productID, $Description, $Package, $Quantity, $price, $tax);
            mysqli_stmt_execute($addOrderLine);
            $stockUpdate = mysqli_prepare($databaseConnection, "
                UPDATE stockitemholdings
                SET QuantityOnHand = (QuantityOnHand-?)
                WHERE StockItemID=?;"
            );
            mysqli_stmt_bind_param($stockUpdate, 'ii', $Quantity, $productID);
            mysqli_stmt_execute($stockUpdate);

            $OrderLineID++;
        }
        //save cart in orderline and custemer orderlins in orders
        // make oder
        // make for evry item a orderline

        mysqli_commit($databaseConnection);
    }catch (mysqli_sql_exception $exception) {
        mysqli_rollback($databaseConnection);

        throw $exception;
    }


}

function temp($databaseConnection){
    $satment=mysqli_prepare($databaseConnection, "SELECT Temperature as temp FROM coldroomtemperatures WHERE ColdRoomSensorNumber=5");
    mysqli_stmt_execute($satment);
    $Result = mysqli_stmt_get_result($satment);
    $Result = mysqli_fetch_all($Result, MYSQLI_ASSOC);

    return $Result[0]['temp'];
}

function filteren($queryBuildResult,$Sort,$ProductsOnPage, $Offset, $databaseConnection){
    $Query_sort = "
                SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, TaxRate, RecommendedRetailPrice, ROUND(TaxRate * RecommendedRetailPrice / 100 + RecommendedRetailPrice,2) as SellPrice,
                QuantityOnHand,
                (SELECT ImagePath
                FROM stockitemimages
                WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath,
                (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath
                FROM stockitems SI
                JOIN stockitemholdings SIH USING(stockitemid)
                JOIN stockitemstockgroups SIG USING(StockItemID)
                JOIN stockgroups SG USING(StockGroupID)
                WHERE ".$queryBuildResult."
                GROUP BY StockItemID
                ORDER BY ".$Sort."
                LIMIT ?  OFFSET ?";
    $Statement = mysqli_prepare($databaseConnection, $Query_sort);
    mysqli_stmt_bind_param($Statement, "ii",$ProductsOnPage, $Offset);

    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    $ReturnableResult = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC);
    return $ReturnableResult;
}

function row($queryBuildResult,$Sort, $databaseConnection){
    $Query_count = "
                select count(*)
                FROM stockitems SI
                JOIN stockitemholdings SIH USING(stockitemid)
                JOIN stockitemstockgroups SIG USING(StockItemID)
                JOIN stockgroups SG USING(StockGroupID)
                WHERE $queryBuildResult";

    $Statement = mysqli_prepare($databaseConnection, $Query_count);

    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    $ReturnableResult = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC);
    return $ReturnableResult;
}

function filteren_zonder($Sort,$ProductsOnPage, $Offset, $databaseConnection){
    $Query_sort = "
                SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, TaxRate, RecommendedRetailPrice, ROUND(TaxRate * RecommendedRetailPrice / 100 + RecommendedRetailPrice,2) as SellPrice,
                QuantityOnHand,
                (SELECT ImagePath
                FROM stockitemimages
                WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath,
                (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath
                FROM stockitems SI
                JOIN stockitemholdings SIH USING(stockitemid)
                JOIN stockitemstockgroups SIG USING(StockItemID)
                JOIN stockgroups SG USING(StockGroupID)
                GROUP BY StockItemID
                ORDER BY ".$Sort."
                LIMIT ?  OFFSET ?";
    $Statement = mysqli_prepare($databaseConnection, $Query_sort);
    mysqli_stmt_bind_param($Statement, "ii",$ProductsOnPage, $Offset);

    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    $ReturnableResult = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC);
    return $ReturnableResult;

}
function row_zonder($Sort, $databaseConnection){
    $Query_count = "
                select count(*)
                FROM stockitems SI
                JOIN stockitemholdings SIH USING(stockitemid)
                JOIN stockitemstockgroups SIG USING(StockItemID)
                JOIN stockgroups SG USING(StockGroupID)";

    $Statement = mysqli_prepare($databaseConnection, $Query_count);

    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    $ReturnableResult = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC);
    return $ReturnableResult;
}