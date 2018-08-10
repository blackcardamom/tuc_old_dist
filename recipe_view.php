<?php
    include_once 'header.php';
    include_once 'includes/conn.inc.php'
?>
<div class="recipe_intro">
    <div class="recipe_intro_img">
        <img id="recipe_intro_img">
    </div>
    <h1 id="recipe_title"></h1>
    <p><i class="fas fa-clock" title="Cooking time"></i> <span id="recipe_time"></span> &nbsp;&nbsp;
        <i class="fas fa-utensils" title="Serving size"></i> <span id="recipe_serves"></span> &nbsp;&nbsp;
        <br id="mobile_linebreak"><br id="mobile_linebreak">
        <a id="social_fb"><i class="fab fa-facebook button"title="Share to Facebook"></i></a> &nbsp;&nbsp;
        <a id="social_twtr"><i class="fab fa-twitter button" title="Share to Twitter"></i></a> &nbsp;&nbsp;
        <a id="social_pnt"><i class="fab fa-pinterest button" title="Share to Pinterest"></i></a> &nbsp;&nbsp;
        <a id="social_snoo"><i class="fab fa-reddit button" title="Share to Reddit"></i></a> &nbsp;&nbsp;
        <a><i class="fas fa-copy button" title="Copy to clipboard" onclick="copyToClip()"></i></a> &nbsp;&nbsp;
        <a id="print_pdf"><i class="fas fa-print button"  title="Print recipe"></i></a>
    </p>
    <p id="recipe_intro"></p>
</div>
<div class="recipe_content_container">
    <div class="ingredients" id="ingredients"></div>
    <div class="method" id="method"></div>
</div>
<?php include_once 'footer.php'; ?>
