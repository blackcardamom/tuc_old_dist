<?php
    $selected="gallery";
    $titleSuffix=" - Gallery";
    include_once 'header.php';
    include_once 'includes/base_assumptions.inc.php';
?>

<div class="gallery_wrapper">
    <div class="gallery_selection_window">
        <img src="<?= $website_root ?>/gallery/Brownies_DSC05549.jpg" onclick="changeDisplay(this)">
        <img src="<?= $website_root ?>/gallery/DSC05254.JPG" onclick="changeDisplay(this)">
        <img src="<?= $website_root ?>/gallery/DSC05324.JPG" onclick="changeDisplay(this)">
        <img src="<?= $website_root ?>/gallery/DSC05349.JPG" onclick="changeDisplay(this)">
        <img src="<?= $website_root ?>/gallery/DSC05413.JPG" onclick="changeDisplay(this)">
        <img src="<?= $website_root ?>/gallery/DSC05434.JPG" onclick="changeDisplay(this)">
        <img src="<?= $website_root ?>/gallery/DSC05460.JPG" onclick="changeDisplay(this)">
        <img src="<?= $website_root ?>/gallery/Mango%20Tart_DSC05490.jpg" onclick="changeDisplay(this)">
        <img src="<?= $website_root ?>/gallery/portrait2.jpg" onclick="changeDisplay(this)">
    </div>
    <div class="gallery_display_port"><img src="<?= $website_root ?>/gallery/Brownies_DSC05549.jpg" id="displayed_pic"></div>
</div>
<script>
    function changeDisplay(elem) {
        document.getElementById("displayed_pic").src = elem.src;
    }
</script>

<?php include_once 'footer.php'; ?>
