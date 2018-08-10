<?php include_once 'header.php'; ?>
<div class="recipe_intro">
    <div class="recipe_intro_img">
        <img id="recipe_intro_img">
    </div>
    <h1 id="recipe_title"></h1>
    <p><i class="fas fa-clock"></i> <span id="recipe_time"></span> &nbsp;&nbsp;
        <i class="fas fa-utensils"></i> <span id="recipe_serves"></span> &nbsp;&nbsp;
        <br id="mobile_linebreak"><br id="mobile_linebreak">
        <i class="fab fa-facebook button" id="social_fb" title="Share to Facebook"></i> &nbsp;&nbsp;
        <i class="fab fa-twitter button" id="social_twtr" title="Share to Twitter"></i> &nbsp;&nbsp;
        <i class="fab fa-pinterest button" id="social_pnt" title="Share to Pinterest"></i> &nbsp;&nbsp;
        <i class="fab fa-reddit button" id="social_snoo" title="Share to Reddit"></i> &nbsp;&nbsp;
        <i class="fas fa-copy button" title="Copy to clipboard" onclick="copyToClip()"></i> &nbsp;&nbsp;
        <i class="fas fa-print button" id="print_pdf" title="Print recipe"></i>
    </p>
    <p id="recipe_intro"></p>
</div>
<div class="recipe_content_container">
    <div class="ingredients" id="ingredients"></div>
    <div class="method" id="method"></div>
</div>
<?php include_once 'footer.php'; ?>
