<?php
include 'config.php'; // Include your database configuration file

// Function to handle image upload
function uploadImage($file, $directory = "uploads/") {
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $targetFile = $directory . uniqid("img_", true) . '.' . $imageFileType;

    if ($file["size"] > 5000000) { // 5MB limit
        echo "Sorry, your file is too large.";
        return false;
    }

    if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $targetFile;
        } else {
            echo "Sorry, there was an error uploading your file.";
            return false;
        }
    } else {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        return false;
    }
}

// Process the listing form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = $_POST['address'];
    $postcode = $_POST['postcode'];
    $prijs = $_POST['prijs'];
    $beschrijving = $_POST['beschrijving'];

    // Handling Filters
    $selectedLiggingFilters = $_POST['ligging'] ?? []; // Assuming 'ligging' is an array of selected filter IDs
    $selectedEigenschappenFilters = $_POST['eigenschappen'] ?? []; // Assuming 'eigenschappen' is an array of selected filter IDs   

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'option') === 0) {
            if (in_array($value, $_POST['ligging'])) {
                $selectedLiggingFilters[] = $value;
            } elseif (in_array($value, $_POST['eigenschappen'])) {
                $selectedEigenschappenFilters[] = $value;
            }
        }
    }

    $liggingFiltersString = implode(',', $selectedLiggingFilters);
    $eigenschappenFiltersString = implode(',', $selectedEigenschappenFilters);

    // Insert data into the listings table
    $sql = "INSERT INTO listings (address, postcode, prijs, beschrijving, ligging, eigenschappen) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$address, $postcode, $prijs, $beschrijving, $liggingFiltersString, $eigenschappenFiltersString]);
    $listing_id = $pdo->lastInsertId();

    $listing_id = $pdo->lastInsertId();

    if (isset($_FILES['coverImage']) && $_FILES['coverImage']['error'] == UPLOAD_ERR_OK) {
        $coverImagePath = uploadImage($_FILES['coverImage']);
        if ($coverImagePath) {
            $updateSql = "UPDATE listings SET cover_image_path = ? WHERE id = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$coverImagePath, $listing_id]);
        }
    }

  // Handling Extra Images
  $extraImagesPaths = [];
  if (isset($_FILES['extraImages'])) {
      $count = 0; // Counting the number of processed images
      foreach ($_FILES['extraImages']['tmp_name'] as $key => $tmpName) {
          if ($count >= 5) {
              break;
          }
          if ($_FILES['extraImages']['error'][$key] == UPLOAD_ERR_OK) {
              $fileArray = [
                  'name' => $_FILES['extraImages']['name'][$key],
                  'tmp_name' => $_FILES['extraImages']['tmp_name'][$key],
                  'size' => $_FILES['extraImages']['size'][$key]
              ];
              $imagePath = uploadImage($fileArray);
              if ($imagePath) {
                  $extraImagesPaths[] = $imagePath;
                  $count++;
              }
          }
      }
  }
  if (!empty($extraImagesPaths)) {
      $imagesString = implode(',', $extraImagesPaths);
      $updateExtraImagesSql = "UPDATE listings SET extra_images = ? WHERE id = ?";
      $updateExtraImagesStmt = $pdo->prepare($updateExtraImagesSql);
      $updateExtraImagesStmt->execute([$imagesString, $listing_id]);
  }   
    

    header('Location: admin.php?showCreate=true');
    exit;
}
?>
