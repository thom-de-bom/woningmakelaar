<?php
include_once "./assets/php/login.php"
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- <link rel="stylesheet" href="./assets/css/style.css"> -->
    <style>


    </style>
</head>
<body>
<!-- <header>
    <div class="container">
        <a href="index.php"><img class="logo" src="./assets/img/Vrijwonen_makelaar.png" alt="logo"></a>
        <div class="nav">
            <a href="./woningen.php">woningen</a>
            <a href="contact.php">contact</a>
            <a href="./about.php">about</a>
        </div>
        <?php
        //include "./assets/php/header.php"
        ?>
    </div>
</header> -->

<!-- <main> -->
<div id="formWrapper">
    <form action="./assets/php/handle_contact.php" method="post" class="contact-div">
        <p>Contact</p>

        <div class="input-row">
            <input type="email" id="email" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" placeholder="Email" required>  
            <input type="text" id="volledige-naam" name="volledige-naam" placeholder="Volledige Naam" required>
            <input type="tel" id="mobiel" name="mobiel" pattern="^[0-9]*$" placeholder="Mobiel" required>

        </div>

        <textarea id="bericht" name="bericht" rows="5" placeholder="Bericht" style="resize: none;" required></textarea>
        <button type="submit" class="contact-btn">Verstuur</button>
    </form>
</div>
<div id="notification" style="display:none;">Your message has been successfully submitted!</div>



<!-- </main> -->

<?php
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        echo '<p class="success-notification">Your message has been successfully submitted!</p>';
        header("Location: index.php");
        exit();

    } elseif ($_GET['status'] === 'error') {
        echo '<p class="error-notification">There was an error submitting your form. Please try again.</p>';
        header("Location: index.php");
        exit();
    }
}

?>

</script>

<!-- <footer>
    <img class="logo-footer" src="./assets/img/Vrijwonen_makelaar.png" alt="logo">
    <div class="address">
        <p>Disketteweg 2</p>
        <p>3815 AV Amersfoort</p>
    </div>
    <div class="contact">
        <p>info@vrijwonen.nl</p>
        <p>033-1122334</p>
    </div>
</footer> -->
</body>
</html>