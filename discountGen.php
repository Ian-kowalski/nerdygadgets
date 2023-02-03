<?php
include __DIR__ . '/header.php';
include "discountFunctions.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

?>
<h3>Je kortingscode voor vandaag</h3>

<?php

$n=10;

if(isset($_GET['personalDiscount'])) {
    if ($_GET['personalDiscount']) {
        $codeGen = getCode($n);
        echo $codeGen;
        ?>
        <br>
        <?php
        /*$codeGen2 = substr(sha1(mt_rand()), 17, 10); //To Generate Random Numbers with Letters.
        echo $codeGen2;*/
    }
} else {

        echo("Je hebt nog geen code gegenereerd. Klik op de knop hieronder om dat te doen!");
    }
?>

<form method="get" class="mt-3">
    <input type="submit" class='button button1' value="genereer code" name="personalDiscount">
</form>