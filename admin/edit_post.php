<?php session_start();
    // Todo :
    // * Change the MySQL query to an UPDATE $query
    // * Deal with adding and deleting tags on successful submission

    // We will deal with tags by storing pairs (id, title) of tags in a stack

    // Start the session


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

    // Make sure we have an id and a type
    if(empty($_GET['id']) || empty($_GET['type'])) {
        header("Location: index.php?err=missing_field");
        exit;
    }

    // Whatever happens we need a connection the database
    include_once 'admin.inc.php';

    // Here we handle with attempting to upload the recipe
    // If we succeed we set successfulPost to true and display the success screen
    // Else we pass anything we received in POST back into the form and display an error message
    $successfulPost = false;
    $postErrors = Array();
    $nextID = $_GET['id'];

    // Make sure we've actually pressed submit
    if(isset($_POST['submit'])){
        // Get the includes we need to handle the posting
        include_once '../includes/admin_validate.inc.php';
        include_once '../includes/parsedown/Parsedown.php';
        include_once '../includes/base_assumptions.inc.php';

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
            unset($data['tag_search_bar']);
            $tags = json_decode($data['tags']);               // We'll need this to add tags later
            unset($data['tags']);
            $nextID = $_GET['id'];                        // We'll need this for later

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
                    header("Location: index.php");
                    exit;
                }
            }

            // We are now safe to construct our query using the keys in $data
            $sql = "UPDATE ".$_GET['type']."s  SET";
            foreach($keys as $key) {
                $sql .= $key . " = :" . $key . ", ";
            }
            $sql = substr($sql,0,-2)." WHERE id = :id";

            // We now execute the query
            if ( !($stmt = $admin_pdo->prepare($sql)) ) {
                $postErrors[] = "sql_fail";
            } else {
                // Bind Paramaters
                foreach($keys as $key) {
                    $stmt->bindValue(":".$key,$data[$key]);
                }
                $stmt->bindValue("id",$nextID);
                // Submit new post
                $stmt->execute();
                // Return to original page for success message and link to editted post
                $successfulPost = true;
            }

            // Now we need to update the tags on the database
            if(!empty($tags)) {
                foreach($tags as $tagPair) {
                    if($tagPair[0] == -1) {
                        // We need to delete the tag map with tag_id $tagPair[1]
                        $query = "DELETE FROM ".$_GET['type']."s_tagmap WHERE ".$_GET['type']."_id = :post_id AND tag_id = :tag_id";
                        $stmt = $admin_pdo->prepare($query);
                        $stmt->bindValue('post_id',$_GET['id']);
                        $stmt->bindValue('tag_id', $tagPair[1]);
                        $stmt->execute();
                    } elseif ($tagPair[0] == 0) {
                        // We need to add a new tag and the tagmap

                        // First lets create the new tag
                        $query = "INSERT INTO tags(name) VALUES (:new_tag)";
                        $stmt = $admin_pdo->prepare($query);
                        $stmt->bindValue('new_tag',$tagPair[1]);
                        $stmt->execute();

                        // Now lets figure out what the new tag_id is
                        $sql = "SELECT LAST_INSERT_ID()";
                        $stmt = $admin_pdo->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $new_id = $result['LAST_INSERT_ID()'];

                        // Now let's insert the tagmap
                        $query = "INSERT INTO ".$_GET['type']."s_tagmap (".$_GET['type']."_id, tag_id) VALUES (:post_id, :tag_id)";
                        $stmt = $admin_pdo->prepare($query);
                        $stmt->bindValue('post_id',$_GET['id']);
                        $stmt->bindValue('tag_id', $new_id);
                        $stmt->execute();

                    } else {
                        // We just need to add a new tagmap tag_id $tagPair[0] if such a map does not already exist
                        // Lets first check if the tag exists
                        $query = "SELECT * FROM ".$_GET['type']."s_tagmap WHERE ".$_GET['type']."_id = :post_id AND tag_id = :tag_id";
                        $stmt = $admin_pdo->prepare($query);
                        $stmt->bindValue('post_id',$_GET['id']);
                        $stmt->bindValue('tag_id', $tagPair[0]);
                        $stmt->execute();
                        if(!$stmt->fetch()) {
                            $query = "INSERT INTO ".$_GET['type']."s_tagmap (".$_GET['type']."_id, tag_id) VALUES (:post_id, :tag_id)";
                            $stmt = $admin_pdo->prepare($query);
                            $stmt->bindValue('post_id',$_GET['id']);
                            $stmt->bindValue('tag_id', $tagPair[0]);
                            $stmt->execute();
                        }
                    }
                }
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
<div class="message_wrapper">
    <p>You have successfully edited a <?= $_GET['type'] ?> with ID <?= $nextID ?></p>
    <p>View it <a href="<?= $website_root."/".$_GET['type'] ?>_view.php?id=<?= $nextID ?>">here</a>.</p>
</div>
<?php else: ?>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
    <script>
    // Code for tag live search

    // Adapted from https://www.w3schools.com/php/php_ajax_livesearch.asp
    function showResult(str) {
        var divToUpdate = document.getElementById("livesearch");
        if (str.length==0) {
            divToUpdate.innerHTML="";
            divToUpdate.className="empty_search_results";
            return;
        }
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        } else {  // code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function() {
            if (this.readyState==4 && this.status==200) {
              divToUpdate.innerHTML=this.responseText;
              divToUpdate.className="full_search_results";
            }
        }
        xmlhttp.open("GET","taglivesearch.php?q="+str,true);
        xmlhttp.send();
    }

    // We create a stack of tags stored as pairs (id, name) with id=0 if it is a new tag
    function addTag(id,tag) {
        // Retrieve the array
        var tags = JSON.parse(document.getElementById("tags").value);
        // Make sure it is an array
        if(!Array.isArray(tags)) {
            var tags = [];
        }
        // We need to check to make sure such a tag does not already exist
        for(i= 0; i< tags.length; i++) {
            if(tags[i][0] == id) {
                return;
            }
        }
        tags.push([id, tag]);
        var new_pos = tags.length - 1;
        document.getElementById("tags_display").innerHTML += "<li id='tag_"+tag+"'>"+tag+" <a onclick='removeTag("+'"'+ tag +'"'+")'><i class='fas fa-times-circle'></i></a></li>";
        document.getElementById("tags").value = JSON.stringify(tags);
    }

    // Removes an element from the document
    // Taken from https://www.abeautifulsite.net/adding-and-removing-elements-on-the-fly-using-javascript
    function removeElement(elementId) {
        var element = document.getElementById(elementId);
        element.parentNode.removeChild(element);
    }

    function removeTag(name) {
        var tags = JSON.parse(document.getElementById("tags").value);
        for(i=0 ; i < tags.length; i++) {
            if (tags[i][1] == name) {
                // We set a reminder to ourselves to remove this tag
                // By leaving a pair (-1, tag_id)
                tags[i][1] = tags[i][0];
                tags[i][0] = -1
                removeElement("tag_"+name);
            }
        }
        document.getElementById("tags").value = JSON.stringify(tags);
    }
    </script>

    <?php
        // Here we figure out which data we are supposed to load into the form
        // If data has already been posted then we should use that data
        // Otherwise we should consult the database

        if(isset($_POST['submit'])) {
            // We attempted to edit the post but failed so we should fill the form with $_POST
            $formData = $_POST;
            // The tags are stored in the form data that we received via POST in JSON format
            $tags = json_decode($formData['tags']);
            $tagsJSON = $formData['tags'];
        } else {
            // We have just opened the form so we should populate the form with the database content
            $query = "SELECT * FROM ".$_GET['type']."s WHERE ID = :id";
            $stmt = $admin_pdo->prepare($query);
            $stmt->bindValue('id',$_GET['id']);
            $stmt->execute();
            $formData = $stmt->fetch(PDO::FETCH_ASSOC);

            // We need to fetch the tag information from the database
            $tags = Array();
            $query = "SELECT m.tag_id as tag_id, t.name FROM " . $_GET['type'] . "s_tagmap m, tags t WHERE " . $_GET['type'] . "_id = :post_id AND m.tag_id = t.id";
            $stmt = $admin_pdo->prepare($query);
            $stmt->bindValue('post_id',$_GET['id']);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($tags, array($row['tag_id'], $row['name']) );
            }
            $tagsJSON = json_encode($tags);
        }
    ?>


<?php if($_GET['type'] === "recipe"): ?>

    <div class="form_container">
        <h1>Edit recipe - <?= $formData['title'] ?></h1><br>
        <?php
            if(in_array("no_creds",$postErrors)) {
                echo "<p>Please provide your credentials.</p>";
            }
            if(in_array("bad_creds",$postErrors)) {
                echo "<p>Your credentials were unrecognised.</p>";
            }
            if(in_array("sql_fail",$postErrors)) {
                echo "<p>Something went wrong with the database.</p>";
            }
        ?>
        <form action="edit_post.php?type=recipe&id=<?= $_GET['id'] ?>" method="post">
            <label>Recipe Title</label><br>
            <input type="text" name="title" placeholder="Lemon Meringue Pie"<?= empty($formData['title']) ? "" : " value = '".$formData['title']."'"?>><br>

            <label>Time spent cooking</label><br>
            <input type="text" name="recipe_active_time" placeholder="2 hours 30 minutes"<?= empty($formData['recipe_active_time']) ? "" : " value = '".$formData['recipe_active_time']."'"?>><br>

            <label>Time spent waiting</label><br>
            <input type="text" name="recipe_wait_time" placeholder="3 hours + overnight"<?= empty($formData['recipe_wait_time']) ? "" : " value = '".$formData['recipe_wait_time']."'"?>><br>

            <label>Recipe servings</label><br>
            <input type="text" name="recipe_serves" placeholder="A hungry Tom"<?= empty($formData['recipe_serves']) ? "" : " value = '".$formData['recipe_serves']."'"?>><br>

            <label>Intro Markdown Editor</label><br>
            <textarea id="intro_mde"><?= empty($formData['intro_md']) ? "" : $formData['intro_md']?></textarea>
            <input type="hidden" name="intro_md" id="intro_md_input">

            <label>Ingredients Markdown Editor</label><br>
            <textarea id="ingredients_mde"><?= empty($formData['ingredients_md']) ? "" : $formData['ingredients_md']?></textarea>
            <input type="hidden" name="ingredients_md" id="ingredients_md_input">

            <label>Method Markdown Editor</label><br>
            <textarea id="method_mde"><?= empty($formData['method_md']) ? "" : $formData['method_md']?></textarea>
            <input type="hidden" name="method_md" id="method_md_input">

            <label>Path to intro image</label><br>
            <input type="text" name="intro_img" value="<?= empty($formData["intro_img"]) ? "recipes/".$nextID."/" : $formData["intro_img"] ?>"><br>

            <label>Path to card image</label><br>
            <input type="text" name="card_img" value="<?= empty($formData["card_img"]) ? "recipes/".$nextID."/" : $formData["card_img"] ?>"><br>

            <label>Path to printable pdf</label><br>
            <input type="text" name="print_pdf" value="<?= empty($formData["print_pdf"]) ? "recipes/".$nextID."/" : $formData["print_pdf"] ?>"><br>

            <label>Tags</label>
            <input type="hidden" name="tags" id="tags" value='<?= empty($tagsJSON) ? "[]" : $tagsJSON ?>'>
            <div class="new_post_active_tags">
                <ul id="tags_display">
                    <?php
                        // We need to add the li elements based on $tags
                        if(!empty($tags)) {
                            foreach($tags as $tagPair) {
                                // If the first entry is -1 then this is just a reminder to delete
                                if(!$tagPair[0] !== -1) {
                                    echo "<li id='tag_".$tagPair[1]."'>".$tagPair[1]." <a onclick='removeTag(".'"'.$tagPair[1].'"'.")'><i class='fas fa-times-circle'></i></a></li>";
                                }
                            }
                        }
                    ?>
                </ul>
            </div>
            <input type="text" onkeyup="showResult(this.value)" name="tag_search_bar" class="search_bar" placeholder="Search for tags..." autocomplete="off">
            <div id="livesearch" class="empty_search_results"></div>

            <label>Username</label> &nbsp;&nbsp;
            <input type="text" class="uid" name="uid"> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">

            <label>Password</label> &nbsp;&nbsp;
            <input type="password" name="pwd"> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">
            <button type="submit" name="submit" onclick="onNewRecipeSubmit()" class="action_button">Submit</button>
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

    <div class="form_container">
        <h1>Edit blogpost - <?= $formData['title'] ?></h1><br>
        <?php
            if(in_array("no_creds",$postErrors)) {
                echo "<p>Please provide your credentials.</p>";
            }
            if(in_array("bad_creds",$postErrors)) {
                echo "<p>Your credentials were unrecognised.</p>";
            }
            if(in_array("sql_fail",$postErrors)) {
                echo "<p>Something went wrong with the database.</p>";
            }
        ?>
        <form action="edit_post.php?type=blogpost&id=<?= $_GET['id'] ?>" method="post">
            <label>Blog Title</label><br>
            <input type="text" name="title" placeholder="Lemon Meringue Pie"<?= empty($formData['title']) ? "" : " value = '".$formData['title']."'"?>><br>

            <label>Intro Text</label><br>
            <textarea name="intro"><?= empty($formData['intro']) ? "" : $formData['intro']?></textarea>

            <label>Content Markdown Editor</label><br>
            <textarea id="content_mde"><?= empty($formData['content_md']) ? "" : $formData['content_md']?></textarea>
            <input type="hidden" name="content_md" id="content_md_input">

            <label>Tags</label>
            <input type="hidden" name="tags" id="tags" value='<?= empty($tagsJSON) ? "[]" : $tagsJSON ?>'>
            <div class="new_post_active_tags">
                <ul id="tags_display">
                    <?php
                    // We need to add the li elements based on $tags
                    if(!empty($tags)) {
                        foreach($tags as $tagPair) {
                            // If the first entry is -1 then this is just a reminder to delete
                            if(!$tagPair[0] !== -1) {
                                echo "<li id='tag_".$tagPair[1]."'>".$tagPair[1]." <a onclick='removeTag(".'"'.$tagPair[1].'"'.")'><i class='fas fa-times-circle'></i></a></li>";
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
            <input type="text" onkeyup="showResult(this.value)" name="tag_search_bar" class="search_bar" placeholder="Search for tags..." autocomplete="off">
            <div id="livesearch" class="empty_search_results"></div>

            <label>Username</label>
            <input type="text" class="uid" name="uid"<?= empty($formData['uid']) ? "" : " value = '".$formData['uid']."'"?>> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">

            <label>Password</label> &nbsp;&nbsp;
            <input type="password" name="pwd"> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">
            <button type="submit" name="submit" onclick="onNewBlogSubmit()" class="action_button">Submit</button>
        </form>
    </div>


    <script>
        var content_mde = new SimpleMDE({ element: document.getElementById("content_mde") });
        var content_md_input = document.getElementById("content_md_input");

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

<?php endif; endif;?>
<?php include_once 'footer.php'; ?>
