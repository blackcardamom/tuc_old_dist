<?php
    $selected="gallery";
    $titleSuffix=" - Gallery";
    include_once 'header.php';
    include_once 'includes/base_assumptions.inc.php';
?>

<div class="gallery_wrapper">
    <div class="gallery_selection_window">
        <?php
            $path = $website_root."/gallery/" ;
            $acceptableExtensions = Array("jpg", "jpeg", "JPG", "png", "PNG");

            $dir = new DirectoryIterator($path);
            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot() && in_array($fileinfo->getExtension(),$acceptableExtensions)) {
                    if (!isset($firstFile)) {
                        $firstFile = $fileinfo->getPathname();
                    }
                    echo "<img src='".$fileinfo->getPathname()."' onclick = 'changeDisplay(this)'>";
                }
            }
        ?>
    </div>
    <div class="gallery_display_port"><img src="<?= $firstFile ?>" id="displayed_pic"></div>
</div>
<script>
    function changeDisplay(elem) {
        document.getElementById("displayed_pic").src = elem.src;
    }
</script>

<?php include_once 'footer.php'; ?>
