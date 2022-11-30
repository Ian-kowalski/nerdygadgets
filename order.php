<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";
//123
?>
    <h1 style="min-width: 500px;">BESTELLEN</h1>
<?php
$cart = getCart();

?>

    <!-- Body order page -->

<?php
if($cart!=null){
    ?>
    <div class="cart_price">
        <div class="NAW">
            <form class="NAWRow">
                <label for="country"><h3>Land/regio</h3></label>
                <select name="country" id="country">
                    <option value="nederland">Nederland</option>
                    <option value="belgië">België</option>
                </select><br>
                <h3>Adres</h3>
                <label for="postalcode">Postcode</label>
                <input type="text" name="postalcode" id="postalcode"><br>
                <label for="huisnmr">Huisnmr & toevoeging</label>
                <input type="text" name="huisnmr" id="huisnmr"><br>
                <label for="city">Plaats</label>
                <input type="text" name="city" id="city"><br>
                <label for="street">Straatnaam</label>
                <input type="text" name="street" id="street"><br>
                <label for="city">Plaats</label>
                <input type="text" name="city" id="city"><br>
            </form>
        </div>
        <div class="totalPrice">
            <?php
            $totaalPrice=0;
            foreach($cart as $productID => $amount){
                $StockItem = getStockItem($productID, $databaseConnection);
                $price=$StockItem["SellPrice"]*$amount;
                $totaalPrice+=$price;
            }
            if($totaalPrice >= "50") {
                $verzendkosten = 0;
            }
            print ("Subtotaal: €".number_format($totaalPrice, 2, ",", ".")."<br>");
            print("Verzendkosten: €".number_format($verzendkosten,2,",", "."). "<br>");
            print("Totaal: €".number_format($totaalPrice + $verzendkosten,2,",", "."). "<br>"); ?>
            <a href="/nerdygadgets/order.php" class='button button1'>bestellen</a>
        </div>
    </div>

    <div class="row">
        <h1 style="min-width: 500px;">Je order page doet het</h1>
    </div>
    <div class="HrefDecoration">
        <a href="/nerdygadgets/Cart.php" class='HrefDecoration'>>terug naar winkelmandje</a>
    </div>

<?php
} else {
    ?>
    <div class="row">
        <h1 style="min-width: 500px;">Je hebt nog niks om te bestellen!</h1>
    </div>
    <div class="HrefDecoration">
        <a href="/nerdygadgets/categories.php" class='HrefDecoration'>>terug naar categorieën</a>
    </div>

<?php
}
    include __DIR__ . "/footer.php";
?>