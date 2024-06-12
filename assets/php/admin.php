<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include "remove_listing.php";
include "config.php";
session_start();
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header('Location: login.php');
    exit;
}

// Only allow moderators or admins
if($_SESSION['role'] !== 'moderator' && $_SESSION['role'] !== 'admin'){
    echo "Access denied!";
    exit;
}

// Query to fetch listings in descending order by their ID (assuming ID is an auto-increment field)
$query = "SELECT * FROM listings ORDER BY id DESC";
$listings = $pdo->query($query);


// Fetch all filters
$allFiltersQuery = "SELECT * FROM filters";
$allFiltersResult = $pdo->query($allFiltersQuery);
$allFilters = $allFiltersResult->fetchAll(PDO::FETCH_ASSOC);

// Categorize filters
$liggingFilters = [];
$eigenschappenFilters = [];

foreach ($allFilters as $filter) {
    if ($filter['option'] == 'ligging') {
        $liggingFilters[] = $filter;
    } elseif ($filter['option'] == 'eigenschappen') {
        $eigenschappenFilters[] = $filter;
    }
}

// listings logic
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$perPage = 3;
$offset = ($currentPage - 1) * $perPage;

// Query to fetch listings
$sql = "SELECT * FROM listings LIMIT :perPage OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query to get total number of listings
$totalSql = "SELECT COUNT(*) FROM listings";
$totalListings = $pdo->query($totalSql)->fetchColumn();
$totalPages = ceil($totalListings / $perPage);



// Determine the current page number for contact submissions
$contactPage = isset($_GET['contactPage']) ? (int)$_GET['contactPage'] : 1;
$contactPerPage = 9; // Set the number of submissions per page
$contactOffset = ($contactPage - 1) * $contactPerPage;

// Fetch contact submissions with limit and offset for pagination
$contactSql = "SELECT * FROM contact_submissions LIMIT :perPage OFFSET :offset";
$contactStmt = $pdo->prepare($contactSql);
$contactStmt->bindValue(':perPage', $contactPerPage, PDO::PARAM_INT);
$contactStmt->bindValue(':offset', $contactOffset, PDO::PARAM_INT);
$contactStmt->execute();
$contact_submissions = $contactStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the total number of contact submissions
$totalContactSql = "SELECT COUNT(*) FROM contact_submissions";
$totalContactSubmissions = $pdo->query($totalContactSql)->fetchColumn();
$totalContactPages = ceil($totalContactSubmissions / $contactPerPage);


// Check for the query parameters
$userCreated = isset($_GET['userCreated']) && $_GET['userCreated'] === 'true';
$userDeleted = isset($_GET['userDeleted']) && $_GET['userDeleted'] === 'true';
?>

<!-- <script>
// Wait for the DOM to load
document.addEventListener('DOMContentLoaded', function() {
    // Attach event listener to close button
    var closeButton = document.getElementById('closeModal');
    if(closeButton) {
        closeButton.addEventListener('click', function() {
            document.getElementById('contactModal').style.display = 'none';
        });
    }

    // Function to open modal
    window.openModal = function(submissionId) {
        // Find the corresponding submission data
        var submission = document.querySelector('.contact-submission[data-id="' + submissionId + '"]');
        if(submission) {
            // Update modal content with the submission info
            document.getElementById('modalName').textContent = submission.dataset.name;
            document.getElementById('modalEmail').textContent = submission.dataset.email;
            document.getElementById('modalPhone').textContent = submission.dataset.phone;
            document.getElementById('modalIp').textContent = submission.dataset.ip;
            document.getElementById('modalMessage').textContent = submission.dataset.message;
        }

        // Show the modal
        document.getElementById('contactModal').style.display = 'block';
    };

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        var modal = document.getElementById('contactModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
});
function closeTheModal() {
    document.getElementById('contactModal').style.display = 'none';
}

</script> -->



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/inlogmodal.js"></script>
    <style>

.function-holder{
    position: static;
    display: flex;
    flex-direction: column;
    gap: 7vh;
    margin-top: 5vh;
    margin-left: 10vw;
    align-items: center;
}

.button-holder{
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
    max-width: 200px;
    gap: 15px;
}
.button-holder a{
    text-decoration: none;
}
.button-holder a div{
    text-align: center;
    border: 1px solid black;
    background-color: #FFF;
    width: 200px;
    padding: 10px;
}


/* .button{
    text-align: center;
    border: 1px solid black;
    background-color: #FFF;
    max-width: 200px;
    padding: 10px;
} */

.stat-holder{
align-items: center;
display: flex;
flex-direction: column;
border: 3px solid black;
background-color: #FFF;
max-width: 200px;
gap: 20px;
padding-top: 5px;
padding-bottom: 5px;
padding-inline: 10px;
}
.stat{
    display: flex;
    align-items: center;
    flex-direction: column;
text-align: center;
border: solid black 1px;
background-color: #FFF;
width: 180px;
padding: 3px;
}
.stat div{
    display: flex;
    align-items: center;
    background-color: #FFF;
    border: 1px solid black;
    max-width: 50px;
    max-height: 30px;
    padding: 5px;
    text-align: center;
}
.stat div p{
    text-align: center;
}


.listing {
    background-color: #FFF;
    border: 2px solid black;
    display: flex;
    padding: 20px;
    max-width: 900px;
    margin: 20px auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    gap: 20px;
    width: 93%;
}
.content-holder{
    padding-inline: 5px;
    padding-bottom: 10px;
    height: 90vh;
    margin-top: 50px;
    margin-right: 50px;
    border: 2px solid black;
    display: flex;
    flex-direction: column;
    background-color: #FFF;
    /* overflow: auto; */
    width: 55vw;
}
.everything-holder{
    width: 99vw;
    height: 100vh;
    display: flex;
    flex-direction: row;
    gap: 16vw;
    margin-bottom: 5vh;
}
.station-holder{
    margin-top: 50px;
    display: flex;
    flex-direction: column;
}

.drag-drop-box {
    width: 20vw;
    height: 200px;
    border: 2px solid black;
    background-color: #FFF;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px auto;
    position: relative;
    cursor: pointer;
}
.drag-drop-box p{
    text-align: center;
}

.input-container {
    width: 300px;
    height: 50px;
    border: 2px solid black;
    background-color: #FFF;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px auto;
    padding: 0 10px;
    position: relative;
}

#address,#postcode,#prijs[type="text"] {
    border: none;
    background-color: transparent;
    flex-grow: 2;
    padding-left: 10px;
    outline: none;
}

#address,#postcode,#prijs {
    flex-basis: 80px;
}
#beschrijving{
    width: 30vw;
    height: 20vh;
    border: 2px solid black;
    background-color: #FFF;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px auto;
    position: relative;
}

.filterhouder{
    width: 50vw;
    max-height: 43vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10%;
    border: 1px solid black;
    margin-left: auto;
    margin-right: auto;
    overflow: hidden;
}

.filterhouder label,input{
    flex-basis: 30px;
}

.ligging, .eigenschappen {
    display: flex;
    flex-direction: column;
    width: 40%;
    max-height: 200px; /* Adjust the height as needed */
    overflow-y: auto; /* Enables vertical scrolling */
    overflow-x: hidden; /* Hides horizontal scrollbar */
    padding: 10px; /* Optional: for internal spacing */
    margin-bottom: 10px; /* Optional: for external spacing */
    border: 1px solid #000; /* Optional: adds a border */
}


.befituur-houder{
display: block;
}

.flex-bad{
    display: flex;
    flex-direction: row;
    gap: 10%;
    justify-content: center;
}
#verstuur{
    display: flex;
    align-items: center;
    justify-content: center;
    width: 300px;
    height: 50px;
    border: solid black 1px;
    background-color: #FFF;
    margin-left: auto; margin-right:auto;
}


.contact-submissions-list {
    width: 100%;
    padding-top: 10px;
    left: auto;
    right: auto;
}

.contact-submission {
    border: 3px solid black;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    margin-top: 5px;
    margin-left: auto;
    margin-right: auto;
}

.contact-submission span {
    margin-right: 10px; /* Adjust spacing as needed */
}

.contact-submission span, .full-name, .email, .phone-num {
    border: 2px solid black;
    padding: 5px;
    text-align: center;
}


.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.4);
}

.modal-content {
  background-color: #fefefe;
  margin: 10% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 50%; /* You can adjust the width */
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  border-radius: 5px;
}

.modal-header, .modal-footer {
  padding: 2px 16px;
}

.modal-body {
    padding: 2px 16px;
    display: flex;
    flex-direction: row;
    column-gap: 10%;
}




.close {
  /* float: right; */
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.modal-info {
    border: 2px solid black;
    width: 15vw;
    height: 4vh;
    display: flex;
    align-items: center; /* Vertically center the content */
    justify-content: center; /* Horizontally center the content */
    overflow-x: auto;
    overflow-y: hidden;
}


.contact-message {
  padding: 10px;
  height: 150px; /* Adjust as needed */
  overflow-y: auto;
}

#closeModal {
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  /* float: right; */
}

/* Clear floats */
.modal-header::after,
.modal-footer::after {
  content: "";
  display: table;
  clear: both;
}


.contact-message{
    border: 2px solid black;
    display: flex;
    align-items: center;
    margin-right: 5px;
    width: 65%;
    justify-content: center;
    overflow-y: auto;
    
}

.delete-submission{
    background: none;
    color: inherit;
    border: none;
    padding: 0;
    margin: 0;
    font: inherit;
    cursor: pointer;
    outline: inherit;
    border: 2px solid black;
    padding: 5px;
    text-align: center;
}

.message-content {
  padding-left: 20px;
  flex-grow: 1;
}

/* The Close Button */
.close {
  /* float: right; */
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}

#closeModal {
  padding: 4px 15px;
  text-align: center;
  border: 2px solid black;
  cursor: pointer;
}

.modal-footer {
  padding-top: 20px;
  display: flex;
  justify-content: center;

}

.view-details{
    background: none;      /* Removes background styling */
    color: inherit;        /* Inherits color from parent element */
    border: none;          /* Removes border */
    padding: 0;            /* Removes padding */
    margin: 0;             /* Removes margin */
    font: inherit;         /* Inherits font properties from parent */
    cursor: pointer;       /* Keeps the cursor as a pointer to signify it's clickable */
    outline: inherit;      /* Inherits outline properties (or you can set it to 'none' to remove) */
    border: 2px solid black;
    padding: 5px;
    text-align: center;
}

.filter-container {
    display: flex;
    flex-direction: column;
    align-items: center;
  border: 1px solid #000;
  padding: 20px;
  width: 21vw;
}

.filter-container h2 {
  font-size: 16px;
  color: #000;
}

.filter-container label {
  display: block;
  padding-bottom: 2%;
  color: #000;
}

.filter-container input[type="text"] {
  border: 1px solid #000;
  padding: 5px;
  margin-bottom: 10px;
  display: block;
}

#filter-name{
    margin-left: 20px;
}


.radio-buttons label {
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    margin-right: 10px;
}

.radio-buttons input[type="radio"] {
    margin-right: 5px;
}

.filter-container button {
  border: 1px solid #000;
  background-color: #fff;
  padding: 10px 15px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.filter-container button:hover {
  background-color: #f0f0f0;
}

.remove-filter-container {
  border: 1px solid #000;
  padding: 20px;
  width: 300px;
  text-align: center;
}

.remove-filter-container h2 {
  font-size: 16px;
  color: #000;
  padding-bottom: 5px;
  margin-bottom: 20px;
}

.remove-filter-container label {
  display: block;
  margin: 10px 0;
  color: #000;
}

.remove-filter-container input[type="text"] {
  border: 1px solid #000;
  padding: 5px;
  width: 100%;
  box-sizing: border-box;
  margin-bottom: 10px;
}

.remove-filter-container button {
  border: 1px solid #000;
  background-color: #fff;
  padding: 10px 15px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.remove-filter-container button:hover {
  background-color: #f0f0f0;
}

.add-rem{
    display: flex;
    justify-content: center;
    flex-direction: row;
    gap: 2vw;
    margin-top: 2vh;
}

.location-list-container {
  border: 1px solid #000;
  width: 50%; /* You may set this to the width that suits your layout */
  height: 53vh;
  overflow-y: auto;
  overflow-x: auto;
}

.location-list-container h3 {
    text-align: center;
  font-size: 16px;
  color: #000;
  margin-bottom: 10px;
}

.location-list, .features-list {
  width: 100%;
  border-collapse: collapse;
  overflow-y: auto;
  overflow-x: auto;
}

.location-list th, .location-list td, .features-list th, .features-list td {
  border: 1px solid black;
  padding: 5px;
  text-align: left;
  overflow-y: auto;
  overflow-x: auto;
}

tr{
    overflow-x: auto;
}

th {
  background-color: #f2f2f2;
}


.features-list-container {
  border: 1px solid #000;
  width: 50%; /* Adjusted to 50% as per your changes */
  height: 53vh; /* This will take the full height of its parent */
  overflow-y: auto;
  overflow-x: auto;
}

.features-list-container h3 {
  text-align: center;
  font-size: 16px;
  color: #000;
  margin-bottom: 10px;
}


/* Clear float */
.features-list:after {
  content: "";
  display: table;
  clear: both;
}

.page-button.active {
    background-color: #4CAF50; /* Or any color to indicate the active page */
}

.pagination2{
    display: flex;
    justify-content: center;
        margin-top: 2vh;
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
<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.filter-container form');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);
        fetch('save_filter.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); // You can remove this line later
            window.location.href = 'admin.php'; // Redirect to admin page
        })
        .catch(error => console.error('Error:', error));
    });
});
</script> -->
<header>
    <div class="container">
        <a href="../../index.php"><img class="logo" src="../img/Vrijwonen_makelaar.png" alt="logo"></a>
       
        <?php
        include "header.php"
        ?>
    </div>
</header>

<main>

<?php
// ... [Your existing PHP code, database connection, etc.]

// Query to count the total number of listings
$queryListings = "SELECT COUNT(*) as totalListings FROM listings";
$resultListings = $pdo->query($queryListings);
$totalListings = $resultListings->fetch(PDO::FETCH_ASSOC)['totalListings'];

// Query to count the total number of open contact messages
$queryContacts = "SELECT COUNT(*) as totalContacts FROM contact_submissions";
$resultContacts = $pdo->query($queryContacts);
$totalContacts = $resultContacts->fetch(PDO::FETCH_ASSOC)['totalContacts'];
?>

<div class="everything-holder">
    <div class="station-holder">
            <div class="function-holder">
            <div class="button-holder">
                <a href="#"><div class="button-holder geplaatste" data-content="default-content">Geplaatste woningen</div></a>
                <a href="#"><div class="button-holder nieuwe" data-content="new-content">Nieuwe woning plaatsen</div></a>
                <a href="#"><div class="button-holder contact" data-content="newer-content">Contact berichten</div></a>
                <a href="#"><div class="button-holder filters" data-content="newest-content">Filters</div></a>
                <a href="#"><div class="button-holder users" data-content="newestest-content">users</div></a>
            </div>

                <div class="stat-holder">
                    <div class="stat">
                        <p>totaal aantal woningen</p>
                        <div><p><?php echo $totalListings; ?></p></div>
                    </div>
                    <div class="stat">
                        <p>open contact berichten</p>
                        <div><p><?php echo $totalContacts; ?></p></div>
                    </div>
                </div>
            </div>
    </div>
<div class="content-holder">
    <div class="default-content" style="overflow:auto;">
    <?php foreach ($listings as $listing): ?>
        <div class="listing">
            <!-- Image section -->
            <div class="listing-images">
                <img class="main-image" src="<?php echo $listing['cover_image_path']; ?>" alt="Main House Image">
                <div class="more-images">
                    <?php
                    $extraImages = explode(',', $listing['extra_images']);
                    $count = 0;
                    foreach ($extraImages as $image) {
                        if ($count < 3) {
                            echo '<img src="' . $image . '" alt="Extra Image">';
                            $count++;
                        }
                    }
                    ?>
                </div>
            </div>
            <!-- Text section -->
            <div class="listing-info">
                <h2><?php echo $listing['address']; ?></h2>
                <p><?php echo $listing['beschrijving']; ?></p>
                <!-- New container for price and button -->
                <div class="price-info">
                    <div class="listing-price">€<?php echo $listing['prijs']; ?></div>
                    <button class="more-info" data-id="<?= $listing['id']; ?>" onclick="openDetailsModal(<?= $listing['id']; ?>, 'woningen')">Meer Info</button>
 
                </div>
            </div>
        </div>
    <?php endforeach; ?>

        <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="page-button <?php echo $i == $currentPage ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
        </div>

    </div>
        
        <div class="new-content" style="display: none; overflow:auto;">
        <form action="upload_listing.php" method="post" enctype="multipart/form-data"> 
            <div class="dragdrop-holder" style="display: flex; flex-direction: row; gap: 100px;">
                <div class="drag-drop-box" id="coverBox">
                    <p id="coverBoxText">Drop Cover Image Here</p>
                    <input type="file" name="coverImage" id="coverInput" accept="image/*" hidden>
                </div>

                <div class="drag-drop-box" id="extraImagesBox">
                    <p id="extraImagesBoxText">Drop Extra Images Here (Max 5)</p>
                    <input type="file" name="extraImages[]" id="extraImagesInput" accept="image/*" hidden multiple>
                </div>

            </div>
            <div class="input-holder" style="display: flex; flex-direction:row; gap:5px;">

             <div class="input-container">
                 <label for="address">Address:</label>
                 <input type="text" name="address" id="address" placeholder="Enter address">
             </div>

             <div class="input-container">
                 <label for="postcode">Postcode:</label>
                 <input type="text" name="postcode" id="postcode" placeholder="Enter postcode">
             </div>

             <div class="input-container">
                 <label for="prijs">Prijs:</label>
                 <input type="text" name="prijs" id="prijs" placeholder="Enter prijs">
             </div>
        </div>
    <div class="befituur-houder">
            <div class="filterhouder">
          <h3 style="text-align:center;">eigenschappen</h3>
          <div class="flex-bad">
            <div class="ligging">
              <h4 style="text-align: center;">ligging</h4>
              <?php foreach ($liggingFilters as $filter): ?>
                <label>
                  <input type="checkbox" name="ligging[]" value="<?= htmlspecialchars($filter['id']) ?>">
                  <?= htmlspecialchars($filter['name']) ?>
                </label>
              <?php endforeach; ?>
            </div>
            
            <div class="eigenschappen">
              <h4 style="text-align: center;">eigenschappen</h4>
              <?php foreach ($eigenschappenFilters as $filter): ?>
                <label>
                  <input type="checkbox" name="eigenschappen[]" value="<?= htmlspecialchars($filter['id']) ?>">
                  <?= htmlspecialchars($filter['name']) ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        
        <input type="text" id="beschrijving" name="beschrijving" placeholder="Beschrijving">

        <button id="verstuur" type="submit">verstuur</button>

    </div>
        </form>

        </div>

<div class="newer-content" style="display: none;">
               <!-- Contact Submissions List -->
<div class="contact-submissions-list">
    <?php foreach ($contact_submissions as $submission): ?>
        <div class="contact-submission" 
             data-id="<?= $submission['id']; ?>"
             data-name="<?= htmlspecialchars($submission['volledige_naam']); ?>"
             data-email="<?= htmlspecialchars($submission['email']); ?>"
             data-phone="<?= htmlspecialchars($submission['mobiel']); ?>"
             data-ip="<?= htmlspecialchars($submission['ip_address']); ?>" 
             data-message="<?= htmlspecialchars($submission['bericht']); ?>">
            <span class="full-name"><?= htmlspecialchars($submission['volledige_naam']) ?></span>
            <span class="email"><?= htmlspecialchars($submission['email']) ?></span>
            <span class="phone-num"><?= htmlspecialchars($submission['mobiel']) ?></span>
            <button class="delete-submission" onclick="deleteSubmission(<?= $submission['id']; ?>)">Verwijderen</button>
            <button class="view-details" onclick="openModal(<?= $submission['id']; ?>)">Inhoud</button>
        </div>

    <?php endforeach; ?>
    <div class="pagination2">
    <?php for ($i = 1; $i <= $totalContactPages; $i++): ?>
        <a href="?ShowContact=true&contactPage=<?php echo $i; ?>" class="page-button <?php echo $i == $contactPage ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>


</div>


                
            <!-- Modal Structure -->
    <div id="contactModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
        </div>
        <div class="modal-body">
          <div class="contact-info">
                <div class="modal-info">
                  <h4><span  id="modalName"></span></h4>
                </div>
                <div class="modal-info">
                    <h4><span  id="modalEmail"></span></h4>
                </div>
                <div class="modal-info">
                    <h4><span  id="modalPhone"></span></h4>
                </div>
                <div class="modal-info">
                    <h4><span  id="modalIp"></span></h4>
                </div>
                </div>
                <div class="contact-message">
                <p><span id="modalMessage"></span></p>
            </div>
        </div>
        <div class="modal-footer">
        <button class="close" id="closeModal" onclick="closeTheModal()">sluit</button>
        </div>
      </div>
      <!-- end of modal -->
    </div>
    <!-- end of newer content -->
</div>



<div class="newest-content" style="display: none;"> 


<div class="filter-align" style="display: flex; flex-direction:column; gap:5%;">
    
<div class="add-rem">
        <form action="save_filter.php" method="post">
            <div class="filter-container">
            <h2>Filters toevoegen</h2>
            <label for="filter-name">(scheid meerdere opties met een -)</label>
                <div style="display: flex; flex-direction:row; gap:3vw;">
                    <div style="display:flex; flex-direction:column; align-items: center;">
                        <label for="filter-name">filter naam</label>
                        <input type="text" id="filter-name" name="filter-name">
                    </div>
                    <div class="radio-buttons">
                        <label>
                            <input type="radio" name="filter-option" value="ligging" id="option-ligging">
                            ligging
                        </label>
                        <label>
                            <input type="radio" name="filter-option" value="eigenschappen" id="option-eigenschappen">
                            eigenschappen
                        </label>
                    </div>
                </div>
                <button class="toevoegen" type="submit">toevoegen</button>
            </div>
        </form>
        

        <form action="delete_filter.php" method="post">
          <div class="remove-filter-container">
            <h2>filters verwijderen</h2>
            <label for="filter-name-remove">filter naam of ID </label>
            <label for="filter-name-remove">(scheid meerdere opties met een -)</label>
            <input type="text" id="filter-name-remove" name="filter-name-or-id">
            <button type="submit">verwijder</button>
          </div>
        </form>

    </div>

    <div class="loture" style="display:flex; flex-direction:row;gap: 5%; justify-content: center; margin-top: 3vh;">
            <div class="location-list-container">
          <h3>ligging</h3>
          <table class="location-list">
            <tr>
              <th>Filter Naam</th>
              <th>ID</th>
            </tr>
            <?php foreach ($liggingFilters as $filter): ?>
              <tr>
                <td><?= htmlspecialchars($filter['name']); ?></td>
                <td><?= $filter['id']; ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
            
        <div class="features-list-container">
          <h3>eigenschappen</h3>
          <table class="features-list">
            <tr>
              <th>Filter Naam</th>
              <th>ID</th>
            </tr>
            <?php foreach ($eigenschappenFilters as $filter): ?>
              <tr>
                <td><?= htmlspecialchars($filter['name']); ?></td>
                <td><?= $filter['id']; ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
    </div>
</div>

<!-- end newest-content -->
</div>

<div class="newestest-content" style="display: none;"> 

<div class="user-management">
    <div class="makerem">
        <!-- User Creation Form -->
        <form class="user-form">
            <h2>user maken</h2>
            <p>username</p>
            <input type="text" id="username" name="username">
            <p>password</p>
            <input type="password" id="password" name="password">
            <button type="button" id="createUserButton">aanmaken</button>
        </form>
                
        <!-- User Deletion Form -->
        <form class="user-delete">
            <h2>verwijder user</h2>
            <label for="userid">username/id</label>
            <input type="text" id="userid" name="userid">
            <button type="button" id="deleteUserButton">verwijder</button>
        </form>
    </div>
  <!-- User List -->
  <?php
include 'config.php'; // Database connection

// SQL query to fetch user data
$sql = "SELECT username, id FROM users";
$stmt = $pdo->query($sql);

// Check if there are any users
if ($stmt->rowCount() > 0) {
    echo '<table class="user-list">';
    echo '<tr>';
    echo '<th>username</th>';
    echo '<th>ID</th>';
    echo '<th>verwijder</th>';
    echo '</tr>';

    // Loop through the fetched data and populate the table
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . $row['username'] . '</td>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td><button type="button">verwijder</button></td>';
        echo '</tr>';
    }

    echo '</table>';
} else {
    echo 'No users found.';
}
?>

</div>

</div>


    </div>
</div>





</main>

<footer>
    <img class="logo-footer" src="../img/Vrijwonen_makelaar.png" alt="logo">
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
        <span class="close-btn">&times;</span>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <!-- If the user is logged in, show the admin and logout buttons -->
            <a href="./admin.php" class="login-action">Go to Admin</a>
            <a href="./logout.php" class="login-action">Logout</a>
        <?php else: ?>
            <!-- If the user is not logged in, show the login form -->
            <form method="post" action="./login.php">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username">
                    <input type="password" name="password" placeholder="Password">
                </div>
                <button type="submit" class="login-action">Login</button>
            </form>
        <?php endif; ?>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!-- <script>

document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.button-holder .nieuwe').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('.default-content').style.display = 'none';
        document.querySelector('.new-content').style.display = 'block';
        document.querySelector('.newer-content').style.display = 'none';
        document.querySelector('.newest-content').style.display = 'none';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.button-holder .geplaatste').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('.new-content').style.display = 'none';
        document.querySelector('.newer-content').style.display = 'none';
        document.querySelector('.default-content').style.display = 'block';
        document.querySelector('.pagination').style.display = 'flex';
        document.querySelector('.listing').style.display = 'flex';
        document.querySelector('.newest-content').style.display = 'none';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.button-holder .contact').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('.new-content').style.display = 'none';
        document.querySelector('.newer-content').style.display = 'block';
        document.querySelector('.default-content').style.display = 'none';
        document.querySelector('.newest-content').style.display = 'none';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.button-holder .filters').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('.new-content').style.display = 'none';
        document.querySelector('.newer-content').style.display = 'none';
        document.querySelector('.default-content').style.display = 'none';
        document.querySelector('.newest-content').style.display = 'block';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Check if the URL parameter `showFilters` is set
    const urlParams = new URLSearchParams(window.location.search);
    const showFilters = urlParams.get('showFilters');

    if (showFilters === 'true') {
        document.querySelector('.newest-content').style.display = 'block';
        document.querySelector('.default-content').style.display = 'none';
        document.querySelector('.pagination').style.display = 'none';
        document.querySelector('.listing').style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Check if the URL parameter `showFilters` is set
    const urlParams = new URLSearchParams(window.location.search);
    const showFilters = urlParams.get('showCreate');

    if (showFilters === 'true') {
        document.querySelector('.new-content').style.display = 'block';
        document.querySelector('.default-content').style.display = 'none';
    } else {
        document.querySelector('.new-content').style.display = 'none';
        document.querySelector('.default-content').style.display = 'block';
    }
});

const coverBox = document.getElementById('coverBox');
const extraImagesBox = document.getElementById('extraImagesBox');
const coverInput = document.getElementById('coverInput');
const extraImagesInput = document.getElementById('extraImagesInput');

// coverBox.addEventListener('click', () => coverInput.click());
// extraImagesBox.addEventListener('click', () => extraImagesInput.click());

coverInput.addEventListener('change', handleCoverImage);
extraImagesInput.addEventListener('change', handleExtraImages);

function handleCoverImage(event) {
    const files = event.target.files;

    if (files.length > 1) {
        alert("Only one cover image allowed.");
        return;
    }

    // You can now process the cover image as you see fit
}

function handleExtraImages(event) {
    const files = event.target.files;

    if (files.length > 5) {
        alert("Only a maximum of 5 extra images allowed.");
        return;
    }

    // You can now process the extra images as you see fit
}

</script> -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Section handling button clicks to display different content
    document.querySelector('.button-holder .nieuwe').addEventListener('click', toggleDisplay);
    document.querySelector('.button-holder .geplaatste').addEventListener('click', toggleDisplay);
    document.querySelector('.button-holder .contact').addEventListener('click', toggleDisplay);
    document.querySelector('.button-holder .filters').addEventListener('click', toggleDisplay);

    // Section handling URL parameters
    handleURLParameters();

    // Event listeners for file upload and drag and drop functionality
    setupFileUploadListeners();

    // Modal related functionality
    setupModalFunctionality();

    // Filter form submission
    setupFilterFormSubmission();
    
    // Event listeners for file upload and drag and drop functionality
    setupFileUploadListeners();

    // Event listeners for pagination links
    document.querySelectorAll('.pagination2 a').forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var page = this.getAttribute('href').split('=')[1];
            fetchContactSubmissions(page);
        });
    });

    // Setup AJAX form submission for adding filters
    const addFilterForm = document.querySelector('.filter-container form');
    addFilterForm.addEventListener('submit', function(event) {
        event.preventDefault();
        submitFormAjax(addFilterForm, 'save_filter.php');
    });

    // Setup AJAX form submission for deleting filters
    const deleteFilterForm = document.querySelector('.remove-filter-container form');
    deleteFilterForm.addEventListener('submit', function(event) {
        event.preventDefault();
        submitFormAjax(deleteFilterForm, 'delete_filter.php');
    });

});

function toggleDisplay(e) {
    e.preventDefault();
    const targetContent = e.target.getAttribute('data-content');
    document.querySelectorAll('.content-holder > div').forEach(div => {
        div.style.display = div.classList.contains(targetContent) ? 'block' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to buttons
    document.querySelectorAll('.button-holder').forEach(button => {
        button.addEventListener('click', toggleDisplay);
    });
});


function handleURLParameters() {
    const urlParams = new URLSearchParams(window.location.search);
    const showFilters = urlParams.get('showFilters');
    const showCreate = urlParams.get('showCreate');
    const showListing = urlParams.get('showListing');
    const ShowContact = urlParams.get('ShowContact');

    // Add additional conditions as needed
    if (showFilters === 'true') {
    document.querySelector('.filters').click();
    } else if (showCreate === 'true') {
        document.querySelector('.nieuwe').click();
    } else if (showListing === 'true') {
        // Show listing content
    } else if (ShowContact === 'true') {
        document.querySelector('.contact').click();
    } else if (ShowContact === 'true') {
        document.querySelector('.users').click();
    } else {
        // show default content
    }
}


function setupFileUploadListeners() {
    const coverBox = document.getElementById('coverBox');
    const extraImagesBox = document.getElementById('extraImagesBox');
    const coverInput = document.getElementById('coverInput');
    const extraImagesInput = document.getElementById('extraImagesInput');

    coverBox.onclick = () => coverInput.click();
    extraImagesBox.onclick = () => extraImagesInput.click();

    coverInput.addEventListener('change', handleCoverImage);
    extraImagesInput.addEventListener('change', handleExtraImages);
}

function handleCoverImage(event) {
    const files = event.target.files;

    if (files.length > 1) {
        alert("Only one cover image allowed.");
        return;
    }

    // You can now process the cover image as you see fit
}

function handleExtraImages(event) {
    const files = event.target.files;

    if (files.length > 5) {
        alert("Only a maximum of 5 extra images allowed.");
        return;
    }

    // You can now process the extra images as you see fit
}

function setupModalFunctionality() {
    var closeButton = document.getElementById('closeModal');
    if (closeButton) {
        closeButton.addEventListener('click', function() {
            document.getElementById('contactModal').style.display = 'none';
        });
    }

    window.openModal = function(submissionId) {
        var submission = document.querySelector('.contact-submission[data-id="' + submissionId + '"]');
        if (submission) {
            document.getElementById('modalName').textContent = submission.dataset.name;
            document.getElementById('modalEmail').textContent = submission.dataset.email;
            document.getElementById('modalPhone').textContent = submission.dataset.phone;
            document.getElementById('modalIp').textContent = submission.dataset.ip;
            document.getElementById('modalMessage').textContent = submission.dataset.message;
        }
        document.getElementById('contactModal').style.display = 'block';
    };

    window.onclick = function(event) {
        var modal = document.getElementById('contactModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}


function submitFormAjax(form, url) {
    const formData = new FormData(form);
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            console.log(data.message); // Show success message
            // Optionally, update the UI to reflect the changes
        } else {
            console.error(data.message); // Show error message
        }
    })
    .catch(error => console.error('Error:', error));
}

function setupFileUploadListeners() {
    const coverBox = document.getElementById('coverBox');
    const extraImagesBox = document.getElementById('extraImagesBox');
    const coverInput = document.getElementById('coverInput');
    const extraImagesInput = document.getElementById('extraImagesInput');

    coverBox.onclick = () => coverInput.click();
    extraImagesBox.onclick = () => extraImagesInput.click();

    coverInput.addEventListener('change', function() {
        updateImageText(coverInput, 'coverBoxText', 'Drop Cover Image Here');
    });

    extraImagesInput.addEventListener('change', function() {
        var count = extraImagesInput.files ? extraImagesInput.files.length : 0;
        document.getElementById('extraImagesBoxText').textContent = count > 0 ? count + " image(s) selected" : "Drop Extra Images Here (Max 5)";
    });
}

function updateImageText(inputElement, displayElementId, defaultText) {
    var count = inputElement.files ? inputElement.files.length : 0;
    var displayElement = document.getElementById(displayElementId);
    displayElement.textContent = count > 0 ? count + " image(s) selected" : defaultText;
}


function fetchContactSubmissions(page) {
    // Show loading indicator (optional)

    fetch('./path_to_your_fetch_contact_submissions_endpoint.php?page=' + page)
        .then(response => response.json())
        .then(data => {
            updateContactSubmissions(data);
            // Hide loading indicator (optional)
        })
        .catch(error => {
            console.error('Error fetching contact submissions:', error);
            // Hide loading indicator (optional)
        });
}

function updateContactSubmissions(data) {
    const submissionsList = document.querySelector('.contact-submissions-list');
    submissionsList.innerHTML = '';

    data.submissions.forEach(submission => {
        const submissionElement = document.createElement('div');
        submissionElement.className = 'contact-submission';
        submissionElement.setAttribute('data-id', submission.id);
        // Set other data attributes as needed
        submissionElement.innerHTML = `
            <span class="full-name">${submission.volledige_naam}</span>
            <span class="email">${submission.email}</span>
            <span class="phone-num">${submission.mobiel}</span>
            <button class="view-details" onclick="openModal(${submission.id})">Inhoud</button>
        `;

        submissionsList.appendChild(submissionElement);
    });
     // Update pagination if needed
    // ... Pagination update logic ...
}

function deleteSubmission(id) {
    if (!confirm("Weet u zeker dat u deze inzending wilt verwijderen?")) {
        return; // Stop if the user cancels the action
    }

    // Send AJAX request to server to delete the submission with the given ID
    fetch('delete_submission.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the submission element from the page
            document.querySelector('.contact-submission[data-id="' + id + '"]').remove();
        } else {
            alert("Er is een fout opgetreden bij het verwijderen van de inzending.");
        }
    })
    .catch(error => console.error('Error:', error));
}


$(document).ready(function() {
    // User Creation
    $('#createUserButton').click(function() {
        const username = $('#username').val();
        const password = $('#password').val();
        createUser(username, password);
    });

    // User Deletion from Form
    $('#deleteUserButton').click(function() {
        const userId = $('#userid').val();
        deleteUser(userId);
    });

    // User Deletion from List
    $('.deleteUserFromList').click(function() {
        const userId = $(this).closest('tr').find('.userId').text();
        deleteUser(userId);
    });

    function createUser(username, password) {
        // Ajax request to PHP script to create user
        $.ajax({
            url: '/woningmakelaar/assets/php/create-user.php',
            type: 'POST',
            dataType: 'json',
            data: JSON.stringify({ username, password }),
            contentType: 'application/json',
            success: function(data) {
                if (data.success) {
                    alert('User created successfully.');
                    // Append a query parameter to the URL
                    window.location.href = updateURLParameter(window.location.href, 'userCreated', 'true');
                } else {
                    alert('Failed to create user.');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }

    function deleteUser(userId) {
        // Ajax request to PHP script to delete user
        $.ajax({
            url: '/woningmakelaar/assets/php/delete-user.php',
            type: 'POST',
            dataType: 'json',
            data: JSON.stringify({ userId }),
            contentType: 'application/json',
            success: function(data) {
                if (data.success) {
                    alert('User deleted successfully.');
                    // Append a query parameter to the URL
                    window.location.href = updateURLParameter(window.location.href, 'userDeleted', 'true');
                } else {
                    alert('Failed to delete user.');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }

    // Function to update query parameters in the URL
    function updateURLParameter(url, param, paramValue) {
        var regex = new RegExp('([?&])' + param + '=.*?(&|$)');
        var newURL = url;

        if (regex.test(newURL)) {
            newURL = newURL.replace(regex, '$1' + param + '=' + paramValue + '$2');
        } else {
            newURL += (newURL.indexOf('?') === -1 ? '?' : '&') + param + '=' + paramValue;
        }

        return newURL;
    }
});


// Check for URL parameters and show/hide content
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Check for the "showUsers" parameter in the URL
    if (urlParams.has('showUsers') && urlParams.get('showUsers') === 'true') {
        // Set the style property of the element with the ID "newestest-content" to "block"
        $('#newestest-content').css('display', 'block');
    } else {
        // Set the style property of the element with the ID "newestest-content" to "none"
        $('#newestest-content').css('display', 'none');
    }
});


</script>




<!-- Detailed View Modal for Woningen -->
<div id="woningenDetailsModal" class="details-modal">
    <div class="details-modal-content">
        <span class="close-woningen-details-modal">&times;</span>
        <div class="details-content">
            <!-- Listing details will be loaded here -->
        </div>
    </div>
</div>



</body>

<script src="../js/detailsmodal.js"></script>

</body>
</html>