<?php


function getCart(){
    if(isset($_SESSION['cart'])){               //controleren of winkelmandje (=cart) al bestaat
        $cart = $_SESSION['cart'];                  //zo ja:  ophalen
    } else{
        $cart = array();                            //zo nee: dan een nieuwe (nog lege) array
    }
    return $cart;                               // resulterend winkelmandje terug naar aanroeper functie
}

function saveCart($cart){
    $_SESSION["cart"] = $cart;                  // werk de "gedeelde" $_SESSION["cart"] bij met de meegestuurde gegevens
}

function addProductToCart($stockItemID){
    $cart = getCart();                          // eerst de huidige cart ophalen

    if(array_key_exists($stockItemID, $cart)){  //controleren of $stockItemID(=key!) al in array staat
        $cart[$stockItemID] += 1;                   //zo ja:  aantal met 1 verhogen
    }else{
        $cart[$stockItemID] = 1;                    //zo nee: key toevoegen en aantal op 1 zetten.
    }

    saveCart($cart);                            // werk de "gedeelde" $_SESSION["cart"] bij met de bijgewerkte cart
}


// Verwijderen artikel
function deleteItem($ID) {
    $cart=getCart();

    if (array_key_exists($ID, $cart)) {
        unset($cart[$ID]);
        saveCart($cart);
        ?>
        <script>
            window.location.replace('./cart.php')
        </script>
        <?php
    }
}

// Negatief aantal positief maken
function reverseItem($ID) {
    $cart=getCart();

    if (array_key_exists($ID, $cart)) {
        $cart[$ID] = str_replace("-", "", $cart[$ID]);
        saveCart($cart);
        ?>
        <script>
            window.location.replace('./cart.php')
        </script>
        <?php
    }
}

// Verminderen artikel
function decreaseItem($ID){
    $cart=getCart();

    if (array_key_exists($ID, $cart)) {
            $cart[$ID] -= 1;
            saveCart($cart);
        ?>
        <script>
            window.location.replace('./cart.php')
        </script>
        <?php
    }
}



//update artikel
function updateItem($ID,$new) {
    $databaseConnection = connectToDatabase();
    $StockItem= getStockItem($ID, $databaseConnection);
    $cart=getCart();

    if (array_key_exists($ID, $cart)) {
        if($new<$StockItem["QuantityOnHand"]) {
            $cart[$ID]=$new;
            saveCart($cart);
        } else {
            $cart[$ID] = $StockItem["QuantityOnHand"];
            saveCart($cart);
        }
        ?>
        <script>
            window.location.replace('./cart.php')
        </script>
        <?php
    }
}

// Vermeerderen artikel
function increaseItem($ID){
    $databaseConnection = connectToDatabase();
    $StockItem= getStockItem($ID, $databaseConnection);
    $cart=getCart();

    if (array_key_exists($ID, $cart)) {
        if($cart[$ID]<$StockItem["QuantityOnHand"]) {
            $cart[$ID] += 1;
            saveCart($cart);
        } else {
            $cart[$ID] = $StockItem["QuantityOnHand"];
            saveCart($cart);
        }

        ?>
        <script>
            window.location.replace('./cart.php')
        </script>
        <?php
    }
}
