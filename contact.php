<?php $selected="contact"; include_once 'header.php'; ?>

<div class="contact_container">
    <h1>Contact Info</h1>
    <p>
        <strong>E-mail: </strong><a href="mailto:theuglycroissant@gmail.com">theuglycroissant@gmail.com</a> or use the form below.<br><br>
        <strong>Social Media: </strong><a href="https://www.instagram.com/theuglycroissant/" style="color:white;text-decoration:none;"><i class="fab fa-instagram" style="font-size:1.5em;position:relative;top:0.125em;"></i> @theuglycroissant</a>
    </p>
    <h1>Contact Form</h1>
    <p>All fields are required.</p>
    <form method="post" action="action_page.php">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" placeholder="Your name...">

        <label for="address">E-mail Address</label>
        <input type="text" id="address" name="address" placeholder="Your e-mail address...">

        <label for="subject">Subject</label>
        <input type="text" id="subject" name="address" placeholder="Subject...">

        <label for="msg">Message</label>
        <textarea id="text" name="msg" placeholder="Write something.." style="height:200px"></textarea>

        <input type="submit" value="Submit">
    </form>
</div>

<?php include_once 'footer.php'; ?>
