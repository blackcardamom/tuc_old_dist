<?php $selected="gallery"; $titleSuffix=" - Gallery";  include_once 'header.php'; ?>

<div class="gallery_wrapper">
    <div class="gallery_selection_window">
        <img src="gallery/Brownies_DSC05549.jpg" onclick="changeDisplay(this)">
        <img src="gallery/DSC05254.JPG" onclick="changeDisplay(this)">
        <img src="gallery/DSC05324.JPG" onclick="changeDisplay(this)">
        <img src="gallery/DSC05349.JPG" onclick="changeDisplay(this)">
        <img src="gallery/DSC05413.JPG" onclick="changeDisplay(this)">
        <img src="gallery/DSC05434.JPG" onclick="changeDisplay(this)">
        <img src="gallery/DSC05460.JPG" onclick="changeDisplay(this)">
        <img src="gallery/Mango%20Tart_DSC05490.jpg" onclick="changeDisplay(this)">
        <img src="gallery/portrait2.jpg" onclick="changeDisplay(this)">
    </div>
    <div class="gallery_display_port"><img src="gallery/Brownies_DSC05549.jpg" id="displayed_pic"></div>
</div>
<script>
    function changeDisplay(elem) {
        document.getElementById("displayed_pic").src = elem.src;
    }
</script>

<?php include_once 'footer.php'; ?>
