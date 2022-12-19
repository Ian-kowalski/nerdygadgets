<?php
include __DIR__ . "/header.php";
include 'darkmode.php';
?>

<h3>Lightmode </h3>
<!-- Rounded switch -->


<form method="post">
    <label class="switch">
        <input type="checkbox" name="colorswitch" value="colormode" <?php if(isset($_POST['colorswitch'])) echo "checked='checked'"; ?>  />
        <span class="slider round"></span>
    </label><br>
    <div class="button button1">
        <input type="submit" name="settingsubmit" value="Opslaan">
    </div>
</form>

<?php

if(filter_has_var(INPUT_POST, 'colorswitch')) {
    echo("De slider werkt.");
}

?>