<?php session_start();


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
                    header("Location: index.php");
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
                // Submit new post
                $stmt->execute();
                // Return to original page for success message and link to new post
                $successfulPost = true;
            }

            // Now we need to add the tags to the database
            if(is_array($tags)) {
                // First we add any new tags
                if(!empty($tags[0])) {
                    if(is_array($tags[0])) {
                        foreach($tags[0] as $newTag) {
                            $sql = "INSERT INTO tags(name) VALUES (:newTag)";
                            $stmt = $admin_pdo->prepare($sql);
                            $stmt->bindValue('newTag',$newTag);
                            $stmt->execute();
                            $sql = "SELECT LAST_INSERT_ID()";
                            $stmt = $admin_pdo->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            $newIndex = $result['LAST_INSERT_ID()'];
                            $tags[$newIndex] = $newTag;
                        }
                    }
                }

                // Now we need to add the tagmaps to the database
                foreach($tags as $key => $tag) {
                    if($key!==0 && $tag !== null) {
                        $sql = "INSERT INTO ".$_GET['type']."s_tagmap(".$_GET['type']."_id,tag_id) VALUES (:post_id,:tag_id)";
                        $stmt = $admin_pdo->prepare($sql);
                        $stmt->bindValue('post_id',$nextID);
                        $stmt->bindValue('tag_id',$key);
                        $stmt->execute();
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
    <p>You have successful posted a <?= $_GET['type'] ?> with ID <?= $nextID ?></p>
    <p>View it <a href="<?= $website_root."/".$_GET['type'] ?>_view.php?id=<?= $nextID ?>">here</a>.</p>
</div>
<?php else: ?>

    <?php
        // This code is common to all forms
        $sql = "SELECT `AUTO_INCREMENT`
                FROM  INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = :dbName
                AND   TABLE_NAME   = :tableName;";

        $stmt = $admin_pdo->prepare($sql);
        $stmt->bindValue(':dbName', $dbName);
        $stmt->bindValue(':tableName', $_GET['type']."s");
        $stmt->execute();
        $nextID = $stmt->fetch(PDO::FETCH_ASSOC)['AUTO_INCREMENT'];
    ?>
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

    // We create an array with the first position containing an array of new tags to add
    // Subsequent positions correpond to the IDs of existing tags
    // We set them true if we wish to add them otherwise we leave them null
    function addTag(id,tag) {
        // Retrieve the array
        var tags = JSON.parse(document.getElementById("tags").value);
        // Make sure it is an array
        if(!Array.isArray(tags)) {
            tags = [];
        }
        if (id == 0) {
            // We are adding a new tag
            // Make sure tags[0] is an array
            if(!Array.isArray(tags[0])) {
                tags[0] = [];
            }
            if(!tags[0].includes(tag)) {
                tags[0].push(tag);
                document.getElementById("tags_display").innerHTML += "<li id='tag_"+tag+"'>"+tag+" <a onclick='removeTag(0,"+'"'+tag+'"'+")'><i class='fas fa-times-circle'></i></a></li>";
            }
        } else {
            console.log(id+":"+tag);
            // We are adding an existing tag
            if(tags[id] == null) {
                tags[id] = tag;
                document.getElementById("tags_display").innerHTML += "<li id='tag_"+id+"'>"+tag+" <a onclick='removeTag("+id+","+'"'+tag+'"'+")'><i class='fas fa-times-circle'></i></a></li>";
            }
        }
        document.getElementById("tags").value = JSON.stringify(tags);
    }

    // Taken from https://www.abeautifulsite.net/adding-and-removing-elements-on-the-fly-using-javascript
    function removeElement(elementId) {
        // Removes an element from the document
        var element = document.getElementById(elementId);
        element.parentNode.removeChild(element);
    }

    function removeTag(id,tag) {
        var tags = JSON.parse(document.getElementById("tags").value);
        if (id == 0) {
            // We are removing a new tag
            var index = tags[0].indexOf(tag);
            if (index > -1) {
              tags[0].splice(index, 1);
            }
            removeElement("tag_"+tag);
        } else {
            // We are remvoing an existing tag
            tags[id]= null;
            removeElement("tag_"+id);
        }
        document.getElementById("tags").value = JSON.stringify(tags);
    }
    </script>


<?php if($_GET['type'] === "recipe"): ?>

    <div class="form_container">
        <h1>Create new recipe (with ID <?= $nextID ?>)</h1><br>
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
        <form action="new_post.php?type=recipe" method="post">
            <input type="hidden" name="nextID" value="<?= (string)$nextID?>">

            <label>Recipe Title</label><br>
            <input type="text" name="title" placeholder="Lemon Meringue Pie"<?= empty($_POST['title']) ? "" : " value = '".$_POST['title']."'"?>><br>

            <label>Time spent cooking</label><br>
            <input type="text" name="recipe_active_time" placeholder="2 hours 30 minutes"<?= empty($_POST['recipe_active_time']) ? "" : " value = '".$_POST['recipe_active_time']."'"?>><br>

            <label>Time spent waiting</label><br>
            <input type="text" name="recipe_wait_time" placeholder="3 hours + overnight"<?= empty($_POST['recipe_wait_time']) ? "" : " value = '".$_POST['recipe_wait_time']."'"?>><br>

            <label>Recipe servings</label><br>
            <input type="text" name="recipe_serves" placeholder="A hungry Tom"<?= empty($_POST['recipe_serves']) ? "" : " value = '".$_POST['recipe_serves']."'"?>><br>

            <label>Intro Markdown Editor</label><br>
            <textarea id="intro_mde"><?= empty($_POST['intro_md']) ? "" : $_POST['intro_md']?></textarea>
            <input type="hidden" name="intro_md" id="intro_md_input">

            <label>Ingredients Markdown Editor</label><br>
            <textarea id="ingredients_mde"><?= empty($_POST['ingredients_md']) ? "" : $_POST['ingredients_md']?></textarea>
            <input type="hidden" name="ingredients_md" id="ingredients_md_input">

            <label>Method Markdown Editor</label><br>
            <textarea id="method_mde"><?= empty($_POST['method_md']) ? "" : $_POST['method_md']?></textarea>
            <input type="hidden" name="method_md" id="method_md_input">

            <label>Path to intro image</label><br>
            <input type="text" name="intro_img" value="<?= empty($_POST["intro_img"]) ? "recipes/".$nextID."/" : $_POST["intro_img"] ?>"><br>

            <label>Path to card image</label><br>
            <input type="text" name="card_img" value="<?= empty($_POST["card_img"]) ? "recipes/".$nextID."/" : $_POST["card_img"] ?>"><br>

            <label>Path to printable pdf</label><br>
            <input type="text" name="print_pdf" value="<?= empty($_POST["print_pdf"]) ? "recipes/".$nextID."/" : $_POST["print_pdf"] ?>"><br>

            <label>Tags</label>
            <input type="hidden" name="tags" id="tags" value='<?= empty($_POST['tags']) ? "[]" : $_POST['tags'] ?>'>
            <div class="new_post_active_tags">
                <ul id="tags_display">
                    <?php
                    // We need to add the li elements based on $_POST['tags']
                        if(!empty($_POST['tags'])) {
                            $tags = json_decode($_POST['tags']);
                            if(!empty($tags[0])) {
                                foreach($tags[0] as $tag) {
                                    echo "<li id='tag_".$tag."'>".$tag." <a onclick='removeTag(0,".'"'.$tag.'"'.")'><i class='fas fa-times-circle'></i></a></li>";
                                }
                            }
                            foreach($tags as $id => $tag) {
                                if($id !== 0 && $tag !== null) {
                                    echo "<li id='tag_".$id."'>".$tag." <a onclick='removeTag(".$id.",".'"'.$tag.'"'.")'><i class='fas fa-times-circle'></i></a></li>";
                                }
                            }
                        }
                    ?>
                </ul>
            </div>
            <input type="text" onkeyup="showResult(this.value)" name="tag_search_bar" class="search_bar" placeholder="Search for tags..." autocomplete="off">
            <div id="livesearch" class="empty_search_results"></div>

            <label>Username</label> &nbsp;&nbsp;
            <input type="text" class="uid" name="uid"<?= empty($_POST['uid']) ? "" : " value = '".$_POST['uid']."'"?>> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">

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
        <h1>Create new blogpost (with ID <?= $nextID ?>)</h1><br>
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
        <form action="new_post.php?type=blogpost" method="post">
            <input type="hidden" name="nextID" value="<?= (string)$nextID?>">

            <label>Blog Title</label><br>
            <input type="text" name="title" placeholder="Lemon Meringue Pie"<?= empty($_POST['title']) ? "" : " value = '".$_POST['title']."'"?>><br>

            <label>Intro Text</label><br>
            <textarea name="intro"><?= empty($_POST['intro']) ? "" : $_POST['intro']?></textarea>

            <label>Content Markdown Editor</label><br>
            <textarea id="content_mde"><?= empty($_POST['content_md']) ? "" : $_POST['content_md']?></textarea>
            <input type="hidden" name="content_md" id="content_md_input">

            <label>Tags</label>
            <input type="hidden" name="tags" id="tags" value='<?= empty($_POST['tags']) ? "[]" : $_POST['tags'] ?>'>
            <div class="new_post_active_tags">
                <ul id="tags_display">
                    <?php
                    // We need to add the li elements based on $_POST['tags']
                        if(!empty($_POST['tags'])) {
                            $tags = json_decode($_POST['tags']);
                            if(!empty($tags[0])) {
                                foreach($tags[0] as $tag) {
                                    echo "<li id='tag_".$tag."'>".$tag." <a onclick='removeTag(0,".'"'.$tag.'"'.")'><i class='fas fa-times-circle'></i></a></li>";
                                 }
                            }
                            foreach($tags as $id => $tag) {
                                if($id !== 0 && $tag !== null) {
                                    echo "<li id='tag_".$id."'>".$tag." <a onclick='removeTag(".$id.",".'"'.$tag.'"'.")'><i class='fas fa-times-circle'></i></a></li>";
                                }
                            }
                        }
                    ?>
                </ul>
            </div>
            <input type="text" onkeyup="showResult(this.value)" name="tag_search_bar" class="search_bar" placeholder="Search for tags..." autocomplete="off">
            <div id="livesearch" class="empty_search_results"></div>

            <label>Username</label> &nbsp;&nbsp;
            <input type="text" class="uid" name="uid"<?= empty($_POST['uid']) ? "" : " value = '".$_POST['uid']."'"?>> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">

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
