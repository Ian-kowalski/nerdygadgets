<?php
function getColorMode(){
    if(isset($_SESSION['colorMode'])){               //controleren of winkelmandje (=cart) al bestaat
    $cart = $_SESSION['colorMode'];                  //zo ja:  ophalen
    } else{
        $colorMode = array();                            //zo nee: dan een nieuwe (nog lege) array
    }
    return $colorMode;                               // resulterend winkelmandje terug naar aanroeper functie
}