<?php
// index.php
// Define the path to the userheader.dat file
$userheaderFile = "userheader.dat"; // Adjust the path to your .dat file if needed
$userheaderContent = '';

// Check if the file exists and is readable
if (file_exists($userheaderFile) && is_readable($userheaderFile)) {
    // Read the content of the header.dat file
    $userheaderContent = file_get_contents($userheaderFile);
} else {
    $userheaderContent = 'Error loading header content from userheader.dat.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

        <?php
            $cssFiles = ["userheadercss.dat"];
            foreach ($cssFiles as $file) {
                if (file_exists($file) && is_readable($file)) {
                    echo "<style>" . file_get_contents($file) . "</style>";
                }
            }
        ?>
</head>
<body>
    <div class="content">
        <!-- Content loaded from the header.dat file -->
        <?php echo $userheaderContent; ?>
    </div>

    <?php
      // Load header JavaScript from headerjs.dat
      $jsFiles = ["userheaderjs.dat"];
      foreach ($jsFiles as $file) {
        if (file_exists($file) && is_readable($file)) {
          echo "<script>" . file_get_contents($file) . "</script>";
        } else {
          echo "<!-- Error: File $file not found or not readable -->";
        }
      }
    ?>
</body>
</html>