<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";
include "discountFunctions.php";
?>
<h1 style="min-width: 500px;">UW BESTELING:</h1>
<?php
$cart = getCart();

?>

<!-- Body cart -->

<?php
if($cart!=null){
    ?>
        <div class="cart_price">
            <div class="cart">
                <?php
                foreach($cart as $productID => $amount){
                    $StockItem = getStockItem($productID, $databaseConnection);
                    $StockItemImage = getStockItemImage($productID, $databaseConnection);
                    $ProduktLink=("view.php?id=".$productID);
                    ?>
                      <div class="cartRow">
                          <div class="rowLeft">
                              <!-- Foto -->
                              <?php
                              if (isset($StockItemImage)&&count($StockItemImage) >= 1) {
                                  ?>
                              <div class="productImage"
                                   style="background-image: url('Public/StockItemIMG/<?php print $StockItemImage[0]['ImagePath']; ?>');">
                              </div>
                              <?php
                              } else{
                                  ?>
                                  <div class="productImage"
                                       style="background-image: url('Public/StockGroupIMG/<?php print $StockItem['BackupImagePath']; ?>'); background-size: cover;">
                                  </div>
                                  <?php
                              }
                              ?>
                          </div>
                          <div class="rowMid">
                            <?php
                                print("<a href=".$ProduktLink." class='HrefDecoration'>".$StockItem["StockItemName"]." </a>")
                            ?>
                          </div>
                          <div class="rowRight">
                              <div class="price">
                                  <?php
                                  print("per stuk:<br>€".number_format($StockItem["SellPrice"], 2, ",", "."))
                                  ?>
                              </div>
                              <div class="amountControl">
                                  <div class="counter">
                                      aantal:
                                      <div>
                                          <?php if(isset($_GET["update$productID"])){updateItem($productID,$_GET["aantal"])  ;} ?>
                                          <form method="get" id="counter">
                                              <input class="number" type="number" name="aantal" value="<?php print($amount)?>" >
                                              <input type="submit" name="update<?php print($productID) ?>" hidden>
                                          </form>
                                      </div>
                                      <div class="Controle">
                                          <div>
                                              <?php if(isset($_GET["plus$productID"])){increaseItem($productID);} ?>
                                              <button type="submit" form="counter" class="plus plus1" name="plus<?php print($productID) ?>" ><i class="fa fa-plus"></i></button>
                                          </div>
                                          <?php if ($amount <= 1) { ?>
                                              <div>
                                                  <?php if(isset($_GET["min$productID"])){deleteItem($productID);} ?>
                                              </div>
                                          <?php }
                                          if($amount < 0) {reverseItem($productID);}
                                          else { ?>
                                              <div>
                                                  <?php if(isset($_GET["min$productID"])){decreaseItem($productID)  ;} ?>
                                                  <button type="submit" form="counter" class="plus plus1" name="min<?php print($productID) ?>" ><i class="fa fa-minus"></i></button>
                                              </div>
                                          <?php } ?>

                                      </div>
                                </div>
                                  <div>
                                      <?php if(isset($_GET["delete$productID"])){deleteItem($productID);} ?>
                                      <button type="submit" form="counter" class="trash" name="delete<?php print($productID) ?>" ><i class="fa fa-trash-o fa_custom"></i></button>
                                  </div>
                              </div>
                          </div>
                      </div>
                    <?php
                }?>
            </div>
            <div id="the-movable-element-id" class="totalCartPrice">
                <?php
                $subPrijs=0;
                $verzendkosten=6.95;
                foreach($cart as $productID => $amount){
                    $StockItem = getStockItem($productID, $databaseConnection);
                    $prijs=$StockItem["SellPrice"]*$amount;
                    $subPrijs+=$prijs;
                }
                if($subPrijs > "50") {
                    $verzendkosten = 0;
                }
                $korting = $kortingPercentage * $subPrijs;
                $totaalPrijs = $subPrijs - $korting + $verzendkosten;

                $_SESSION['subPrijs'] = $subPrijs;
                $_SESSION['korting'] = $korting;
                $_SESSION['kortingPercentage'] = $kortingPercentage * 100 . "%";
                $_SESSION['verzendkosten'] = $verzendkosten;
                $_SESSION['totaalPrijs'] = round($totaalPrijs, 2);

                print ("Subtotaal: €".number_format($subPrijs, 2, ",", ".")."<br>");
                print("Korting: -€".number_format($korting,2,",", "."). " (-" . $_SESSION['kortingPercentage'] . ")<br>");
                print("Verzendkosten: €".number_format($verzendkosten,2,",", "."). "<br>");
                print("Totaal: €".number_format($totaalPrijs ,2,",", "."). "<br>"); ?>
                <h5 class="FilterTopMargin"><i class="fas fa-dollar-sign"></i> Kortingscode</h5>
                <form method="get">
                    <input type="text" name="discountCode" id="discountCode" value="<?php echo($_SESSION['discountCode']) ?>">
                </form>
                <form action="order.php">
                    <button class='buttonRev cartbutton'>bestellen</button>
                </form>
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

    <form class="mt-3" action="categories.php">
        <input type="submit" class='button button1' value="terug naar categorieën">
    </form>
    <?php
}
else{
    ?>
    <div class="row">
        <h1 style="min-width: 500px;">De winkelwagen is helaas nog leeg</h1>
    </div>
    <form class="mt-3" action="categories.php">
        <input type="submit" class='buttonRed button1' value="terug naar categorieën">
    </form>

    <?php
}

include __DIR__ . "/footer.php";
?>
