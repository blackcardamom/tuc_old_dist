<?php
    $selected="contact";
    $titleSuffix=" - Contact";
    include_once 'header.php';
    include_once 'includes/base_assumptions.inc.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'includes/PHPMailer/src/Exception.php';
    require 'includes/PHPMailer/src/PHPMailer.php';
    require 'includes/PHPMailer/src/SMTP.php';
?>

<?php
    // Initialise all variables we plan to use

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

    // If the user hasn't clicked submit we don't want to run any code
    if(isset($_POST['submit'])) {
        // Assume inputs are good
        $fieldTypes = Array(
            "name" => " goodResponse",
            "address" => " goodResponse",
            "subject" => " goodResponse",
            "msg" => " goodResponse");

        // Sanitize inputs
        $name = htmlspecialchars($_POST['name']);
        $address = filter_var($_POST['address'], FILTER_SANITIZE_EMAIL);
        $subject = htmlspecialchars($_POST['subject']);
        $msg = htmlspecialchars($_POST['msg']);

        // Check for missing fields
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

        // Validate email adress
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            array_push($resp, "invalid_email");
            $fieldTypes['address'] = 'badResponse';
        }

        // If there isn't an error so far send the email
        if (empty($resp)) {
            $mailTo = "theuglycroissant@gmail.com";
            $headers = "From: $address";
            $text = "$name submitted the following message through the online contact form:\n\n$msg";

            $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
            try {
                //Server settings                        // Enable verbose debug output
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = 'mail.theuglycroissant.com';            // Specify main SMTP server
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = 'contact_form@theuglycroissant.com';// SMTP username
                $creds = parse_ini_file("/home/vwmnpccl/etc/creds.ini");       // Get the creds for the email server
                $mail->Password = $creds['contactPwd'];                           // SMTP password
                $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 465;                                    // TCP port to connect to

                //Recipients
                $mail->setFrom($address, $name);
                $mail->addAddress('admin@theuglycroissant.com', 'The Ugly Croissant');     // Add a recipient
                $mail->addReplyTo($address, $name);

                //Content
                $mail->isHTML(false);
                $mail->Subject = $subject;
                $mail->Body    = "$name submitted the following message through the online contact form:\n\n$msg";

                $mail->send();
                array_push($resp,"success");

            } catch (Exception $e) {
                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
            }
        }
    }
?>

<div class="contact_container">
    <h1>Contact Info</h1>
    <p>
        <strong>E-mail: </strong><a href="mailto:admin@theuglycroissant.com">admin@theuglycroissant.com</a> or use the form below.<br><br>
        <strong>Social Media: </strong><span class="no_break"><a href="https://www.instagram.com/theuglycroissant/" style="color:white;text-decoration:none;"><i class="fab fa-instagram" style="font-size:1.5em;position:relative;top:0.125em;"></i> @theuglycroissant</a></span>
                                         &nbsp;  &nbsp; <span class="no_break"><a href="https://www.facebook.com/theuglycroissant" style="color:white;text-decoration:none;"><i class="fab fa-facebook" style="font-size:1.5em;position:relative;top:0.125em;"></i> The Ugly Croissant</a></span>
    </p>
    <h1>Contact Form</h1>
    <?php
        // Either display opening message or success message
        if(in_array("success",$resp)) {
            echo "<p>Message successfuly sent, we'll be in touch soon.</p>";
        } else {
            echo "<p>All fields are required.</p>";
        }
    ?>
    <p style="color: var(--badResponse-red);">
    <?php
        // Display any errors in data
        if(in_array("missing_field",$resp)) {
            echo "Some fields were missing, please try again. ";
        }
        if(in_array("invalid_email",$resp)) {
            echo "You entered an invalid email, please try again.";
        }
    ?> </p>

    <!-- To avoid losing previous input we put back old input
         We also change the input class depending on whether the input was deemed acceptable -->
    <form method="post" action="<?= $website_root ?>/contact.php">
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
