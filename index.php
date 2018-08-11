<?php include_once 'header.php'; ?>

<div class="feature_parent">
    <div class="feature_left_arrow button" onclick="changeCard(-1)"><i class="fas fa-arrow-left"></i></div>
    <div class="feature_right_arrow button" onclick="changeCard(1)"><i class="fas fa-arrow-right"></i></div>
    <div class="scrolling-wrapper-flexbox" id="scrolling_container">
        <div class="feature_card" id="card1">
            <div class="feature_card_img"><a href="recipes/chocolate_cake/recipe.html"><img src="recipes/chocolate_cake/landscape_picture.jpg"></a></div>
            <div class="feature_card_text">
                <h2><a href="recipes/chocolate_cake/recipe.html">Chocolate Celebration Cake</a></h2>
                <p><i class="fas fa-clock"></i> 1 hour 30 minutes + cooling time </time> &nbsp;&nbsp; <i class="fas fa-utensils"></i> 8 servings</p>
                <p>Seeing as this is the first recipe on the website it seems fit for it to be a celebration cake! The most challenging eggless bake I have tackled has to be cake, and I was very happy when this came out just how I wanted. The cake itself
                    is a little like a muffin, but when you put it together with the ganache I think it would rival any chocolate cake out there. So please, enjoy.</p>
            </div>
        </div>
        <div class="feature_card" id="card2">
            <div class="feature_card_img"><a href="recipes/lemon_meringue/recipe.html"><img src="recipes/lemon_meringue/landscape_picture.jpg"></a></div>
            <div class="feature_card_text">
                <h2><a href="recipes/lemon_meringue/recipe.html">Lemon Meringue Pie</a></h2>
                <p><i class="fas fa-clock"></i> 2 hours &nbsp;&nbsp; <i class="fas fa-utensils"></i> 4 as a main</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent sed nulla et libero sodales vestibulum. Etiam ac purus ante. Vestibulum non urna dictum, pharetra nunc vitae, cursus massa. Suspendisse tempus et mi quis dignissim. Aliquam
                    at massa ipsum. Nulla viverra tortor sit amet ullamcorper pharetra. Donec sit amet erat blandit, malesuada lorem vel, tristique purus. Proin semper nec elit ac bibendum.</p>
            </div>
        </div>
        <div class="feature_card" id="card3">
            <div class="feature_card_img"><a href="recipes/sweet_pastry/recipe.html"><img src="recipes/sweet_pastry/landscape_picture.jpg"></a></div>
            <div class="feature_card_text">
                <h2><a href="recipes/lemon_meringue/recipe.html">Basic Sweet Pastry</a></h2>
                <p><i class="fas fa-clock"></i> 20 minutes + 30 minutes resting &nbsp;&nbsp;</p>
                <p>I have been using this recipe for sweet pastry since I started making eggless desserts and it hasn’t failed me yet! Once you crack the pastry shell it opens up a world of possibilities, holding any sort of fruity, custardy, chocolatey
                    mess you can think of - trust me they’re very forgiving. Here I just give a guide to making the pastry dough, see my other recipe for blind baking.</p>
            </div>
        </div>
        <div class="feature_card" id="card4">
            <h2>Card 4</h2>
        </div>
    </div>
</div>
<div class="my_content">
    <h2 style="margin:0">Welcome to The Ugly Crossiant</h2>
    <p>This is the home page, what should we put here?</p>
</div>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script>
    var cardNames = ["card1", "card2", "card3", "card4"];

    // Makes modular arithmetic work with negative numbers
    function mod(n, m) {
        return ((n % m) + m) % m;
    }

    // Javascript scroll animation
    // From https://gist.github.com/andjosh/6764939
    function scrollTo(element, to, duration) {
        var start = element.scrollLeft,
            change = to - start,
            currentTime = 0,
            increment = 20;

        var animateScroll = function() {
            currentTime += increment;
            var val = Math.easeInOutQuad(currentTime, start, change, duration);
            element.scrollLeft = val;
            if (currentTime < duration) {
                setTimeout(animateScroll, increment);
            }
        };
        animateScroll();
    }

    // Adjusted from https://gist.github.com/andjosh/6764939
    //t = current time
    //b = start value
    //c = change in value
    //d = duration
    Math.easeInOutQuad = function(t, b, c, d) {
        t /= d / 2;
        if (t < 1) return c / 2 * t * t + b;
        t--;
        return -c / 2 * (t * (t - 2) - 1) + b;
    };

    function changeCard(direction) {
        // Typical direction is 1 or -1 (right or left resp.)
        var currentCard = window.location.hash.substr(1);
        // No hash means we've just opened the page so must be on the first card
        if (currentCard == "") {
            currentCard = cardNames[0];
        }
        // Finds current card number
        var num = cardNames.indexOf(currentCard);
        // Calculates new card number
        num = mod((num + direction), cardNames.length);
        // Smooth scrolling
        var container = document.getElementById("scrolling_container");
        var newCard = document.getElementById(cardNames[num]);
        var $newCard = $(newCard)
        scrollTo(container, $newCard.position().left + container.scrollLeft, 500);
        // Updates hash
        window.location.hash = "#" + cardNames[num];
    }
</script>

<?php include_once 'footer.php'; ?>
