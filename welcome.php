<?php
// Initialize the session
include __DIR__ . '/header.php';

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<h1 class="my-5">Hallo, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welkom terug.</h1>
<p>
    <a href="browse.php" class="btn btn-success">Start shopping</a>
    <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
    <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>

</p>
<?php
include __DIR__ . "/footer.php";
?>