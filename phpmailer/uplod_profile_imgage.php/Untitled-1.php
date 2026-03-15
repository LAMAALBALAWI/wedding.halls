<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $target_dir = "uploads/profile_pics/";
    $file_name = basename($_FILES["profile_image"]["name"]);
    $target_file = $target_dir . uniqid() . "_" . $file_name;

    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        $sql = "UPDATE users SET profile_image = '$target_file' WHERE user_id = '$user_id'";
        mysqli_query($conn, $sql);
        header("Location: profile.php");
        exit();
    } else {
        echo "حدث خطأ أثناء رفع الصورة.";
    }
} else {
    echo "الرجاء اختيار صورة صحيحة.";
}
?>