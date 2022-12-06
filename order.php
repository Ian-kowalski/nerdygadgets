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
            <form method="get" action="Verwerkorder.php" class="NAWRow" id="Nawgegevens">
                <label for="country"><h3>Land/regio</h3></label>
                <select name="country" id="country">
                    <option value="nederland">Nederland</option>
                    <option value="belgië">België</option>
                </select><br>
                <h3>Adres</h3>
                <label for="postalcode">Postcode</label>
                <input type="text" name="postalcode" id="postalcode" required><br>
                <label for="huisnr">Huisnr & toevoeging</label>
                <input type="text" name="huisnr" id="huisnr" required><br>
                <label for="city">Plaats</label>
                <input type="text" name="city" id="city" required><br>
                <label for="street">Straatnaam</label>
                <input type="text" name="street" id="street" required><br>
                <label for="gender"><h3>Aanhef</h3></label>
                <input type="radio" name="gender" id="mevrouw" value="Mevrouw"><label for="gender">Mevrouw</label>
                <input type="radio" name="gender" id="meneer" value="Meneer">Meneer
                <input type="radio" name="gender" id="geenvanbeide" value="Geen van beide">Geen van beide<br>
                <label for="NAWgegevens"> <br> <br> <h3>Persoonsgegevens</h3></label>
                <label for="fname">Voornaam</label>
                <input type="text" name="fname" id="fname" required><br>
                <label for="prefixes">Tussenvoegsels (optioneel)</label>
                <input type="text" name="prefixes" id="prefixes"><br>
                <label for="lname">Achternaam</label>
                <input type="text" name="lname" id="lname" required><br>
                <!--<label for="Email">Email-adres</label>
                <input type="text" name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"><br>-->
                <label for="Telefoon">Telefoonnummer</label>
                <input type="tel" id="phone" name="phone" pattern="[0]{1}[0-9]{1}[0-9]{8}" required>

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