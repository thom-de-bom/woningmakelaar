<?php
include_once "./assets/php/config.php";
include_once "./assets/php/login.php";
// Fetch a random listing
$randomListingQuery = "SELECT * FROM listings ORDER BY RAND() LIMIT 1";
$randomListingStmt = $pdo->query($randomListingQuery);
$randomListing = $randomListingStmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    
    <style>


        html,body{
            overflow: hidden;
            background-color: #D9D9D9;
        }
        p,h1,h2,h3,a{
            font-family: 'Heebo', sans-serif;
        }
        p, a{
            font-size: 16px;
        }

.overons {
    background-color: #FFF;
    border: 2px solid black;
    display: flex;
    padding: 40px;  /* Increased from 20px to 40px for more space */
    max-width: 900px;
    margin: 20px auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    margin-top: 2vh;
}


.overons-logo {
    width: 22.5%; /* Adjusted to be 10% smaller than before */
    max-height: 100%; 
    margin-right: 20px;
}

.overons-content {
    display: flex;
    flex-direction: column;
    width: 77.5%; /* Adjusted to ensure the total width is 100% */
}

.overons-content p.title {
    margin: 0 0 10px 0;
    font-size: 1.2em; 
    text-align: center; 
}

.overons-content p {
    margin: 0;
    font-size: 0.85em;
    line-height: 1.4;
}


.contact-div {
    text-align: center;
    padding: 20px;
    background-color: #FFF;
    border: 2px solid black;
    width: 70%; /* Adjust to your preferred width */
    max-width: 800px; /* Limit the width, can be adjusted as per requirements */
    margin: 50px auto; /* Centers the form container horizontally and adds top and bottom margin */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional: adds a subtle shadow */
    display: flex;
    flex-direction: column;
    align-items: center;
}

.contact-div p{
    margin-top: 1px;
}

.input-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.input-row input, 
textarea, 
.contact-btn {
    border: 2px solid black;
    padding: 10px;
    background: transparent;
    border-radius: 0; /* To ensure sharp rectangular corners */
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.input-row input {
    flex: 1; /* To ensure equal distribution of space */
    margin-right: 10px; /* Space between inputs */
}

.input-row input:last-child {
    margin-right: 0;
}

textarea {
    width: 100%;
    min-height: 100px; /* You can adjust this as per your requirements */
    margin-bottom: 20px;
}

.contact-btn {
    cursor: pointer;
    padding: 10px 20px; /* Adjust padding for the button */
}

.success-notification {
    background-color: #4CAF50; 
    padding: 10px;
    color: white;
    border-radius: 3px;
    text-align: center;
    margin-bottom: 20px;
}

.error-notification {
    background-color: #f44336; 
    padding: 10px;
    color: white;
    border-radius: 3px;
    text-align: center;
    margin-bottom: 20px;
}

main {
    display: flex;
    justify-content: center;
    align-items: center;
    height: calc(100vh - /* header height */ 60px - /* footer height */ 60px); 
    overflow-y: hidden;
    overflow-x: hidden;
    flex-direction: column;
    margin-top: 60px; /* Same as header height */
    margin-bottom: 60px; /* Same as footer height */
    padding: 20px; /* Adjust as needed */
}

.overons {
    margin-top: 0; /* Resetting margin-top to 0 as we are centering it using flexbox */
    background-color: #FFF;
    border: 2px solid black;
    display: flex;
    padding: 40px;  /* Increased from 20px to 40px for more space */
    max-width: 900px;
    margin: 20px auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
}


.overons-logo {
    width: 185px; /* Setting fixed width */
    height: 90px; /* Setting fixed height */
    padding-right: 15px;
    
}

.overons-content {
    display: flex;
    flex-direction: column;
    width: 77.5%; /* Adjusted to ensure the total width is 100% */
}

.overons-content p.title {
    margin: 0 0 10px 0;
    font-size: 1.2em; 
    text-align: center; 
}

.overons-content p {
    color: #333; /* Making text a bit darker */
    text-shadow: 1px 1px 2px rgba(255,255,255,0.5); /* Adding subtle shadow for better readability */
    margin: 0;
    font-size: 0.85em;
    line-height: 1.4;
    text-align: left;
    text-wrap: wrap;
}

.slide section {
    overflow-y: auto; /* Makes section content scrollable if it overflows */
}


section {
    width: 100vw;
    height: 100vh;
}

#home{
    margin-top: 10px;

}
#slider {
    white-space: nowrap;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    width: 100vw;
    height: 100vh;
}

.slide {
    width: 100vw;
    height: 100vh;
    position: absolute;
    top: 0;
    left: 0;
    display: none;
    z-index: 10; /* Ensures that slides are below the modal */
}

 
.slidertog {
    position: fixed;
    top: 50%;
    transform: translateY(-50%);
    padding: 10px 20px;
    cursor: pointer;
    z-index: 1;
}


.slide-active {
    display: block;
    z-index: 10; /* Ensures that the active slide is below the modal */
}

.slide-from-top {
    animation: slideFromTop 0.7s ease forwards;
}

.slide-from-bottom {
    animation: slideFromTop 0.7s ease forwards;
}

.slide-from-left {
    animation: slideFromTop 0.7s ease forwards;
}

.slide-from-right {
    animation: slideFromTop 0.7s ease forwards;
}

.slide-to-top {
    animation: slideToTop 0.7s ease forwards;
}

.slide-to-bottom {
    animation: slideToBottom 0.7s ease forwards;
}

.slide-to-left {
    animation: slideToLeft 0.7s ease forwards;
}


.slide-to-right {
    animation: slideToRight 0.7s ease forwards;
}

@keyframes slideToTop {
    from { transform: translateY(0); }
    to { transform: translateY(-100%); }
}

@keyframes slideToBottom {
    from { transform: translateY(0); }
    to { transform: translateY(100%); }
}

@keyframes slideToLeft {
    from { transform: translateX(0); }
    to { transform: translateX(-100%); }
}


@keyframes slideToRight {
    from { transform: translateX(0); }
    to { transform: translateX(100%); }
}

@keyframes slideFromRight {
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
}
@keyframes slideFromLeft{
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
}
@keyframes slideFromBottom{
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
}
@keyframes slideFromTop {
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
}



.hide-scrollbars::-webkit-scrollbar-thumb,
.hide-scrollbars *::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0); /* Fully transparent */
    border-radius: 10px; /* Optional: for rounded corners */
}

/* For Firefox */
.hide-scrollbars,
.hide-scrollbars * {
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0) transparent; /* Fully transparent scrollbar with transparent background */
}

/* Make sure to hide the scrollbar track as well for all browsers */
.hide-scrollbars::-webkit-scrollbar-track,
.hide-scrollbars *::-webkit-scrollbar-track,
.hide-scrollbars::-webkit-scrollbar-corner,
.hide-scrollbars *::-webkit-scrollbar-corner {
    background: transparent;
}



header,footer{
    z-index: 9999;
}


.filter-button, .pagination {
    z-index: 10; /* higher than the header and footer */
    
}   

.filter-button{
    margin-top: 10vh;
}

.pagination{
    margin-bottom: 8vh;
}
/* woningen.php styling */

.filter-button {
    display: flex;
    background-color: #FFF;
    border: 1px solid black;
    padding: 10px 20px;
    cursor: pointer;
    max-width: 3%;
    align-items: center;
    margin-left: auto;
    margin-right: auto;
    justify-content: center;
    text-decoration: none;
}

.bars {
    margin-left: 10px;
}

.modal {
    display: none;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 1000;  /* This value ensures the modal is above most other elements */
}

.modal-content {
    background-color: #FFF;
    margin: 8% auto;
    padding: 20px;
    width: 50%;
    z-index: 1001;  /* This value ensures the content is above the modal background */
}


.close-btn {
    cursor: pointer;
    float: right;
    font-size: 28px;
}


.filterhouder{
    display: flex;
    flex-direction: row;
    justify-content: center;
    gap: 5%;
}
.details-ligging{
    display: flex;
    flex-direction: column;
    align-items: center;
}
.details-eigenschappen{
    display: flex;
    flex-direction: column;
    align-items: center;
}

#listingsContainer{
    margin-top: 1vh;
}

.overig{
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.page-button {
    background-color: #FFF;
    border: 1px solid black;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.page-button:hover {
    background-color: #f5f5f5;
}

.page-button.dots, .page-button.last {
    width: auto;
    padding: 0 10px;
}
/* contact.php styling */


#contact{
    margin-top: 10vh;
}

.contact-div {
    text-align: center;
    padding: 20px;
    background-color: #FFF;
    border: 2px solid black;
    width: 70%; /* Adjust to your preferred width */
    max-width: 800px; /* Limit the width, can be adjusted as per requirements */
    margin: 50px auto; /* Centers the form container horizontally and adds top and bottom margin */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional: adds a subtle shadow */
    display: flex;
    flex-direction: column;
    align-items: center;
}

.contact-div p{
    margin-top: 1px;
}

.input-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    gap: 20px;
}

.input-row input, 
textarea, 
.contact-btn {
    border: 2px solid black;
    padding: 10px;
    background: transparent;
    border-radius: 0; /* To ensure sharp rectangular corners */
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.input-row input {
    flex: 1; /* To ensure equal distribution of space */
    margin-right: 10px; /* Space between inputs */
}

.input-row input:last-child {
    margin-right: 0;
}

textarea {
    width: 100%;
    min-height: 100px; /* You can adjust this as per your requirements */
    margin-bottom: 20px;
}

.contact-btn {
    cursor: pointer;
    padding: 10px 20px; /* Adjust padding for the button */
}

.success-notification {
    background-color: #4CAF50; 
    padding: 10px;
    color: white;
    border-radius: 3px;
    text-align: center;
    margin-bottom: 20px;
}

.error-notification {
    background-color: #f44336; 
    padding: 10px;
    color: white;
    border-radius: 3px;
    text-align: center;
    margin-bottom: 20px;
}

/* about.php styling */

#about{
    margin-top: 10vh;
}


.overons-about {
    margin-top: 0; /* Resetting margin-top to 0 as we are centering it using flexbox */
    background-color: #FFF;
    border: 2px solid black;
    display: flex;
    padding: 40px;  /* Increased from 20px to 40px for more space */
    max-width: 900px;
    margin: 20px auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
}


.overons-logo {
    width: 185px; /* Setting fixed width */
    height: 90px; /* Setting fixed height */
    padding-right: 15px;
    
}

.overons-content {
    display: flex;
    flex-direction: column;
    width: 77.5%; /* Adjusted to ensure the total width is 100% */
}

.overons-content p.title {
    margin: 0 0 10px 0;
    font-size: 1.2em; 
    text-align: center; 
}

.overons-content p {
    color: #333; /* Making text a bit darker */
    text-shadow: 1px 1px 2px rgba(255,255,255,0.5); /* Adding subtle shadow for better readability */
    margin: 0;
    font-size: 0.85em;
    line-height: 1.4;
    text-align: left;
}

/* listing modal */

.details-modal, #woningenDetailsModal {
    display: none;
    position: fixed;
    z-index: 2000; /* Increased z-index */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}



.details-modal-content {
    background-color: #fefefe;
    margin: 6% auto auto auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    overflow: auto;
    max-height: 70vh;
}

.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}


    </style>
</head>
<body>
<header>
    <div class="container">
    <a id="navHome" href="javascript:void(0);"><img class="logo" src="./assets/img/Vrijwonen_makelaar.png" alt="logo"></a>
        <div class="nav">
    <a id="navWoningen" href="javascript:void(0);">Woningen</a>
    <a id="navContact" href="javascript:void(0);">Contact</a>
    <a id="navAbout" href="javascript:void(0);">About</a>
</div>
        <?php
        include "./assets/php/header.php"
        ?>
    </div>
</header>

<main>


<div id="slider">
        <div class="slide" style="">
        <!-- Section for Home/Index content -->
    <section id="home" style="overflow-y: auto; display:flex; flex-direction:column; justify-content:center;" >
    <div class="overons">
    <img class="overons-logo" src="./assets/img/Vrijwonen_makelaar.png" alt="logo">
    <div class="overons-content">
        <p class="title">over ons</p>
        <p>Bij Vrij Wonen begrijpen we dat een huis meer is dan alleen een plek om te wonen. Het is de plek waar uw dromen en toekomstige herinneringen worden gevormd. Ons toegewijde team van fictieve makelaars staat klaar om u te begeleiden op uw reis naar het vinden van de perfecte woning, zelfs als die woning nog niet bestaat!</p>
    </div>
</div>


<div class="listing">
    <!-- Image section -->
    <div class="listing-images">
        <img class="main-image" src="./assets/php/<?php echo $randomListing['cover_image_path']; ?>" alt="Main House Image">
        <div class="more-images">
            <?php
            // Assuming extra images are stored as comma-separated values
            $extraImages = explode(',', $randomListing['extra_images']);
            foreach ($extraImages as $image):
                if (!empty($image)):
            ?>
                <img src="./assets/php/<?php echo $image; ?>" alt="House Image">
            <?php
                endif;
            endforeach;
            ?>
        </div>
    </div>

    <!-- Text section -->
    <div class="listing-info">
        <h2><?php echo $randomListing['address']; ?></h2>
        <p><?php echo $randomListing['beschrijving']; ?></p>
        <div class="price-info">
            <div class="listing-price">€<?php echo $randomListing['prijs']; ?></div>
            <button class="more-info" onclick="openDetailsModal(<?php echo $randomListing['id']; ?>)">Meer Info</button>
        </div>
    </div>
</div>


<!-- Detailed View Modal -->
<div id="detailsModal" class="details-modal">
    <div class="details-modal-content">
        <span class="close-details-modal">&times;</span>
        <div class="details-content">
            <!-- Listing details will be loaded here -->
        </div>
    </div>
</div>



    </section>




        </div>
        <div class="slide" style="">
        <!-- Section for Woningen content -->
        <section id="woningen" style="overflow-y: auto; display:flex; flex-direction:column; justify-content:bottom;">
            <?php include './woningen.php'; ?>
        </section>
        </div>
        <div class="slide" style="">
        <!-- Section for Contact content -->
        <section id="contact">
            <?php include './contact.php'; ?>
        </section>
        </div>
        <div class="slide" style="">
        <!-- Section for About content -->
        <section id="about">
            <?php include './about.php'; ?>
        </section>
        </div>
    </div>

    

</main>

<footer>
    <img class="logo-footer" src="./assets/img/Vrijwonen_makelaar.png" alt="logo">
    <div class="address">
        <p>Disketteweg 2</p>
        <p>3815 AV Amersfoort</p>
    </div>
    <div class="contact">
        <p>info@vrijwonen.nl</p>
        <p>033-1122334</p>
    </div>
</footer>

<!-- Login Modal -->
<div id="loginModal" class="login-modal">
    <div class="login-modal-content">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <!-- If the user is logged in, show the admin and logout buttons -->
            <a href="./assets/php/admin.php" class="login-action">Go to Admin</a>
            <a href="./assets/php/logout.php" class="login-action">Logout</a>
        <?php else: ?>
            <!-- If the user is not logged in, show the login form -->
            <form method="post" action="./assets/php/login.php">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username">
                    <input type="password" name="password" placeholder="Password">
                </div>
                <button type="submit" class="login-action">Login</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="./assets/js/inlogmodal.js"></script>
<script src="./assets/js/slider.js"></script>
</body>
</html>
<script src="./assets/js/detailsmodal.js"></script>