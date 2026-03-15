<?php
include 'connect.php';

// عداد الإشعارات
$notif_count = 0;
$msg_count = 0;

if (isset($_SESSION['user_id'])) {

    $uid = $_SESSION['user_id'];

    // الإشعارات غير المقروءة
    $stmt = $conn->prepare("SELECT COUNT(*) AS c FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $notif_count = $res['c'];

    // الرسائل غير المقروءة
    $stmt2 = $conn->prepare("SELECT COUNT(*) AS c FROM messages WHERE receiver_id = ? AND is_read = 0");
    $stmt2->bind_param("i", $uid);
    $stmt2->execute();
    $res2 = $stmt2->get_result()->fetch_assoc();
    $msg_count = $res2['c'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>منصة حجز القاعات</title>
    <link rel="stylesheet" href="style.css">

<style>
.header-icons {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-btn {
    background: #d63384;
    color: white;
    font-size: 22px;
    padding: 10px 14px;
    border-radius: 8px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: 0.2s;
    position: relative;
}

.header-btn:hover {
    background: #b82a6f;
}

.badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: red;
    color: white;
    padding: 2px 6px;
    font-size: 12px;
    border-radius: 50%;
    font-weight: bold;
}
</style>

</head>
<body>

<header class="header">
    <div class="logo">منصة حجز القاعات</div>

    <div class="header-right">
        <span style="color:white;margin-right:10px;">
            <?php if(isset($_SESSION['username'])) echo "مرحبا، ".$_SESSION['username']; ?>
        </span>

        <div class="header-icons">

            <!-- الصفحة الرئيسية -->
            <a href="index.php" class="header-btn">🏠</a>

            <!-- القائمة الجانبية -->
            <button id="toggleSidebar" class="header-btn">☰</button>

            <!-- الإشعارات -->
            <a href="notifications.php" class="header-btn">
                🔔
                <?php if ($notif_count > 0): ?>
                    <span class="badge"><?= $notif_count ?></span>
                <?php endif; ?>
            </a>

            <!-- الرسائل -->
            <a href="messages.php" class="header-btn">
                💬
                <?php if ($msg_count > 0): ?>
                    <span class="badge"><?= $msg_count ?></span>
                <?php endif; ?>
            </a>

            <!-- الملف الشخصي -->
            <a href="profile.php" class="header-btn">👤</a>

        </div>
    </div>
</header>

</body>
</html>
