<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";

?>
<h1 style="min-width: 500px;">UW BESTELING:</h1>
<?php
$cart = getCart();

?>

<!-- Body cart -->

<?php
if($cart!=null){
    ?>
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
                              <div class="number">
                                  <?php if(isset($_GET["update$productID"])){updateItem($productID,$_GET["aantal"])  ;} ?>
                                  <form method="get">
                                      <input type="number" name="aantal" value="<?php print($amount)?>">
                                      <input type="submit" name="update<?php print($productID) ?>" hidden>
                                  </form>
                              </div>
                              <div class="Controle">
                                  <div class="plus">
                                      <?php if(isset($_GET["plus$productID"])){increaseItem($productID);} ?>
                                      <form method="get">
                                          <input type="submit" name="plus<?php print($productID) ?>" value="+">
                                      </form>
                                  </div>
                                  <?php if ($amount < 1) { ?>
                                      <div class="min">
                                          <?php if(isset($_GET["min$productID"])){deleteItem($productID);} ?>
                                          <form method="get">
                                              <input type="submit" name="min<?php print($productID) ?>" value="-">
                                          </form>
                                      </div>
                                  <?php }
                                  else { ?>
                                      <div class="min">
                                          <?php if(isset($_GET["min$productID"])){decreaseItem($productID)  ;} ?>
                                          <form method="get">
                                              <input type="submit" name="min<?php print($productID) ?>" value="-">
                                          </form>
                                      </div>
                                  <?php } ?>

                              </div>
                        </div>
                          <div class="trash">
                              <?php if(isset($_GET["delete$productID"])){deleteItem($productID);} ?>
                              <form method="get">
                                  <input type="submit" name="delete<?php print($productID) ?>" value="X">
                              </form>
                          </div>
                      </div>
                  </div>
              </div>
            <?php
        }?>
    </div>
    <div class="totalPrice">
            <?php
            $totaalPrice=0;
            foreach($cart as $productID => $amount){
                $StockItem = getStockItem($productID, $databaseConnection);
                $price=$StockItem["SellPrice"]*$amount;
                $totaalPrice+=$price;
            }
            print("totaal prijs: €".number_format($totaalPrice,2,",", "."));?>
        </div>

    <?php
}
else{
    ?>
    <div class="row">
        <h1 style="min-width: 500px;">De winkelwagen is helaas nog leeg</h1>
    </div>
    <div class="HrefDecoration">
        <a href="/nerdygadgets/categories.php" class='HrefDecoration'>>terug naar categorieën</a>
    </div>

    <?php
}

include __DIR__ . "/footer.php";
?>
