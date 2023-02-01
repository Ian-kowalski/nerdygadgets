<?php
include __DIR__ . '/header.php';
include "discountGenFunctions.php";
?>
<h3>Je kortingscode voor vandaag</h3>

<?php
if (!$_GET['personalDiscount']) {
    echo("Je hebt nog geen code gegenereerd. Klik op de knop hieronder om dat te doen!");
} else {
    $codeGen1 = getCode($n);
    echo $codeGen1;
    ?>
    <br>
    <?php
    $codeGen2 = substr(sha1(mt_rand()), 17, 10); //To Generate Random Numbers with Letters.
    echo $codeGen2;
}
?>

<form method="get" class="mt-3">
    <input type="submit" class='button button1' value="genereer code" name="personalDiscount">
</form>
