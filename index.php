<?php
    include_once 'header.php';
    include_once 'includes/conn.inc.php';
?>

<div class='feature_parent'>
    <div class='feature_left_arrow button' onclick='changeCard(-1)'><i class='fas fa-arrow-left'></i></div>
    <div class='feature_right_arrow button' onclick='changeCard(1)'><i class='fas fa-arrow-right'></i></div>
    <div class='scrolling-wrapper-flexbox' id='scrolling_container'>
        <!--
        <div class='feature_card' id='card1'>
            <div class='feature_card_img'><a href='recipes/chocolate_cake/recipe.html'><img src='recipes/chocolate_cake/landscape_picture.jpg'></a></div>
            <div class='feature_card_text'>
                <h2><a href='recipes/chocolate_cake/recipe.html'>Chocolate Celebration Cake</a></h2>
                <p><i class='fas fa-clock'></i> 1 hour 30 minutes + cooling time </time> &nbsp;&nbsp; <i class='fas fa-utensils'></i> 8 servings</p>
                <p>Seeing as this is the first recipe on the website it seems fit for it to be a celebration cake! The most challenging eggless bake I have tackled has to be cake, and I was very happy when this came out just how I wanted. The cake itself
                    is a little like a muffin, but when you put it together with the ganache I think it would rival any chocolate cake out there. So please, enjoy.</p>
            </div>
        </div> -->
        <?php
            $sql = 'SELECT a.id, a.title, a.recipe_active_time, a.recipe_wait_time, a.recipe_serves, a.intro_html, b.position, b.feature_img
                    FROM recipes a
                    RIGHT JOIN feature_cards b
                    ON a.id=b.recipe_id
                    ORDER BY position ASC';
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);

            if($resultCheck == 0) {
                echo 'No features returned from datbase';
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $recipe_active_time = $row['recipe_active_time'];
                    $recipe_wait_time = $row['recipe_wait_time'];
                    $recipe_serves = $row['recipe_serves'];
                    $intro_html = $row['intro_html'];
                    $feature_img = $row['feature_img'];

                    $feature_card = "
                        <div class='feature_card' id='feature$id'>
                            <div class='feature_card_img'><a href='recipe_view.php?id=$id'><img src='$feature_img'></a></div>
                            <div class='feature_card_text'>
                                <h2><a href='recipe_view.php?id=$id'>$title</a></h2>
                                <p><i class='fas fa-clock'></i> $recipe_active_time &nbsp;&nbsp;
                                    <i class='fas fa-bed' title='Waiting time'></i> $recipe_wait_time &nbsp;&nbsp;
                                    <i class='fas fa-utensils'></i> $recipe_serves</p>
                                $intro_html
                            </div>
                        </div>";
                    echo $feature_card;
                }
            }

        ?>

    </div>
</div>

<div class='my_content'>
    <h2 style='margin:0'>Welcome to The Ugly Crossiant</h2>
    <p>This is the home page, what should we put here?</p>
</div>
<script src='https://code.jquery.com/jquery-1.10.2.js'></script>
<script>
    var cardNames = $(".feature_card").map(function() { return this.id; }).toArray();

    // Makes modular arithmetic work with negative numbers
    function mod(n, m) {
        return ((n % m) + m) % m;
    }

    // Javascript scroll animation
    // Taken from https://gist.github.com/andjosh/6764939
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

    // Taken from https://gist.github.com/andjosh/6764939
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

    // Adjusted from https://gist.github.com/andjosh/6764939
    function changeCard(direction) {
        // Typical direction is 1 or -1 (right or left resp.)
        var currentCard = window.location.hash.substr(1);
        // No hash means we've just opened the page so must be on the first card
        if (currentCard == '') {
            currentCard = cardNames[0];
        }
        // Finds current card number
        var num = cardNames.indexOf(currentCard);
        // Calculates new card number
        num = mod((num + direction), cardNames.length);
        // Smooth scrolling
        var container = document.getElementById('scrolling_container');
        var newCard = document.getElementById(cardNames[num]);
        var $newCard = $(newCard)
        scrollTo(container, $newCard.position().left + container.scrollLeft, 500);
        // Updates hash
        window.location.hash = '#' + cardNames[num];
    }
</script>

<?php include_once 'footer.php'; ?>
