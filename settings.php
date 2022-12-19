<?php
include __DIR__ . '/header.php';
include 'darkmode.php';

$checked = '';

if(isset($_POST['colorswitch'])){
    $_SESSION['colorswitch'] = $_POST['colorswitch'];
}
if(isset($_SESSION['colorswitch'])){
    $checked = "checked='checked'";
}

if(!isset($_POST['colorswitch'])){
    unset($_SESSION['colorswitch']);
    $checked = '';
}

?>
<h3>Lightmode </h3>
<!-- Rounded switch -->

<form method="post">
    <label class="switch">
        <input type="checkbox" name="colorswitch" value="colormode" <?php if(isset($_SESSION['colorswitch'])) echo ("checked='checked'") ?>  />
        <span class="slider round"></span>
    </label><br>
    <div class="button button1">
        <input type="submit" name="settingsubmit" value="Opslaan">
    </div>
</form>

