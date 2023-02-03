<?php

function getCode($n) {
    $characters = '01234567890123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
}
?>

<?php

$kortingsCodes = array("Kerst20" => 0.20, "NewYear23" => 0.23, "10YearsNG" => 0.10);

if(isset($_GET['discountCode'])) {
    $discountCode = $_GET['discountCode'];
    if($kortingsCodes[$discountCode]) {
        $kortingPercentage = $kortingsCodes[$discountCode];
    }
} else {
    $kortingPercentage = 0;
}
?>

<script>
    const input = document.getElementById('text');

    function Alert(){
        alert("De kortingscode is toegepast!");
    }
</script>