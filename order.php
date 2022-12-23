<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";
?>
    <h1 style="min-width: 500px;">BESTELLEN</h1>
<?php
$cart = getCart();

?>

    <!-- Body order page -->

<?php
if($cart!=null){
    ?>
    <div class="order_price">
        <div class="NAW">
            <form method="get" action="Verwerkorder.php"  id="Nawgegevens">
                <div class="NAWRow">
                    <label for="country"><h3>Land/regio</h3></label>
                    <select name="country" id="country">
                        <option value="nederland">Nederland</option>
                        <option value="belgië">België</option>
                    </select><br>
                </div>
                <div class="NAWRow">
                    <h3>Adres</h3>
                    <label for="street">Straatnaam</label>
                    <input type="text" name="straat" id="straat" required pattern="[A-Z a-z]{1,}"><br>
                    <div class="NAWcol">
                        <label for="huisnr">Huisnr & toevoeging</label>
                        <input type="text" name="huisnr" id="huisnr" required pattern="[0-9]{1,}[a-zA-Z]{0,1}"><br>
                        <label for="postcode">Postcode</label>
                        <input type="text" name="postcode" id="postcode" required pattern="[0-9]{4,4}+[A-Z]{2,2}"><br>
                    </div>
                    <label for="city">Plaats</label>
                    <input type="text" name="city" id="city" required pattern="[a-z A-Z]{1,}"><br>

                </div>
                <div class="NAWRow">
                    <label for="gender"><h3>Aanhef</h3></label>
                    <div class="NAWcol">
                        <input class="radio" type="radio" name="gender" id="mevrouw" value="Mevrouw" required>Mevrouw
                    </div>
                    <div class="NAWcol">
                        <input class="radio" type="radio" name="gender" id="meneer" value="Meneer">Meneer
                    </div>
                    <div class="NAWcol">
                        <input class="radio" type="radio" name="gender" id="geenvanbeide" value="Geen van beide">Geen van beide
                    </div>
                </div>
                <div class="NAWRow">
                    <br>
                   <label for="NAWgegevens"><h3>Persoonsgegevens</h3></label>
                    <label for="fname">Voornaam</label>
                    <input type="text" name="fname" id="fname" required pattern="[A-Za-z]{1,}"><br>
                    <label for="prefixes">Tussenvoegsels (optioneel)</label>
                    <input type="text" name="prefixes" id="prefixes" pattern="[A-Z a-z]{0,}"><br>
                    <label for="lname">Achternaam</label>
                    <input type="text" name="lname" id="lname" required pattern="[a-z A-Z]{1,}"><br>
                    <label for="Telefoon">Telefoonnummer</label>
                    <input type="tel" id="phone" name="phone" pattern="[0]{1}[0-9]{1}[0-9]{8}" required>
                </div>
            </form>
        </div>
        <div class="totalPrice">
            <?php
            $totaalPrice=0;
            $verzendkosten=6.50;
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
        <button type="submit" form="Nawgegevens" value="Submit" class='button button1'>bestellen</button>
            <!--<a href="https://www.ideal.nl/demo/qr/?app=ideal" class='button button1'>bestellen</a>-->
        </div>
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