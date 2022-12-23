
<h4 class="FilterTopMargin"><i class="fas fa-euro-sign"></i> Prijs</h4>
            <label for="fname">minimum prijs: </label>
            <input type="input" name="min_price" id="min_price" onchange="this.form.submit()">>
    minimum prijs="<?php print (isset($_GET['min_price'])) ? $_GET['min_price'] : ""; ?>">

            <label for="fname">maximum prijs: </label>
            <input type="input" name="max_price" id="max_price" class="submit" onchange="this.form.submit()">>
    maximum prijs="<?php print (isset($_GET['max_price'])) ? $_GET['max_price'] : ""; ?>">

            <input type="hidden" name="category_id" id="category_id"
                     value="<?php print (isset($_GET['category_id'])) ? $_GET['category_id'] : ""; ?>">
            <input type="hidden" name="sort" id="sort" value="<?php print ($_SESSION['sort']); ?>">

            <h4 class="FilterTopMargin"><i class="fas fa-tags"> </i> Prijs</h4>
            <div class="range_container">
                <div class="sliders_control">
                    <input id="fromSlider" type="range" value="0" min="0" max="100"/>
                    <input id="toSlider" type="range" value="100" min="0" max="100"/>
                </div>
            </div>