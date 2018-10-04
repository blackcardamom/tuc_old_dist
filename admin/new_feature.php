<?php session_start();


    // Make sure we're logged in
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }

    // Setup the page
    include_once 'header.php';
    $selected = "features";
    include_once 'topnav.php';
    include_once 'admin.inc.php';
?>

<?php if(!isset($_POST['submit'])): ?>



<div class="form_container">
    <h1>Create new feature card</h1>
    <form action="new_feature.php" method="post">
        <label>Featured recipe</label><br>
        <select name="recipe_id" id="recipe_dropdown">
            <?php
                $query = "SELECT * FROM recipes ORDER BY date_published DESC";
                $stmt = $admin_pdo->prepare($query);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<option value='".$row['id']."' selected>".$row['id']." - ".$row['title']."</option>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='".$row['id']."' selected>".$row['id']." - ".$row['title']."</option>";
                }
            ?>
        </select>

        <label>Feature card image</label><br>
        <input type="text" name="feature_img" id="feature_img_input">
        <button type="submit" name="submit" class="action_button" style="position: absolute;left:50%;transform: translateX(-50%);">Submit</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$('#feature_img_input').val("recipes/"+$('#recipe_dropdown').val().toString()+"/");
$('#recipe_dropdown').on('change', function(e) {
    console.log("Hello?");
    $('#feature_img_input').val("recipes/"+$('#recipe_dropdown').val().toString()+"/");
});
</script>

<?php else: ?>

<?php
// This code should actually add the new recipe, we assume it goes straigh to the front
$query = "SELECT MIN(position) FROM feature_cards";
$stmt = $admin_pdo->prepare($query);
$stmt->execute();
$new_pos = $stmt->fetch(PDO::FETCH_ASSOC)['MIN(position)'] - 1;

$query = "INSERT INTO feature_cards(position, recipe_id, feature_img) VALUES (:pos, :id, :img)";
$stmt = $admin_pdo->prepare($query);
$stmt->bindValue('pos',$new_pos);
$stmt->bindValue('id',$_POST['recipe_id']);
$stmt->bindValue('img',$_POST['feature_img']);
$stmt->execute();

?>

<div class="message_wrapper">
    <p>You have successful posted a new feature card</p>
    <a href="features.php"><button class="action_button">Return</button></a>
</div>


<?php endif; ?>

<?php include_once 'footer.php'; ?>
