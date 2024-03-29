<?php
//Alle code hieronder is onderdeel van mijn (Leroy Vlug) nieuwe code voor de individuele herkansing

function getCode($n) {
    $characters = '01234567890123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
}
?>

<?php
$kortingPercentage = 0;

if(isset($_GET['discountCode'])) {
    $_SESSION['discountCode'] = $_GET['discountCode'];
    $statement = mysqli_prepare($databaseConnection, "
                                                        SELECT * FROM Discounts
                                                        WHERE Code = ?");
    mysqli_stmt_bind_param($statement, "s", $_GET['discountCode']);
    mysqli_stmt_execute($statement);
    $Result = mysqli_stmt_get_result($statement);
    $Discounts = mysqli_fetch_all($Result, MYSQLI_ASSOC);
    $GenStatement = mysqli_prepare($databaseConnection, "
                                                        SELECT * FROM GenDiscounts
                                                        WHERE GenCode = ? AND UserID = ?");
    mysqli_stmt_bind_param($GenStatement, "si", $_GET['discountCode'], $_SESSION['id']);
    mysqli_stmt_execute($GenStatement);
    $GenResult = mysqli_stmt_get_result($GenStatement);
    $GenDiscounts = mysqli_fetch_all($GenResult, MYSQLI_ASSOC);
    if ($Discounts != NULL || $GenDiscounts != NULL) {
        if ($Discounts != NULL) {
            $_SESSION['GenKortingID'] = NULL;
            if ($Discounts[0]['Status'] == "active") {
                $kortingPercentage = $Discounts[0]['DiscountPercentage'];
                $_SESSION['kortingID'] = $Discounts[0]['DiscountID'];
                echo('<script> alert("De kortingscode is toegepast!"); </script>');
            } elseif ($Discounts[0]['Status'] == "inactive") {
                echo('<script> alert("De kortingscode is niet geldig!"); </script>');
            }
        }
        if ($GenDiscounts != NULL) {
            $_SESSION['kortingID'] = NULL;
            if ($GenDiscounts[0]['Used'] == "No" && $GenDiscounts[0]['ValidTo'] > date("Y-m-d H:i:s")) {
                $kortingPercentage = $GenDiscounts[0]['GenDiscountPercentage'];
                $_SESSION['GenKortingID'] = $GenDiscounts[0]['GenDiscountID'];
                echo('<script> alert("De kortingscode is toegepast!"); </script>');
            } elseif ($GenDiscounts[0]['Used'] == "Yes") {
                echo('<script> alert("De kortingscode is al gebruikt!"); </script>');
            } elseif ($GenDiscounts[0]['ValidTo'] < date("Y-m-d H:i:s")) {
                echo('<script> alert("De kortingscode kan niet meer gebruikt worden!"); </script>');
            }
        }
    } else {
        echo('<script> alert("De kortingscode bestaat niet!"); </script>');
    }
} elseif (!isset($_GET['discountCode']) && $_SESSION['discountCode'] == "") {
    $_SESSION['discountCode'] = "";
}
?>