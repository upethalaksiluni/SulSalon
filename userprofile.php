<?php
session_start();
include('dbconnection.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit();
}

$username = $_SESSION['username'];

$sql = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>

    <?php
            $cssFiles = ["userprofilecss.dat"];
            foreach ($cssFiles as $file) {
                if (file_exists($file) && is_readable($file)) {
                    echo "<style>" . file_get_contents($file) . "</style>";
                }
            }
    ?>

</head>
<body>
    <h1>User Profile</h1>
    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" value="<?= $user['full_name']; ?>">

        <label for="gender">Gender:</label>
        <select id="gender" name="gender">
            <option value="male" <?= $user['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?= $user['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
        </select>

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" value="<?= $user['dob']; ?>">

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?= $user['phone_number']; ?>">

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" value="<?= $user['email']; ?>">

        <label for="home_address">Home Address:</label>
        <input type="text" id="home_address" name="home_address" value="<?= $user['home_address']; ?>">

        <label for="profile_picture">Profile Picture:</label>
        <input type="file" id="profile_picture" name="profile_picture">

        <label for="preferred_stylist">Preferred Stylist:</label>
        <input type="text" id="preferred_stylist" name="preferred_stylist" value="<?= $user['preferred_stylist']; ?>">

        <label for="frequent_services">Frequent Services:</label>
        <textarea id="frequent_services" name="frequent_services"><?= $user['frequent_services']; ?></textarea>

        <label for="preferred_appointment_time">Preferred Appointment Time:</label>
        <input type="text" id="preferred_appointment_time" name="preferred_appointment_time" value="<?= $user['preferred_appointment_time']; ?>">

        <label for="hair_skin_type">Hair/Skin Type:</label>
        <input type="text" id="hair_skin_type" name="hair_skin_type" value="<?= $user['hair_skin_type']; ?>">

        <label for="allergies">Allergies:</label>
        <textarea id="allergies" name="allergies"><?= $user['allergies']; ?></textarea>

        <label for="medical_conditions">Medical Conditions:</label>
        <textarea id="medical_conditions" name="medical_conditions"><?= $user['medical_conditions']; ?></textarea>

        <button type="submit">Update Profile</button>
    </form>
</body>
</html>