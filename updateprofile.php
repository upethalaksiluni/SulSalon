<?php
session_start();
include('dbconnection.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $home_address = $_POST['home_address'];
    $preferred_stylist = $_POST['preferred_stylist'];
    $frequent_services = $_POST['frequent_services'];
    $preferred_appointment_time = $_POST['preferred_appointment_time'];
    $hair_skin_type = $_POST['hair_skin_type'];
    $allergies = $_POST['allergies'];
    $medical_conditions = $_POST['medical_conditions'];

    // Handle profile picture upload
    $profile_picture = NULL;
    if ($_FILES['profile_picture']['tmp_name']) {
        $profile_picture = addslashes(file_get_contents($_FILES['profile_picture']['tmp_name']));
    }

    $sql = "UPDATE users SET full_name = '$full_name', gender = '$gender', dob = '$dob', phone_number = '$phone_number', email = '$email', home_address = '$home_address', profile_picture = '$profile_picture', preferred_stylist = '$preferred_stylist', frequent_services = '$frequent_services', preferred_appointment_time = '$preferred_appointment_time', hair_skin_type = '$hair_skin_type', allergies = '$allergies', medical_conditions = '$medical_conditions' WHERE username = '$username'";
    
    if (mysqli_query($conn, $sql)) {
        header('Location: userprofile.php');
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
