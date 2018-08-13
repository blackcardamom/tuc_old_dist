<?php $selected="contact"; $titleSuffix=" - Contact";  include_once 'header.php'; ?>

<?php
    // Make sure that the user actually wants to send a message

    $resp = Array();
    $fieldTypes = Array(
        "name" => "",
        "address" => "",
        "subject" => "",
        "msg" => "");
    $name = "";
    $address = "";
    $subject = "";
    $msg = "";

    if(isset($_POST['submit'])) {
        $fieldTypes = Array(
            "name" => " goodResponse",
            "address" => " goodResponse",
            "subject" => " goodResponse",
            "msg" => " goodResponse");

        $name = $_POST['name'];
        $address = $_POST['address'];
        $subject = $_POST['subject'];
        $msg = $_POST['msg'];

        if (empty($_POST['name'])) {
            array_push($resp, "missing_field");
            $fieldTypes['name'] = 'badResponse';
        }
        if(empty($_POST['address'])){
            array_push($resp, "missing_field");
            $fieldTypes['address'] = 'badResponse';
        }
        if(empty($_POST['subject'])){
            array_push($resp, "missing_field");
            $fieldTypes['subject'] = 'badResponse';
        }
        if(empty($_POST['msg'])){
            array_push($resp, "missing_field");
            $fieldTypes['msg'] = 'badResponse';
        }
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            array_push($resp, "invalid_email");
            $fieldTypes['address'] = 'badResponse';
        }
        if (empty($resp)) {
            $mailTo = "theuglycroissant@gmail.com";
            $headers = "From: $address";
            $text = "$name submitted the following message through the online contact form:\n\n$msg";
            mail($mailTo, "Contact Form: ".$subject, $text, $headers);
            array_push($resp,"success");
            $_POST['msg_sent'] = 1;
        }
    }
?>

<div class="contact_container">
    <h1>Contact Info</h1>
    <p>
        <strong>E-mail: </strong><a href="mailto:theuglycroissant@gmail.com">theuglycroissant@gmail.com</a> or use the form below.<br><br>
        <strong>Social Media: </strong><a href="https://www.instagram.com/theuglycroissant/" style="color:white;text-decoration:none;"><i class="fab fa-instagram" style="font-size:1.5em;position:relative;top:0.125em;"></i> @theuglycroissant</a>
    </p>
    <h1>Contact Form</h1>
    <?php
        if(in_array("success",$resp)) {
            echo "<p>Message successfuly sent, we'll be in touch soon.</p>";
        } else {
            echo "<p>All fields are required.</p>";
        }
    ?>
    <p style="color: var(--badResponse-red);">
    <?php
        if(in_array("missing_field",$resp)) {
            echo "Some fields were missing, please try again. ";
        }
        if(in_array("invalid_email",$resp)) {
            echo "You entered an invalid email, please try again.";
        }
    ?> </p>

    <form method="post" action="contact.php">
        <label for="name">Name</label>
        <input type="text" name="name" placeholder="Your name..." value="<?= $name ?>" class = "<?= $fieldTypes['name'] ?>">

        <label for="address">E-mail Address</label>
        <input type="text" name="address" placeholder="Your e-mail address..." value="<?= $address ?>" class = "<?= $fieldTypes['address'] ?>">

        <label for="subject">Subject</label>
        <input type="text" name="subject" placeholder="Subject..." value="<?= $subject ?>" class = "<?= $fieldTypes['subject'] ?>">

        <label for="msg">Message</label>
        <textarea name="msg" placeholder="Write something.." style="height:200px" class = "<?= $fieldTypes['msg'] ?>"><?= $msg ?></textarea>

        <input type="submit" name="submit" value="Submit">


    </form>
</div>

<?php include_once 'footer.php'; ?>
