<?php
// Initialize the session
include __DIR__ . '/header.php';

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$statement = mysqli_prepare($databaseConnection, "
                    SELECT CustomerName,PhoneNumber, DeliveryAddressLine1,DeliveryPostalCode,DeliveryLocation 
                    FROM  customers
                    join users on id=CustomerID 
                    where username = ?");
mysqli_stmt_bind_param($statement ,"s", $_SESSION["username"]);
mysqli_stmt_execute($statement);
$Result = mysqli_stmt_get_result($statement);
$row = mysqli_fetch_all($Result, MYSQLI_ASSOC);
$name=$row[0]["CustomerName"];
$tel=$row[0]["PhoneNumber"];
$address=$row[0]["DeliveryAddressLine1"];
$postCode=$row[0]["DeliveryPostalCode"];
$location=$row[0]["DeliveryLocation"]
?>

<h1 class="my-5">Hallo, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welkom terug.</h1>
<p>
    <?php
    print("klant gegevens<br>".$name."<br>".$tel."<br>".$address."<br>".$postCode."<br>".$location);
    ?>
    <br>
    <a href="browse.php" class="btn btn-success">Start shopping</a>
    <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
    <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>

</p>
<?php
include __DIR__ . "/footer.php";
?>