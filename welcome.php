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

<div class="welkom">
    <form action="browse.php">
        <input type="submit" class='buttonRev button1' value="Start shopping">
    </form>
    <form action="reset-password.php">
        <input type="submit" class='button button1' value="Reset Your Password">
    </form>
    <form action="updateadress.php">
        <input type="submit" class='button button1' value="update adress">
    </form>
    <form action="logout.php">
        <input type="submit" class='buttonRed button1' value="Sign Out of Your Account">
    </form>
</div>

<?php
include __DIR__ . "/footer.php";
?>