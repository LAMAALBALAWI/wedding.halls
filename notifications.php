<?php
session_start();
include 'connect.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* 1) تعليم الإشعارات كمقروءة */
$update = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
$update->bind_param("i", $user_id);
$update->execute();

/* 2) جلب الإشعارات */
$stmt = $conn->prepare("
    SELECT message, created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>الإشعارات</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="content" style="max-width:700px; margin:auto; margin-top:40px;">

    <h2 style="color:#d63384;">الإشعارات</h2>

    <?php if ($result->num_rows > 0): ?>
        <ul style="list-style:none; padding:0;">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li style="margin-bottom:15px; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    <div style="font-size:14px; color:#555;">
                        <?= htmlspecialchars($row['message']) ?>
                    </div>
                    <div style="font-size:12px; color:#999; margin-top:5px;">
                        <?= htmlspecialchars($row['created_at']) ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>لا توجد إشعارات حالياً.</p>
    <?php endif; ?>

    <br>
    <a href="index.php" style="color:#d63384;">العودة للصفحة الرئيسية</a>

</div>

</body>
</html>
