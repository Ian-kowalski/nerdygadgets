<?php
// Include config file
include __DIR__ . '/header.php';
require_once "config.php";


// Define variables and initialize with empty values
$username = $password = $confirm_password = $voornaam = $achternaam = $postcode= $woonplaats = $adres = $tel = "";
$username_err = $password_err = $confirm_password_err = $voornaam_err = $achternaam_err = $postcode_err = $woonplaats_err = $adres_err = $tel_err = " " ;
$name = "";
if(isset($_GET["voornaam"]))$name= $_GET["voornaam"] . " " . $_GET["$achternaam"];

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

        $statement = mysqli_prepare($link, "
                        SELECT MAX(CustomerID) + 1 AS CstId -- Fetch highest known ID and increase by 1, save as CstId
                        FROM customers;");
        mysqli_stmt_execute($statement);
        $Result = mysqli_stmt_get_result($statement);
        $customerID = mysqli_fetch_all($Result, MYSQLI_ASSOC); //Fetch result from SQL query
        $customerID = $customerID[0]["CstId"]; //Retrieve customerID from fetched array

        $addToCustumer = mysqli_prepare($link, "
            INSERT INTO customers(CustomerID,CustomerName,BillToCustomerID,CustomerCategoryID,PrimaryContactPersonID,DeliveryMethodID,DeliveryCityID,PostalCityID,AccountOpenedDate,StandardDiscountPercentage,IsStatementSent,IsOnCreditHold,PaymentDays,PhoneNumber,FaxNumber,WebsiteURL,DeliveryAddressLine1,DeliveryPostalCode,DeliveryLocation,PostalAddressLine1,PostalPostalCode,LastEditedBy,ValidFrom,ValidTo) 
            values(?,?,?,1,1,2,776,776,CURRENT_TIMESTAMP,0.000,0,0,7,?,?,'www.windesheim.nl',?,?,?,?,?,1,CURRENT_TIMESTAMP,'9999-12-31 23:59:59' )"
        );
        mysqli_stmt_bind_param($addToCustumer, 'isisssssss', $customerID, $name, $customerID, $tel, $tel,$adres, $Postcode, $plaats, $adres, $Postcode);
        mysqli_stmt_execute($addToCustumer);

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO users (id,username, password) VALUES (?,?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iss",$customerID, $param_username, $param_password);

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
    }

    // Close connection
    mysqli_close($link);
}
?>
<div class="wrapper">

    <h2>Sign Up</h2>

    <p>Already have an account? <a href="login.php">Login here</a>.</p>
    <p>Please fill out this form to create an account.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
            <label>voornaam</label>
            <input type="text" name="voornaam" required pattern="[a-zA-z]{1, }" class="form-control <?php echo (!empty($voornaam_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $voornaam; ?>">
            <span class="invalid-feedback"><?php echo $voornaam_err; ?></span>
        </div>

        <div class="form-group">
            <label>Achternaam</label>
            <input type="text" name="achternaam" required pattern="[a-zA-z]{1, }" class="form-control <?php echo (!empty($achternaam_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $achternaam; ?>">
            <span class="invalid-feedback"><?php echo $achternaam_err; ?></span>
        </div>

        <div class="form-group">
            <label>Straat</label>
            <input type="adres" name="adres" class="form-control <?php echo (!empty($adres_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $adres; ?>">
            <span class="invalid-feedback"><?php echo $adres_err; ?></span>

            <div class="form-group">
                <label>huisnr</label>
                <input type="adres" name="adres" class="form-control <?php echo (!empty($adres_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $adres; ?>">
                <span class="invalid-feedback"><?php echo $adres_err; ?></span>

            <div class="form-group">
                <label>Postcode</label>
                <input type="postcode" name="postcode" required pattern="[0-9]{4,4}+[A-Z]{2,2}" class="form-control <?php echo (!empty($postcode_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $postcode; ?>">
                <span class="invalid-feedback"><?php echo $postcode_err; ?></span>

                <div class="form-group">
                    <label>Woonplaats</label>
                    <input type="Woonplaats" name="Woonplaats" required pattern="[a-z A-z]{1, }" class="form-control <?php echo (!empty($woonplaats_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $woonplaats; ?>">
                    <span class="invalid-feedback"><?php echo $woonplaats_err; ?></span>

                </div>
                <div class="form-group">
                    <label>Telefoonnummer</label>
                <input type="text" name="Telefoonnummer" class="form-control" required pattern="[0]{1}[0-9]{1}[0-9]{8}" class="form-control <?php echo (!empty($tel_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $tel; ?>">
                    <span class="invalid-feedback"><?php echo $tel_err; ?></span>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required pattern="[a-z A-z0-9]{6,}" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required pattern="[a-z A-z0-9]{6,}" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                </div>

    </form>
</div>
</body>
</html>


<?php
include __DIR__ . "/footer.php";
?>