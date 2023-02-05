<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";
include "discountFunctions.php";
?>
    <h1 style="min-width: 500px;">BESTELLEN</h1>
<?php
$cart = getCart();
if(isset($_SESSION["loggedin"])){
    $statement = mysqli_prepare($databaseConnection, "
                    SELECT CustomerName,PhoneNumber, DeliveryAddressLine1,DeliveryPostalCode,DeliveryLocation,Gender
                    FROM  customers
                    join users on id=CustomerID 
                    where username = ?");
    mysqli_stmt_bind_param($statement ,"s", $_SESSION["username"]);
    mysqli_stmt_execute($statement);
    $Result = mysqli_stmt_get_result($statement);
    $row = mysqli_fetch_all($Result, MYSQLI_ASSOC);
    $name=$row[0]["CustomerName"];
    $tel=$row[0]["PhoneNumber"];
    $address=$row[0]["DeliveryAddressLine1"];
    $postCode=$row[0]["DeliveryPostalCode"];
    $location=$row[0]["DeliveryLocation"];
    $Gender=$row[0]["Gender"];
    $address=explode(" ",$address);
    $name=explode(" ",$name);
    $lname=implode(" ",array_slice($name,-1,1,true));
    $fname=implode(" ",array_slice($name,0,-1,true));


    $nr=implode(" ",array_slice($address,-1,1, true));
    $straat=implode(" ",array_slice($address,0,-1,true));
}
?>

    <!-- Body order page -->
<div>
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
                    <label for="straat">Straatnaam</label>
                    <input type="text" name="straat" id="straat" required pattern="[A-Z a-z]{1,}"
                           value="<?php print (isset($_SESSION["loggedin"])) ? $straat :""; ?>"><br>
                </div>
                <div class="NAWcol">
                    <label for="huisnr">Huisnr & toevoeging</label>
                    <input type="text" name="huisnr" id="huisnr" required pattern="[0-9]{1,}[a-zA-Z]{0,1}"
                           value="<?php print (isset($_SESSION["loggedin"])) ? $nr :""; ?>"><br>
                    <label for="postcode">Postcode</label>
                    <input type="text" name="postcode" id="postcode" required pattern="[0-9]{4,4}+[A-Z]{2,2}"
                           value="<?php print (isset($_SESSION["loggedin"])) ? $postCode :""; ?>"><br>
                </div>
                <div class="NAWRow">
                    <label for="woonplaats">Plaats</label>
                    <input type="text" name="woonplaats" id="woonplaats" required pattern="[a-z A-Z]{1,}"
                           value="<?php print (isset($_SESSION["loggedin"])) ? $location :""; ?>"><br>

                </div>
                <div class="NAWRow">
                    <label for="gender"><h3>Aanhef</h3></label>
                    <div class="NAWcol">
                        <input class="radio" type="radio" name="gender" id="mevrouw" value="V" required <?php if(isset($_SESSION["loggedin"])){if($Gender=="V"){print "checked ";}}?> >Mevrouw
                    </div>
                    <div class="NAWcol">
                        <input class="radio" type="radio" name="gender" id="meneer" value="M" <?php if(isset($_SESSION["loggedin"])){if($Gender=="M"){print "checked ";}}?> >Meneer
                    </div>
                    <div class="NAWcol">
                        <input class="radio" type="radio" name="gender" id="geenvanbeide" value="X" <?php if(isset($_SESSION["loggedin"])){if($Gender=="X"){print "checked ";}}?> >Geen van beide
                    </div>
                </div>
                <div class="NAWRow">
                    <br>
                   <label for="NAWgegevens"><h3>Persoonsgegevens</h3></label>
                    <label for="fname">Voornaam</label>
                    <input type="text" name="voornaam" id="voornaam" required pattern="[A-Z a-z]{1,}"
                           value="<?php print (isset($_SESSION["loggedin"])) ? $fname :""; ?>"><br>
                    <label for="lname">Achternaam</label>
                    <input type="text" name="achternaam" id="achternaam" required pattern="[a-z A-Z]{1,}"
                           value="<?php print (isset($_SESSION["loggedin"])) ? $lname :""; ?>"><br>
                    <label for="Telefoon">Telefoonnummer</label>
                    <input type="tel" id="telefoonnummer" name="telefoonnummer" pattern="[0]{1}[0-9]{1}[0-9]{8}" required
                           value="<?php print (isset($_SESSION["loggedin"])) ? $tel :""; ?>">
                </div>
            </form>
        </div>
        <div id="the-movable-element-id" class="totalPrice">
            <?php
            print ("Subtotaal: €".number_format($_SESSION['subPrijs'], 2, ",", ".")."<br>");
            print("Korting: -€".number_format($_SESSION['korting'],2,",", ".")."<br>");
            print("Verzendkosten: €".number_format($_SESSION['verzendkosten'],2,",", "."). "<br>");
            print("Totaal: €".number_format($_SESSION['totaalPrijs'],2,",", "."). "<br>"); ?>
        <button type="submit" form="Nawgegevens" value="Submit" class='buttonRev button1'>bestellen</button>
        </div>
        <script>
            $(window).scroll(function() {
                var scrollTop = $(window).scrollTop();
                console.log("scrollTop>>>" + scrollTop);
                if (scrollTop == 0) {
                    $("#the-movable-element-id").css({"margin-top": "0px"});
                } else {
                    $("#the-movable-element-id").css({"margin-top": ($(window).scrollTop()) + "px"});
                }
            });
        </script>
    </div>
    <form class="mt-3" action="Cart.php">
        <input type="submit" class='button button1' value="terug naar winkelmandje">
    </form>

<?php
} else {
    ?>
    <div class="row">
        <h1 style="min-width: 500px;">Je hebt nog niks om te bestellen!</h1>
    </div>
    <form class="mt-3" action="categories.php">
        <input type="submit" class='button button1' value="terug naar categorieën">
    </form>

<?php


}
    include __DIR__ . "/footer.php";
?>
</div>
