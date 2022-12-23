<!-- dit is het bestand dat wordt geladen zodra je naar de website gaat -->
<?php
include __DIR__ . "/header.php";
?>
<div class="IndexStyle">
    <div class="col-11">
        <div class="TextPrice">

                <!-- Slideshow container -->
                <div class="slideshow-container">
                    <a href="view.php?id=93">
                    <!-- Full-width images with number and caption text -->
                    <div class="mySlides fade">
                        <img src="Public\StockItemIMG\slide1.png" style="width:100%">
                    </div>
                    </a>
                    <a href="view.php?id=52">
                    <div class="mySlides fade">
                        <img src="Public\StockItemIMG\slide2.png" style="width:100%">
                    </div>
                    </a>
<!-- hieronder staan de slides die worden weergegeven op de index pagina -->
                    <!-- Next and previous buttons -->
                    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                    <a class="next" onclick="plusSlides(1)">&#10095;</a>
                </div>
                <br>

                <!-- The dots/circles -->
                <div style="text-align:center">
                    <span class="dot" onclick="currentSlide(1)"></span>
                    <span class="dot" onclick="currentSlide(2)"></span>
                </div>

                <script>
                    let slideIndex = 1;
                    showSlides(slideIndex);

                    // Next/previous controls
                    function plusSlides(n) {
                        showSlides(slideIndex += n);
                    }

                    // Thumbnail image controls
                    function currentSlide(n) {
                        showSlides(slideIndex = n);
                    }

                    function showSlides(n) {
                        let i;
                        let slides = document.getElementsByClassName("mySlides");
                        let dots = document.getElementsByClassName("dot");
                        if (n > slides.length) {slideIndex = 1}
                        if (n < 1) {slideIndex = slides.length}
                        for (i = 0; i < slides.length; i++) {
                            slides[i].style.display = "none";
                        }
                        for (i = 0; i < dots.length; i++) {
                            dots[i].className = dots[i].className.replace(" active", "");
                        }
                        slides[slideIndex-1].style.display = "block";
                        dots[slideIndex-1].className += " active";
                    }
                </script>
            </a>
    </div>
</div>
<?php
include __DIR__ . "/footer.php";
?>
