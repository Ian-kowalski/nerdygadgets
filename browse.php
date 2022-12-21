<?php
include __DIR__ . "/header.php";

$ReturnableResult = null;
$Sort = "SellPrice";
$SortName = "price_low_high";
$AmountOfPages = 0;
$queryBuildResult = "";
$SearchString = "";

if (isset($_GET['category_id'])) {
    $CategoryID = $_GET['category_id'];
    $_SESSION["category_id"] = $_GET['category_id'];
} else {
    $CategoryID = "";
    $_SESSION["category_id"]=$CategoryID;
}
if (isset($_GET['products_on_page'])) {
    $ProductsOnPage = $_GET['products_on_page'];
    $_SESSION['products_on_page'] = $_GET['products_on_page'];
} else if (isset($_SESSION['products_on_page'])) {
    $ProductsOnPage = $_SESSION['products_on_page'];
} else {
    $ProductsOnPage = 25;
    $_SESSION['products_on_page'] = 25;
}
if (isset($_GET['page_number'])) {
    $PageNumber = $_GET['page_number'];
    $_SESSION["page_number"] = $_GET['page_number'];
} else {
    $PageNumber = 0;
}
if (isset($_GET['search_string'])) {
    $SearchString = $_GET['search_string'];
    $_SESSION["search_string"] = $_GET['search_string'];
}
if (isset($_GET['sort'])) {
    $SortOnPage = $_GET['sort'];
    $_SESSION["sort"] = $_GET['sort'];
} else if (isset($_SESSION["sort"])) {
    $SortOnPage = $_SESSION["sort"];
} else {
    $SortOnPage = "price_low_high";
    $_SESSION["sort"] = "price_low_high";
}
if (isset($_GET['ColorID'])) {
    $_SESSION["ColorID"] = $_GET['ColorID'];
} else {
    $_SESSION["ColorID"] = "0";
}
if (isset($_GET['SizeID'])) {
    $_SESSION["SizeID"] = $_GET['SizeID'];
} else {
    $_SESSION["SizeID"] = "0";
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

function pricefilter($queryBuildResult, $min, $max)
{
    if ($queryBuildResult != "") {
        $queryBuildResult .= " AND ";
    }
    $queryBuildResult .= "SI.SellPrice BETWEEN $min AND $max";
    return $queryBuildResult;
}

if (isset($_GET['pricefilter'])) {
    $PriceFilter = $_GET['pricefilter'];
    $_SESSION['pricefilter'] = $_GET['pricefilter'];
} else if (isset($_SESSION['pricefilter'])) {
    $PriceFilter = $_SESSION['pricefilter'];
} else {
    $PriceFilter = "";
    $_SESSION['pricefilter'] = "";
}


$Offset = $PageNumber * $ProductsOnPage;



if ($CategoryID == "") {
    if ($queryBuildResult != "") {
        $Query_sort = "
                SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, TaxRate, RecommendedRetailPrice, ROUND(TaxRate * RecommendedRetailPrice / 100 + RecommendedRetailPrice,2) as SellPrice,
                QuantityOnHand,
                (SELECT ImagePath
                FROM stockitemimages
                WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath,
                (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath
                FROM stockitems SI
                JOIN stockitemholdings SIH USING(stockitemid)
                WHERE ".$queryBuildResult."
                GROUP BY StockItemID
                ORDER BY ".$Sort."
                LIMIT ?  OFFSET ?";
        $Query_count = "
                select count(*)
                FROM stockitems SI
                WHERE $queryBuildResult";
    }else{
        $Query_sort = "
                SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, TaxRate, RecommendedRetailPrice, ROUND(TaxRate * RecommendedRetailPrice / 100 + RecommendedRetailPrice,2) as SellPrice,
                QuantityOnHand,
                (SELECT ImagePath
                FROM stockitemimages
                WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath,
                (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath
                FROM stockitems SI
                JOIN stockitemholdings SIH USING(stockitemid)
                GROUP BY StockItemID
                ORDER BY ".$Sort."
                LIMIT ?  OFFSET ?";
        $Query_count = "
                select count(*)
                FROM stockitems SI";
    }
    $Statement = mysqli_prepare($databaseConnection, $Query_sort);
    mysqli_stmt_bind_param($Statement, "ii",$ProductsOnPage, $Offset);

    $rows = mysqli_prepare($databaseConnection, $Query_count);
}

if ($CategoryID != "") {
    if ($queryBuildResult != "") {
        $Query_sort = "
               SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, TaxRate, RecommendedRetailPrice,
               ROUND(SI.TaxRate * SI.RecommendedRetailPrice / 100 + SI.RecommendedRetailPrice,2) as SellPrice,
               QuantityOnHand,
               (SELECT ImagePath FROM stockitemimages WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath,
               (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath
               FROM stockitems SI
               JOIN stockitemholdings SIH USING(stockitemid)
               JOIN stockitemstockgroups SIG USING(StockItemID)
               JOIN stockgroups SG USING(StockGroupID)
               WHERE ".$queryBuildResult." AND SIG.StockGroupID = ?
               GROUP BY StockItemID
               ORDER BY " . $Sort . "
               LIMIT ? OFFSET ?";
        $Query_count = "
                select count(*)
                FROM stockitems SI
                JOIN stockitemstockgroups SIG USING(StockItemID)
                WHERE ".$queryBuildResult." AND SIG.StockGroupID = ?";
    } else {
        $Query_sort = "
                SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, TaxRate, RecommendedRetailPrice,
                ROUND(SI.TaxRate * SI.RecommendedRetailPrice / 100 + SI.RecommendedRetailPrice,2) as SellPrice,
                QuantityOnHand,
                (SELECT ImagePath FROM stockitemimages WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath,
                (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath
                FROM stockitems SI
                JOIN stockitemholdings SIH USING(stockitemid)
                JOIN stockitemstockgroups SIG USING(StockItemID)
                JOIN stockgroups SG USING(StockGroupID)
                WHERE SIG.StockGroupID = ?
                GROUP BY StockItemID
                ORDER BY " . $Sort . "
                LIMIT ? OFFSET ?";
        $Query_count = "
                select count(*)
                FROM stockitems SI
                JOIN stockitemstockgroups SIG USING(StockItemID)
                WHERE SIG.StockGroupID = ?";
    }
    $Statement = mysqli_prepare($databaseConnection, $Query_sort);
    mysqli_stmt_bind_param($Statement, "iii", $CategoryID, $ProductsOnPage, $Offset);

    $rows = mysqli_prepare($databaseConnection, $Query_count);
    mysqli_stmt_bind_param($rows, "i", $CategoryID);
}


mysqli_stmt_execute($rows);
$Result = mysqli_stmt_get_result($rows);
$Result = mysqli_fetch_all($Result, MYSQLI_ASSOC);

mysqli_stmt_execute($Statement);
$ReturnableResult = mysqli_stmt_get_result($Statement);
$ReturnableResult = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC);

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
            <h4 class="FilterTopMargin"><i class="fas fa-list-ol"></i> Aantal producten op pagina</h4>
            <input type="hidden" name="category_id" id="category_id"
                   value="<?php print (isset($_GET['category_id'])) ? $_GET['category_id'] : ""; ?>">
            <input type="hidden" name="sort" id="sort" value="<?php print ($_SESSION['sort']); ?>">
            <input type="hidden" name="ColorID" id="ColorID" value="<?php print ($_SESSION['ColorID']); ?>">
            <input type="hidden" name="SizeID" id="SizeID" value="<?php print ($_SESSION['SizeID']); ?>">
            <input type="hidden" name="category_id" id="category_id" value="<?php if (isset($_GET['category_id'])) {print ($_GET['category_id']);} ?>">
            <input type="hidden" name="result_page_numbers" id="result_page_numbers" value="<?php print (isset($_GET['result_page_numbers'])) ? $_GET['result_page_numbers'] : "0"; ?>">
            <input type="hidden" name="products_on_page" id="products_on_page" value="<?php print ($_SESSION['products_on_page']); ?>">
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
            <h4 class="FilterTopMargin"><i class="fas fa-tags"> </i> prijs</h4>
            <input type="range" name="price" id="price" min="0" max="1000" step="10"
                   value="<?php print (isset($_GET['price'])) ? $_GET['price'] : 1000; ?>"
                   oninput="this.form.submit()">
            <h4 class="FilterTopMargin"><i class="fas fa-palette"></i> Kleur</h4>
            <select name="ColorID" id="ColorID" onchange="this.form.submit()">>
                <option value="0" <?php if ($_SESSION['ColorID'] == "0") {
                    print "selected";
                } ?>> kies kleur
                </option>
                <option value="1" <?php if ($_SESSION['ColorID'] == "1") {
                    print "selected";
                } ?> >zwart
                </option>
                <option value="2" <?php if ($_SESSION['ColorID'] == "2") {
                    print "selected";
                } ?>>wit
                </option>
                <option value="3" <?php if ($_SESSION['ColorID'] == "3") {
                    print "selected";
                } ?>>blauwfix
                </option>
            </select>
            <h4 class="FilterTopMargin"><i class="fas fa-ruler-combined"></i> Maat</h4>
            <select name="SizeID" id="SizeID" onchange="this.form.submit()">>
                <option value="0" <?php if ($_SESSION['SizeID'] == "0") {
                    print "selected";
                } ?>> kies maat
                </option>
                <option value="1" <?php if ($_SESSION['SizeID'] == "1") {
                    print "selected";
                } ?> >s
                </option>
                <option value="2" <?php if ($_SESSION['SizeID'] == "2") {
                    print "selected";
                } ?>>m
                </option>
                <option value="3" <?php if ($_SESSION['SizeID'] == "3") {
                    print "selected";
                } ?>>l
                </option>
                <option value="4" <?php if ($_SESSION['SizeID'] == "4") {
                    print "selected";
                } ?>>xl
                </option>
                <option value="5" <?php if ($_SESSION['SizeID'] == "5") {
                    print "selected";
                } ?>>xxl
                </option>
                <option value="6" <?php if ($_SESSION['SizeID'] == "6") {
                    print "selected";
                } ?>>xl
                </option>
                <option value="7" <?php if ($_SESSION['SizeID'] == "7") {
                    print "selected";
                } ?>>xxxl
                </option>
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
            Yarr, er zijn geen resultaten gevonden.
        </h2>
        <?php
    }
    ?>

</div>

<?php
include __DIR__ . "/footer.php";
?>
