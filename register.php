<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $_POST['full_name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $home_address = $_POST['home_address'];
    $profile_picture = NULL;

    if ($_FILES['profile_picture']['tmp_name']) {
        $profile_picture = addslashes(file_get_contents($_FILES['profile_picture']['tmp_name']));
    }

    $preferred_stylist = $_POST['preferred_stylist'];
    $frequent_services = $_POST['frequent_services'];
    $preferred_appointment_time = $_POST['preferred_appointment_time'];
    $hair_skin_type = $_POST['hair_skin_type'];
    $allergies = $_POST['allergies'];
    $medical_conditions = $_POST['medical_conditions'];

    $sql = "INSERT INTO users (username, password, full_name, gender, dob, phone_number, email, home_address, profile_picture, preferred_stylist, frequent_services, preferred_appointment_time, hair_skin_type, allergies, medical_conditions)
            VALUES ('$username', '$password', '$full_name', '$gender', '$dob', '$phone_number', '$email', '$home_address', '$profile_picture', '$preferred_stylist', '$frequent_services', '$preferred_appointment_time', '$hair_skin_type', '$allergies', '$medical_conditions')";

    if (mysqli_query($conn, $sql)) {
        header('Location: login.html');
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
