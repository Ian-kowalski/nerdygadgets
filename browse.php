<!-- dit bestand bevat alle code voor het productoverzicht -->
<?php
//hi
include __DIR__ . "/header.php";
// test of er een categorie is geselecteerd klikken op een categorie
$ReturnableResult = null;
$Result=null;
$Sort = "SellPrice";
$AmountOfPages = 0;
$queryBuildResult = "";

if (isset($_GET['category_id'])) {
    $CategoryID = $_GET['category_id'];
    $_SESSION["category_id"] = $_GET['category_id'];
} elseif(isset($_SESSION["category_id"])) {
    $CategoryID=$_SESSION["category_id"];
    $_GET['category_id']=$CategoryID;
} else {
    $CategoryID = "";
    $_SESSION["category_id"]=$CategoryID;
}

if (isset($_GET['products_on_page'])) {
    $ProductsOnPage = $_GET['products_on_page'];
    $_SESSION['products_on_page'] = $_GET['products_on_page'];
} else if (isset($_SESSION['products_on_page'])) {
    $ProductsOnPage = $_SESSION['products_on_page'];
    $_GET['products_on_page']=$ProductsOnPage;
} else {
    $ProductsOnPage = 25;
    $_SESSION['products_on_page'] = 25;
}

if (isset($_GET['page_number'])) {
    $PageNumber = $_GET['page_number'];
    $_SESSION["page_number"] = $_GET['page_number'];
} elseif(isset($_SESSION['page_number'])){
    $PageNumber = $_SESSION['page_number'];
    $_GET['page_number']=$PageNumber;
}else {
    $PageNumber = 0;
    $_SESSION['page_number']=$PageNumber;
}

if (isset($_GET['ColorID'])) {
    $ColorID = $_GET['ColorID'];
    $_SESSION["ColorID"] = $_GET['ColorID'];
}elseif(isset($_SESSION["ColorID"])){
    $ColorID = $_SESSION["ColorID"];
    $_GET['ColorID']=$ColorID;
} else {
    $ColorID =0;
    $_SESSION["ColorID"] = 0;
}

if (isset($_GET['SizeID'])) {
    $SizeID = $_GET['SizeID'];
    $_SESSION["SizeID"] = $_GET['SizeID'];
} elseif(isset($_SESSION["SizeID"])){
    $SizeID = $_SESSION['SizeID'];
    $_GET['SizeID']=$SizeID;
} else {
    $SizeID = "";
    $_SESSION["SizeID"] ="";
}

if (isset($_GET['search_string'])) {
    $SearchString = $_GET['search_string'];
    $_SESSION["search_string"] = $_GET['search_string'];
}elseif(isset($_SESSION['search_string'])){
    $SearchString = $_SESSION['search_string'];
    $_GET["search_string"] = $SearchString;
}else{
    $SearchString = "";
    $_SESSION["search_string"] = "";
}

if (isset($_GET['sort'])) {
    $SortOnPage = $_GET['sort'];
    $_SESSION["sort"] = $_GET['sort'];
} else if (isset($_SESSION["sort"])) {
    $SortOnPage = $_SESSION["sort"];
    $_GET["sort"]=$_SESSION["sort"];
} else {
    $SortOnPage = "price_low_high";
    $_SESSION["sort"] = "price_low_high";
}

if (isset($_GET['pricefilter'])) {
    $PriceFilter = $_GET['pricefilter'];
    $_SESSION['pricefilter'] = $_GET['pricefilter'];
} else if (isset($_SESSION['pricefilter'])) {
    $PriceFilter = $_SESSION['pricefilter'];
    $_GET["pricefilter"]=$_SESSION["pricefilter"];
} else {
    $PriceFilter = "";
    $_SESSION['pricefilter'] = "";
}

switch ($SortOnPage) {
    case "price_high_low":
    {
        $Sort = "SellPrice DESC";
        break;
    }
    case "name_low_high":
    {
        $Sort = "StockItemName";
        break;
    }
    case "name_high_low";
        $Sort = "StockItemName DESC";
        break;
    case "price_low_high":
    {
        $Sort = "SellPrice";
        break;
    }
    default:
    {
        $Sort = "SellPrice";
        $SortName = "price_low_high";
    }
}

if ($SearchString != "") {
    $SearchString = str_replace("'", "", $SearchString); //Voorkoming SQL-injectie: Haalt single aanhalingstekens weg
    $searchValues = explode(" ", $SearchString);
    $queryBuildResult .= "(";
    for ($i = 0; $i < count($searchValues); $i++) {
        if ($i != 0) {
            $queryBuildResult .= "AND ";
        }
        $queryBuildResult .= "SI.SearchDetails LIKE '%$searchValues[$i]%' ";
    }
    if(is_int($SearchString)){
        $queryBuildResult .= "OR SI.StockItemID ='$SearchString'";
    }
    $queryBuildResult .= ")";
}
// add $CategoryID

if($CategoryID!="") {
    if ($queryBuildResult != "") {
        $queryBuildResult .= " AND";
    }
    $queryBuildResult .= " SIG.StockGroupID =$CategoryID";
}
if($CategoryID==2||$CategoryID==4||$CategoryID=="") {
//add $ColorID
    if ($ColorID != 0) {
        if ($queryBuildResult != "") {
            $queryBuildResult .= " AND";
        }
        $queryBuildResult .= " SI.ColorID = $ColorID";
    }
//add $SizeID
    if ($SizeID != "") {
        if ($queryBuildResult != "") {
            $queryBuildResult .= " AND";
        }
        $queryBuildResult .= " SI.Size = '$SizeID'";
    }
}
$Offset = $PageNumber * $ProductsOnPage;

$ReturnableResult=filteren($queryBuildResult, $Sort, $ProductsOnPage, $Offset,$databaseConnection);
$Result=row($queryBuildResult, $Sort, $databaseConnection);


$amount = $Result[0];
if (isset($amount)) {
    $AmountOfPages = ceil($amount["count(*)"] / $ProductsOnPage);
}


function getVoorraadTekst($actueleVoorraad) {
    if ($actueleVoorraad > 1000) {
        return "Ruime voorraad beschikbaar.";
    } else {
        return "Voorraad: $actueleVoorraad";
    }
}
function berekenVerkoopPrijs($adviesPrijs, $btw) {
    return $btw * $adviesPrijs / 100 + $adviesPrijs;
}
?>

<div id="FilterFrame"><h2 class="FilterText"><i class="fas fa-filter"></i> Filteren </h2>
    <form>
        <div id="FilterOptions">
            <h4 class="FilterTopMargin"><i class="fas fa-search"></i> Zoeken</h4>
            <input type="text" name="search_string" id="search_string"
                   value="<?php print (isset($_GET['search_string'])) ? $_GET['search_string'] : ""; ?>"
                   class="form-submit">
            <input type="hidden" name="category_id" id="category_id"
                   value="<?php print (isset($_GET['category_id'])) ? $_GET['category_id'] : ""; ?>">
            <input type="hidden" name="sort" id="sort" value="<?php print ($_SESSION['sort']); ?>">
            <input type="hidden" name="ColorID" id="ColorID" value="<?php print ($_SESSION['ColorID']); ?>">
            <input type="hidden" name="SizeID" id="SizeID" value="<?php print ($_SESSION['SizeID']); ?>">
            <input type="hidden" name="category_id" id="category_id" value="<?php if (isset($_GET['category_id'])) {print ($_GET['category_id']);} ?>">
            <input type="hidden" name="result_page_numbers" id="result_page_numbers" value="<?php print (isset($_GET['result_page_numbers'])) ? $_GET['result_page_numbers'] : "0"; ?>">
            <input type="hidden" name="products_on_page" id="products_on_page" value="<?php print ($_SESSION['products_on_page']); ?>">
            <h4 class="FilterTopMargin"><i class="fas fa-list-ol"></i> Aantal producten op pagina</h4>
            <select name="products_on_page" id="products_on_page" onchange="this.form.submit()">>
                <option value="25" <?php if ($_SESSION['products_on_page'] == 25) {
                    print "selected";
                } ?>>25
                </option>
                <option value="50" <?php if ($_SESSION['products_on_page'] == 50) {
                    print "selected";
                } ?>>50
                </option>
                <option value="75" <?php if ($_SESSION['products_on_page'] == 75) {
                    print "selected";
                } ?>>75
                </option>
                <option value="100" <?php if ($_SESSION['products_on_page'] == 100) {
                    print "selected";
                } ?>>100
                </option>
            </select>
            <h4 class="FilterTopMargin"><i class="fas fa-sort"></i> Sorteren</h4>
            <select name="sort" id="sort" onchange="this.form.submit()">>
                <option value="price_low_high" <?php if ($_SESSION['sort'] == "price_low_high") {
                    print "selected";
                } ?>>Prijs oplopend
                </option>
                <option value="price_high_low" <?php if ($_SESSION['sort'] == "price_high_low") {
                    print "selected";
                } ?> >Prijs aflopend
                </option>
                <option value="name_low_high" <?php if ($_SESSION['sort'] == "name_low_high") {
                    print "selected";
                } ?>>Naam oplopend
                </option>
                <option value="name_high_low" <?php if ($_SESSION['sort'] == "name_high_low") {
                    print "selected";
                } ?>>Naam aflopend
                </option>
            </select>


            <?php if($CategoryID==2 || $CategoryID==4){?>
            <h4 class="FilterTopMargin"><i class="fas fa-palette"></i> Kleur</h4>
            <select name="ColorID" id="ColorID" onchange="this.form.submit()">>
                <option value=0 <?php if ($_SESSION['ColorID'] == 0 ) {
                    print "selected";
                } ?>> Kies een kleur
                </option>
                <option value=3 <?php if ($_SESSION['ColorID'] == 3) {
                    print "selected";
                } ?> >Zwart
                </option>
                <?php if($CategoryID!=4){ ?>
                <option value=4 <?php if ($_SESSION['ColorID'] == 4) {
                    print "selected";
                } ?>>Blauw
                </option>
                <option value=12 <?php if ($_SESSION['ColorID'] == 12) {
                    print "selected";
                } ?>>Staalgrijs
                </option>
                <option value=18 <?php if ($_SESSION['ColorID'] == "18") {
                print "selected";
                } ?>>Bruin
                </option>
                <?php } ?>
                <option value=35 <?php if ($_SESSION['ColorID'] == 35) {
                    print "selected";
                } ?>>Wit
                </option>
            </select>
            <h4 class="FilterTopMargin"><i class="fas fa-ruler-combined"></i> Maat</h4>
            <select name="SizeID" id="SizeID" onchange="this.form.submit()">>
                <option value="" <?php if ($_SESSION['SizeID'] == "") {
                    print "selected";
                } ?>> Kies een maat
                </option>
                <option value="3XS" <?php if ($_SESSION['SizeID'] == "3XS") {
                    print "selected";
                } ?> >3XS
                </option>
                <option value="XXS" <?php if ($_SESSION['SizeID'] == "XXS") {
                    print "selected";
                } ?>>XXS
                </option>
                <option value="XS" <?php if ($_SESSION['SizeID'] == "XS") {
                    print "selected";
                } ?>>XS
                </option>
                <option value="S" <?php if ($_SESSION['SizeID'] == "S") {
                    print "selected";
                } ?>>S
                </option>
                <option value="M" <?php if ($_SESSION['SizeID'] == "M") {
                    print "selected";
                } ?>>M
                </option>
                <option value="L" <?php if ($_SESSION['SizeID'] == "L") {
                    print "selected";
                } ?>>L
                </option>
                <option value="XL" <?php if ($_SESSION['SizeID'] == "XL") {
                    print "selected";
                } ?>>XL
                </option>
                <option value="XXL" <?php if ($_SESSION['SizeID'] == "XXL") {
                    print "selected";
                } ?>>XXL
                </option>
                <option value="3XL" <?php if ($_SESSION['SizeID'] == "3XL") {
                    print "selected";
                } ?>>3XL
                </option>
                <option value="4XL" <?php if ($_SESSION['SizeID'] == "4XL") {
                    print "selected";
                } ?>>4XL
                </option>
                <option value="5XL" <?php if ($_SESSION['SizeID'] == "5XL") {
                    print "selected";
                } ?>>5XL
                </option>
                <option value="6XL" <?php if ($_SESSION['SizeID'] == "6XL") {
                    print "selected";
                } ?>>6XL
                </option>
                <option value="7XL" <?php if ($_SESSION['SizeID'] == "7XL") {
                    print "selected";
                } ?>>7XL

                <?php }?>
            </select>
        </div>
    </form>
</div>

<div id="ResultsArea" class="Browse">
    <?php
    if (isset($ReturnableResult) && count($ReturnableResult) > 0) {
        foreach ($ReturnableResult as $row) {
            ?>
            <a class="ListItem" href='view.php?id=<?php print $row['StockItemID']; ?>'>
                <div id="ProductFrame">
                    <?php
                    if (isset($row['ImagePath'])) { ?>
                        <div class="ImgFrame"
                             style="background-image: url('<?php print "Public/StockItemIMG/" . $row['ImagePath']; ?>'); background-size: 230px; background-repeat: no-repeat; background-position: center;"></div>
                    <?php } else if (isset($row['BackupImagePath'])) { ?>
                        <div class="ImgFrame"
                             style="background-image: url('<?php print "Public/StockGroupIMG/" . $row['BackupImagePath'] ?>'); background-size: cover;"></div>
                    <?php }
                    ?>

                    <div id="StockItemFrameRight">
                        <div class="CenterPriceLeftChild">
                            <h1 class="StockItemPriceText"><?php print "€".number_format(berekenVerkoopPrijs($row["RecommendedRetailPrice"], $row["TaxRate"]), 2,",", "."); ?></h1>
                            <h6>Inclusief BTW </h6>
                        </div>
                    </div>
                    <h1 class="StockItemID">Artikelnummer: <?php print $row["StockItemID"]; ?></h1>
                    <p class="StockItemName"><?php print $row["StockItemName"]; ?></p>
                    <p class="StockItemComments"><?php print $row["MarketingComments"]; ?></p>
                    <h4 class="ItemQuantity"><?php print getVoorraadTekst($row["QuantityOnHand"]); ?></h4>
                </div>
            </a>
        <?php } ?>

        <form id="PageSelector">
            <?php
            if ($AmountOfPages > 0) {
                for ($i = 1; $i <= $AmountOfPages; $i++) {
                    if ($PageNumber == ($i - 1)) {
                        ?>
                        <div id="SelectedPage"><?php print $i; ?></div><?php
                    } else { ?>
                        <button id="page_number" class="PageNumber" value="<?php print($i-1); ?>" type="submit"
                                name="page_number"><?php print($i); ?></button>
                    <?php }
                }
            }
            ?>
        </form>
        <?php
    } else {
        ?>
        <h2 id="NoSearchResults">
            Sorry, geen resultaten gevonden
        </h2>
        <?php
    }
    ?>

</div>


<?php
include __DIR__ . "/footer.php";
?>

