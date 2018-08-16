<?php
    $selected="recipes";
    include_once 'includes/pdo.inc.php';

    if (empty($_GET['id'])) {
        header("Location: pagenotfound.php");
    } else {

        $sql = "SELECT * FROM recipes WHERE id = :id";
        $stmt = $pdo_conn->prepare($sql);
        $stmt->bindValue('id',$_GET['id']);
        $stmt->execute();

        if (!($row = $stmt->fetch(PDO::FETCH_ASSOC))) {
            header("Location: pagenotfound.php");
        } else {
            $title = $row['title'];
            $recipe_active_time = $row['recipe_active_time'];
            $recipe_wait_time = $row['recipe_wait_time'];
            $recipe_serves = $row['recipe_serves'];
            $social_fb = $row['social_fb'];
            $social_twtr = $row['social_twtr'];
            $social_pnt = $row['social_pnt'];
            $social_snoo = $row['social_snoo'];
            $print_pdf = $row['print_pdf'];
            $intro_html = $row['intro_html'];
            $ingredients_html = $row['ingredients_html'];
            $method_html = $row['method_html'];
            $intro_img = $row['intro_img'];
        }
    }
    $titleSuffix=" - $title";
    include_once 'header.php';
?>

<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId            : '1079304678904325',
      autoLogAppEvents : true,
      xfbml            : true,
      version          : 'v3.1'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>


<div class="recipe_intro">
    <div class="recipe_intro_img">
        <img src="<?= $intro_img ?>" alt="<?=$title?>">
    </div>
    <h1><?= $title ?></h1>
    <p><i class="fas fa-clock" title="Acitve time"></i> <?= $recipe_active_time ?> &nbsp;&nbsp;
        <i class="fas fa-bed" title="Waiting time"></i> <?= $recipe_wait_time ?> &nbsp;&nbsp;
        <i class="fas fa-utensils" title="Serving size"></i> <?= $recipe_serves ?> &nbsp;&nbsp;
        <br id="mobile_linebreak"><br id="mobile_linebreak">
        <a id="fbBtn"><i class="fab fa-facebook button"title="Share to Facebook"></i></a> &nbsp;&nbsp;
        <a href="<?= $social_twtr ?>"><i class="fab fa-twitter button" title="Share to Twitter"></i></a> &nbsp;&nbsp;
        <a href="<?= $social_pnt ?>"><i class="fab fa-pinterest button" title="Share to Pinterest"></i></a> &nbsp;&nbsp;
        <a href="<?= $social_snoo ?>"><i class="fab fa-reddit button" title="Share to Reddit"></i></a> &nbsp;&nbsp;
        <a><i class="fas fa-copy button" title="Copy to clipboard" onclick="copyToClip()"></i></a> &nbsp;&nbsp;
        <a href="<?= $print_pdf ?>"><i class="fas fa-print button"  title="Print recipe"></i></a>
    </p>
    <?= $intro_html ?>
</div>
<div class="recipe_content_container">
    <div class="ingredients">
        <h2>Ingredients</h2>
        <?= $ingredients_html ?>
    </div>
    <div class="method">
        <h2>Method</h2>
        <?= $method_html ?>
    </div>
</div>

<script>
document.getElementById('fbBtn').onclick = function() {
  FB.ui({
    method: 'share',
    display: 'popup',
    href: window.location.href,
  }, function(response){});
}
</script>

<?php include_once 'footer.php'; ?>
