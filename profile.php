<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

include 'connect.php';
include 'header.php';
include 'sidebar.php';

// جلب بيانات المستخدم
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id=?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<div class="content">
    <h2>معلوماتي</h2>
    <p><strong>اسم المستخدم:</strong> <?php echo $user['name']; ?></p>
    <p><strong>البريد الإلكتروني:</strong> <?php echo $user['email']; ?></p>
</div>

<script src="script.js"></script>