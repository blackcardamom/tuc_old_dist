<?php
    // Start the session
    session_start();

    // Make sure we're logged in
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }

    // Make sure we have been passed an acceptable type
    $acceptableTypes = Array("recipe","blogpost");
    if(!in_array($_GET['type'],$acceptableTypes)) {
        // We have been attacked so we don't give them any error message
        session_destroy();
        header("Location: index.php");
        exit;
    }
    // We can now trust the type

    // Whatever happens we need a connection the database
    include_once 'admin.inc.php';

    // Here we handle with attempting to upload the recipe
    // If we succeed we set successfulPost to true and display the success screen
    // Else we pass anything we received in POST back into the form and display an error message
    $successfulPost = false;
    $postErrors = Array();

    // Make sure we've actually pressed submit
    if(isset($_POST['submit'])){
        // Get the includes we need to handle the posting
        include_once '../includes/admin_validate.inc.php';
        include_once '../includes/parsedown/Parsedown.php';
        include_once '../includes/base_assumptions.inc.php';

        echo "creds : ".checkCredentials($_POST['uid'],$_POST['pwd'])." : creds";
        if(empty($_POST['uid']) || empty($_POST['pwd'])) {
            $postErrors[] = "no_creds";
        } elseif(!checkCredentials($_POST['uid'],$_POST['pwd'])) {
            $postErrors[] = "bad_creds";
        } else {
            // To get here we must have provided correct credentials
            // We now begin inputting our post

            // Creating array of data we wish to enter into the new post
            $data = $_POST;
            unset($data['uid']);
            unset($data['pwd']);
            unset($data['submit']);
            $nextID = $data['nextID'];                        // We'll need this for later
            unset($data['nextID']);

            // We now need to proccess the markdown on the page to create the relavent HTML and create the social links
            if($_GET['type'] === "recipe") {
                // Converting markdown to HTML
                $Parsedown = new Parsedown();
                $data['intro_html']=$Parsedown->text($_POST['intro_md']);
                $data['ingredients_html']=$Parsedown->text($_POST['ingredients_md']);
                $data['method_html']=$Parsedown->text($_POST['method_md']);
                // Creating social links
                $newURL_enc = rawurlencode($website_root."/recipe_view?id=".$nextID);
                $data['social_fb'] = "https://www.facebook.com/sharer/sharer.php?u=".$newURL_enc;
                $data['social_twtr']="https://twitter.com/intent/tweet?text=". rawurlencode("Check out this ".$data['title']." on The Ugly Croissant")."&url=" .$newURL_enc;
                $data['social_pnt']="http://pinterest.com/pin/create/link/?url=" . $newURL_enc . "&media=" . rawurlencode($website_root."/".$data['intro_img']);
                $data['social_snoo']="http://www.reddit.com/submit?url=" . $newURL_enc ."&title=" . rawurlencode("Check out this ".$data['title']);
            } elseif($_GET['type'] === "blogpost") {
                // Converting markdown to HTML
                $Parsedown = new Parsedown();
                $data['content_html']=$Parsedown->text($_POST['content_md']);
                // Creating social links
                $newURL_enc = rawurlencode($website_root."/blogpost_view?id=".$nextID);
                $data['social_fb'] = "https://www.facebook.com/sharer/sharer.php?u=".$newURL_enc;
                $data['social_twtr']="https://twitter.com/intent/tweet?text=". rawurlencode("Check out this blogpost - '".$data['title']."', on The Ugly Croissant")."&url=" .$newURL_enc;
                $data['social_pnt']="http://pinterest.com/pin/create/link/?url=" . $newURL_enc . "&media=" . rawurlencode($website_root."/android-chrome-192x192.png");
                $data['social_snoo']="http://www.reddit.com/submit?url=" . $newURL_enc ."&title=" . rawurlencode("Check out this blogpost - '".$data['title']."'");
            } else {
                // We have been attacked so we don't give them any error message
                session_destroy();
                header("Location: index.php");
                exit;
            }

            // Getting list of keys for which we have data
            $keys = array_keys($data);

            // Get list of acceptable keys
            $sql = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema=:dbName AND table_name='".$_GET['type']."s'";
            $stmt = $admin_pdo->prepare($sql);
            $stmt->bindValue(':dbName',$dbName);
            $stmt->execute();
            $acceptableKeys = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Make sure all keys are acceptable
            foreach ($keys as $key) {
                if(!in_array($key, $acceptableKeys)) {
                    // We have been attacked so we don't give them any error message
                    session_destroy();
                    header("Location: index.php&bad_key=".$key."&sql=".$sql);
                    exit;
                }
            }

            // We are now safe to construct our query using the keys in $data
            $sql = "INSERT into ".$_GET['type']."s (";
            foreach($keys as $key) {
                $sql = $sql.$key.", ";
            }
            $sql = substr($sql,0,-2).") VALUES (";
            foreach($keys as $key) {
                $sql = $sql.":". $key .", ";
            }
            $sql = substr($sql,0,-2).")";

            // We now execute the query
            if ( !($stmt = $admin_pdo->prepare($sql)) ) {
                $postErrors[] = "sql_fail";
            } else {
                // Bind Paramaters
                foreach($keys as $key) {
                    $stmt->bindValue(":".$key,$data[$key]);
                }
                // Submit new recipe
                $stmt->execute();
                // Return to original page for success message and link to new recipe
                $successfulPost = true;
            }

        }
    }


// Now we can start rendering the content of the page

// Setup the page
include_once 'header.php';
$selected = $_GET['type']."s";
include_once 'topnav.php';

?>

<?php if($successfulPost): ?>
<p>You have successful posted a <?= $_GET['type'] ?> with ID <?= $nextID ?></p>
<?php elseif($_GET['type'] === "recipe"): ?>

    <?php
        $sql = "SELECT `AUTO_INCREMENT`
                FROM  INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = :dbName
                AND   TABLE_NAME   = 'recipes';";

        $stmt = $admin_pdo->prepare($sql);
        $stmt->bindValue(':dbName', $dbName);
        $stmt->execute();
        $nextID = $stmt->fetch(PDO::FETCH_ASSOC)['AUTO_INCREMENT'];
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
    <div class="form_container">
        <h1>Create new recipe (with ID <?= $nextID ?>)</h1><br>
        <form action="new_post.php?type=recipe" method="post">
            <input type="hidden" name="nextID" value="<?= (string)$nextID?>">
            <strong>Recipe Title</strong><br>
            <input type="text" name="title" placeholder="Lemon Meringue Pie"<?= empty($_POST['title']) ? "" : " value = '".$_POST['title']."'"?>><br>
            <strong>Time spent cooking</strong><br>
            <input type="text" name="recipe_active_time" placeholder="2 hours 30 minutes"<?= empty($_POST['recipe_active_time']) ? "" : " value = '".$_POST['recipe_active_time']."'"?>><br>
            <strong>Time spent waiting</strong><br>
            <input type="text" name="recipe_wait_time" placeholder="3 hours + overnight"<?= empty($_POST['recipe_wait_time']) ? "" : " value = '".$_POST['recipe_wait_time']."'"?>><br>
            <strong>Recipe servings</strong><br>
            <input type="text" name="recipe_serves" placeholder="A hungry Tom"<?= empty($_POST['recipe_serves']) ? "" : " value = '".$_POST['recipe_serves']."'"?>><br>
            <strong>Intro Markdown Editor</strong><br>
            <textarea id="intro_mde"><?= empty($_POST['intro_md']) ? "" : $_POST['intro_md']?></textarea>
            <input type="hidden" name="intro_md" id="intro_md_input">
            <strong>Ingredients Markdown Editor</strong><br>
            <textarea id="ingredients_mde"><?= empty($_POST['ingredients_md']) ? "" : $_POST['ingredients_md']?></textarea>
            <input type="hidden" name="ingredients_md" id="ingredients_md_input">
            <strong>Method Markdown Editor</strong><br>
            <textarea id="method_mde"><?= empty($_POST['method_md']) ? "" : $_POST['method_md']?></textarea>
            <input type="hidden" name="method_md" id="method_md_input">
            <strong>Path to intro image</strong><br>
            <input type="text" name="intro_img" value="<?= empty($_POST["intro_img"]) ? "recipes/".$nextID."/" : $_POST["intro_img"] ?>"><br>
            <strong>Path to card image</strong><br>
            <input type="text" name="card_img" value="<?= empty($_POST["card_img"]) ? "recipes/".$nextID."/" : $_POST["card_img"] ?>"><br>
            <strong>Path to printable pdf</strong><br>
            <input type="text" name="print_pdf" value="<?= empty($_POST["print_pdf"]) ? "recipes/".$nextID."/" : $_POST["print_pdf"] ?>"><br>
            <strong>Username</strong> &nbsp;&nbsp;
            <input type="text" name="uid"<?= empty($_POST['uid']) ? "" : " value = '".$_POST['uid']."'"?>> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">
            <strong>Password</strong> &nbsp;&nbsp;
            <input type="password" name="pwd"> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">
            <input type="submit" name="submit" onclick="onNewRecipeSubmit()">
        </form>
    </div>

    <script>
    var intro_mde = new SimpleMDE({ element: document.getElementById("intro_mde") });
    var ingredients_mde = new SimpleMDE({ element: document.getElementById("ingredients_mde") });
    var method_mde = new SimpleMDE({ element: document.getElementById("method_mde") });

    var intro_md_input = document.getElementById("intro_md_input")
    var ingredients_md_input = document.getElementById("ingredients_md_input")
    var method_md_input = document.getElementById("method_md_input")

    function onNewRecipeSubmit() {
        intro_md_input.value = intro_mde.value();
        ingredients_md_input.value = ingredients_mde.value();
        method_md_input.value = method_mde.value();
    }
    </script>

<?php elseif($_GET['type'] === "blogpost"):?>
    <?php
        $sql = "SELECT `AUTO_INCREMENT`
                FROM  INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = :dbName
                AND   TABLE_NAME   = 'blogposts';";

        $stmt = $admin_pdo->prepare($sql);
        $stmt->bindValue(':dbName', $dbName);
        $stmt->execute();
        $nextID = $stmt->fetch(PDO::FETCH_ASSOC)['AUTO_INCREMENT'];
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
    <div class="form_container">
        <h1>Create new recipe (with ID <?= $nextID ?>)</h1><br>
        <form action="new_post.php?type=blogpost" method="post">
            <input type="hidden" name="nextID" value="<?= (string)$nextID?>">
            <strong>Blog Title</strong><br>
            <input type="text" name="title" placeholder="Lemon Meringue Pie"<?= empty($_POST['title']) ? "" : " value = '".$_POST['title']."'"?>><br>
            <strong>Intro Text</strong><br>
            <textarea name="intro"><?= empty($_POST['intro']) ? "" : $_POST['intro']?></textarea>
            <strong>Content Markdown Editor</strong><br>
            <textarea id="content_mde"><?= empty($_POST['content_md']) ? "" : $_POST['content_md']?></textarea>
            <input type="hidden" name="content_md" id="content_md_input">
            <strong>Username</strong> &nbsp;&nbsp;
            <input type="text" name="uid"<?= empty($_POST['uid']) ? "" : " value = '".$_POST['uid']."'"?>> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">
            <strong>Password</strong> &nbsp;&nbsp;
            <input type="password" name="pwd"> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">
            <input type="submit" name="submit" onclick="onNewBlogSubmit()">
        </form>
    </div>

    <script>
    var content_mde = new SimpleMDE({ element: document.getElementById("content_mde") });
    var content_md_input = document.getElementById("content_md_input")

    function onNewBlogSubmit() {
        content_md_input.value = content_mde.value();
    }
    </script>

<?php else:
    // We have been attacked so we don't give them any error message
    session_destroy();
    header("Location: index.php");
    exit;
?>

<?php endif; ?>
