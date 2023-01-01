<?php
include __DIR__ . "/header.php";
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$bestaandeklant=false;
$CustomerID=0;
$username = $password = $confirm_password = $voornaam = $achternaam = "";
$username_err = $password_err = $confirm_password_err = $voornaam_err = $achternaam_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    if(empty(trim($_POST["voornaam"]))||empty(trim($_POST["achternaam"]))){
        //validate voornaam
        if(empty(trim($_POST["voornaam"]))){
            $voornaam_err = "Please enter first name.";
        }
        //validate achternaam
        if(empty(trim($_POST["achternaam"]))){
            $achternaam_err = "Please enter surname.";
        }
    } else{
        $sql="SELECT CustomerID FROM customers WHERE CustomerName = ?";
        if($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_name);
            // Set parameters
            $param_name = trim($_POST["voornaam"]) . " " . trim($_POST["achternaam"]);
            if (mysqli_stmt_execute($stmt)) {
                $Result = mysqli_stmt_get_result($stmt);
                $rows = mysqli_fetch_all($Result, MYSQLI_ASSOC);

                if (!empty($rows[0])) {
                    $bestaandeklant=true;
                    $CustomerID=$rows[0]["CustomerID"];
                }else{
                    $bestaandeklant=false;
                    $sql="SELECT MAX(CustomerID) FROM customers";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_execute($stmt);
                    $Result = mysqli_stmt_get_result($stmt);
                    $rows1 = mysqli_fetch_all($Result, MYSQLI_ASSOC);
                    print_r($rows1);
                    $CustomerID=$rows1[0]["MAX(CustomerID)"]+1;
                }

                $voornaam = trim($_POST["voornaam"]);
                $achternaam = trim($_POST["achternaam"]);

            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }


    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }



    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err&&empty($achternaam_err))){

        // Prepare an insert statement for users
        $sql = "INSERT INTO users (id, username, password) VALUES (?, ?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iss", $CustomerID, $param_username, $param_password);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
        if(!$bestaandeklant) {
            // Prepare an insert statement for customers
            $sql = "INSERT INTO customers(CustomerID,CustomerName,BillToCustomerID,CustomerCategoryID,PrimaryContactPersonID,DeliveryMethodID,DeliveryCityID,PostalCityID,AccountOpenedDate,StandardDiscountPercentage,IsStatementSent,IsOnCreditHold,PaymentDays,PhoneNumber,FaxNumber,WebsiteURL,DeliveryAddressLine1,DeliveryPostalCode,DeliveryLocation,PostalAddressLine1,PostalPostalCode,LastEditedBy,ValidFrom,ValidTo) 
                values(?,?,?,1,1,2,776,776,CURRENT_TIMESTAMP,0.000,0,0,7,'','','www.windesheim.nl','','','','','',1,CURRENT_TIMESTAMP,'9999-12-31 23:59:59','X')";

            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "isi", $CustomerID, $param_name, $CustomerID);

                // Set parameters
                $param_name = $voornaam . " " . $achternaam;
                $param_ID = $CustomerID;


                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Redirect to login page
                    header("location: login.php");
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
    }

    // Close connection
    mysqli_close($link);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Sign Up</h2>
    <p>Please fill this form to create an account.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
            <label>voornaam</label>
            <input type="voornaam" name="voornaam" class="form-control <?php echo (!empty($voornaam_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
            <span class="invalid-feedback"><?php echo $voornaam_err; ?></span>
        </div>
        <div class="form-group">
            <label>achternaam</label>
            <input type="achternaam" name="achternaam" class="form-control <?php echo (!empty($achternaam_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
            <span class="invalid-feedback"><?php echo $achternaam_err; ?></span>
        </div>


        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="button button1" value="Submit">
            <input type="reset" class="buttonRed button1 ml-2" value="Reset">
        </div>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
</div>
</body>
</html>

<?php

include __DIR__ . "/footer.php";
?>