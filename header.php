<!-- de inhoud van dit bestand wordt bovenaan elke pagina geplaatst -->
<?php
session_start();
include "database.php";


$databaseConnection = connectToDatabase();

$HeaderColor = "Header";
$LogoColor = "LogoImage";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>NerdyGadgets</title>

    <!-- Javascript -->
    <script src="Public/JS/fontawesome.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="Public/JS/bootstrap.min.js"></script>
    <script src="Public/JS/popper.min.js"></script>
    <script src="Public/JS/resizer.js"></script>

    <!-- Style sheets-->
    <link rel="stylesheet" href="Public/CSS/style.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/typekit.css">



</head>
<body>
<div class="Background">
    <div class="row" id="<?php echo $HeaderColor ?>">
        <div title="home page" class="col-2"><a href="./" id="LogoA">
                <div id="<?php echo $LogoColor ?>"></div>
            </a></div>
        <div class="col-8" id="CategoriesBar">
            <ul id="ul-class">
                <?php
                $HeaderStockGroups = getHeaderStockGroups($databaseConnection);

                foreach ($HeaderStockGroups as $HeaderStockGroup) {
                    ?>
                    <li>
                        <a href="browse.php?category_id=<?php print $HeaderStockGroup['StockGroupID']; ?>"
                           class="HrefDecoration"><?php print $HeaderStockGroup['StockGroupName']; ?></a>
                    </li>
                    <?php
                }
                ?>
                <li>
                    <a href="categories.php" class="HrefDecoration">Alle categorieën</a>
                </li>
            </ul>
        </div>
<!-- code voor US3: zoeken -->
        <ul id="ul-class-navigation">
            <li>
                <a title="zoeken" href="browse.php" class="HrefDecoration"><i class="fas fa-search"></i> </a>
            </li>
            <li>
                <a title="winkelmantje" href="Cart.php" class="HrefDecoration"><i class="fas fa-shopping-cart"></i> </a>
                <?php
                if (isset($_SESSION['cart'])) {
                    $cart = $_SESSION['cart'];
                    $aantal = 0;
                    foreach ($cart as $productID => $amount) {
                        $aantal += $amount;
                    }
                    if($aantal>0) {
                        print("<span class='badge badge-pill badge-danger'>$aantal</span>");
                    }
                }
                ?>
            </li>
            <li>
                <a title="login" href="login.php" class="HrefDecoration"><i  class="fas fa-walking"></i></a>
            </li>
            <li>
                <a title="settings" href="settings.php" class="HrefDecoration"><i class="fas fa-cog"></i></a>
            </li>
        </ul>

<!-- einde code voor US3 zoeken -->
    </div>
    <div class="row" id="Content">
        <div class="col-12">
            <div id="SubContent">


