<?php
    include_once 'includes/pdo.inc.php';
    include_once 'includes/base_assumptions.inc.php';

    // Get all variables we need to load the page

    if (empty($_GET['id'])) {
        header("Location: $website_root/pagenotfound.php?err=no_id_provided");
    } else {

        $sql = "SELECT * FROM blogposts WHERE id = :id";
        $stmt = $pdo_conn->prepare($sql);
        $stmt->bindValue('id',$_GET['id']);
        $stmt->execute();

        if (!($row = $stmt->fetch(PDO::FETCH_ASSOC))) {
            header("Location: $website_root/pagenotfound.php?err=no_recipe_".$_GET['id']);
        } else {
            $title = $row['title'];
            $social_fb = $row['social_fb'];
            $social_twtr = $row['social_twtr'];
            $social_pnt = $row['social_pnt'];
            $social_snoo = $row['social_snoo'];
            $intro = $row['intro'];
            $content_html = $row['content_html'];
        }
    }

    $titlePrefix="$title | ";
    $meta_desciption = strip_tags($intro);

    $meta_iscontent = 1;
    $meta_url = $website_root ."/blogpost_view.php?id=" . htmlspecialchars($_GET['id']);
    include_once 'header.php';
?>

<div class="blog_grid-container">
    <div class="blog_container">
        <h1 class="blog_title"><?= $title ?></h1>
        <div class="blog_socials"><span id='no_break'>
            <a href="<?= $social_fb ?>"><i class="fab fa-facebook button" title="Share to Facebook"></i></a> &nbsp;&nbsp;
            <a href="<?= $social_twtr ?>"><i class="fab fa-twitter button" title="Share to Twitter"></i></a> &nbsp;&nbsp;
            <a href="<?= $social_pnt ?>"><i class="fab fa-pinterest button" title="Share to Pinterest"></i></a> &nbsp;&nbsp;
            <a href="<?= $social_snoo ?>"><i class="fab fa-reddit button" title="Share to Reddit"></i></a> &nbsp;&nbsp;
            <a><i class="fas fa-copy button" title="Copy to clipboard" onclick="copyToClip()"></i></a> &nbsp;&nbsp;
            </span>
        </div>
        <div class="blog_main_content"><?= $content_html ?></div>
    </div>
    <div class="blog_ad_container">AD</div>
</div>


<?php include_once 'footer.php'; ?>
