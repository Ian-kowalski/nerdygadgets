<?php
include __DIR__ . '/header.php';
include 'darkmode.php';


?>
<h3>Lightmode </h3>
<!-- Rounded switch -->
<?php

//does not realy work
/* if(isset($_SESSION["offOn"])){
    $OppisteCheckedVal=$_SESSION["offOn"]["OppisteCheckedVal"];
    $Checked=$_SESSION["offOn"]["checked"];
}elseif (isset($_POST['offOn'])){
    echo "checked!";
    $_SESSION["offOn"]=array("checked"=> "checked","OppisteCheckedVal" => "OFF");

}
else {
    echo "not checked!";
    $_SESSION["offOn"]=array("checked" => "","OppisteCheckedVal" => "ON");

}
print_r($_SESSION);
$OppisteCheckedVal=$_SESSION["offOn"]["OppisteCheckedVal"];
$Checked=$_SESSION["offOn"]["checked"]; */
?>

<!--<form method="post">
<label class="switch">
    <input type="checkbox" name="offOn" onchange="this.form.submit()" <?php /*echo $Checked ?> value="<?php echo $OppisteCheckedVal;*/?>">
    <span class="slider round"></span>
</label>
</form> -->

<form method="post">
    <label class="switch">
        <input type="checkbox">
        <span class="slider round"></span>
    </label>
</form>











