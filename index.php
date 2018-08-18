<?php
    include_once 'header.php';
    include_once 'includes/conn.inc.php';
    include_once 'includes/pdo.inc.php';
    include_once 'includes/base_assumptions.inc.php'
?>

<div class="index_grid-container">
    <div class='index_feature_wrapper'>
        <input type="hidden" id="current_card">
        <div class='feature_left_arrow button' onclick='changeCard(-1)'><i class='fas fa-arrow-left'></i></div>
        <div class='feature_right_arrow button' onclick='changeCard(1)'><i class='fas fa-arrow-right'></i></div>

        <div class='scrolling-wrapper-flexbox' id='scrolling_container'>
            <?php
                //USE PDOS!?
                $sql = 'SELECT a.id, a.title, a.recipe_active_time, a.recipe_wait_time, a.recipe_serves, a.intro_html, b.position, b.feature_img, b.feature_id
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
                        $feature_img = $website_root . "/" .$row['feature_img'];
                        $feature_id = $row['feature_id'];

                        $feature_card = "
                            <div class='feature_card' id='feature$feature_id'>
                                <div class='feature_card_img'><a href='$website_root/recipe_view.php?id=$id'><img src='$feature_img' alt='$title'></a></div>
                                <div class='feature_card_text'>
                                    <h2><a href='$website_root/recipe_view.php?id=$id'>$title</a></h2>
                                    <p style='line-height:1.5em;'><span class='no_break'><i class='fas fa-clock'></i> $recipe_active_time &nbsp;&nbsp;</span>
                                        <span class='no_break'><i class='fas fa-bed' title='Waiting time'></i> $recipe_wait_time &nbsp;&nbsp;</span>
                                        <span class='no_break'><i class='fas fa-utensils'></i> $recipe_serves</span></p>
                                    $intro_html
                                </div>
                            </div>";
                        echo $feature_card;
                    }
                }

            ?>

        </div>
    </div>
    <div class="index_blog_wrapper">
        <h2 class="recent_blog_title">Recent blogposts</h2>
        <?php
            $query = "SELECT * FROM blogposts ORDER BY date_published DESC LIMIT 5";
            $stmt = $pdo_conn->prepare($query);
            if (!$stmt->execute()) {
                echo "<h2>We are having technical issues, no blogposts are currently available.</h2>";
            } else {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $post_card = "
                    <div class='post_card'>
                        <h3><a href='$website_root/blogpost_view.php?id=".$row['id']."'>".$row['title']."</a></h3>
                        <p><span class='blog_date_published'>". date('jS F Y.',strtotime($row['date_published'])) ."</span> ".$row['intro']."</p>
                        <div class='blog_link'><a href='$website_root/blogpost_view.php?id=".$row['id']."'><i class='fas fa-plus-circle'></i> Read more...</a></div>
                    </div>";
                    echo $post_card;
                }
            }
        ?>
    </div>
    <div class="index_sidebar">
        <h3>Advertisments</h3>
    </div>
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
        var currentCard = document.getElementById('current_card').value;
        // No hash means we've just opened the page so must be on the first card
        if (currentCard == '') {
            var num = 0;
        } else {
            var num = parseInt(currentCard, 10);
        }
        // Calculates new card number
        num = mod((num + direction), cardNames.length);
        // Smooth scrolling
        var container = document.getElementById('scrolling_container');
        var newCard = document.getElementById(cardNames[num]);
        var $newCard = $(newCard)
        scrollTo(container, $newCard.position().left + container.scrollLeft, 500);
        // Updates hidden input
        document.getElementById('current_card').value = num;
    }
</script>

<?php include_once 'footer.php'; ?>
