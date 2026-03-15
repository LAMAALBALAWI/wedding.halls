<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// عند إرسال النموذج
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hall_id = $_POST['hall_id'];
    $booking_date = $_POST['booking_date'];
    $user_id = $_SESSION['user_id'];

    // التحقق من عدم وجود حجز لنفس القاعة في نفس اليوم
    $check = $conn->prepare("SELECT * FROM bookings WHERE hall_id = ? AND booking_date = ?");
    $check->bind_param("is", $hall_id, $booking_date);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "❌ القاعة محجوزة في هذا التاريخ، الرجاء اختيار تاريخ آخر.";
    } else {
        // تنفيذ الحجز
        $insert = $conn->prepare("INSERT INTO bookings (user_id, hall_id, booking_date) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $user_id, $hall_id, $booking_date);

        if ($insert->execute()) {
            $message = "✅ تم الحجز بنجاح!";
        } else {
            $message = "❌ حدث خطأ أثناء الحجز.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>حجز قاعة</title>

<style>
body {
    font-family: "Tajawal", sans-serif;
    background: #ffe6f2;
    margin: 0;
    padding: 0;
    direction: rtl;
    text-align: right;
}

.booking-container {
    width: 50%;
    margin: 50px auto;
    background: #ffffff;
    padding: 30px;
    border-radius: 15px;
    border: 2px solid #ffb6d9;
    box-shadow: 0 0 20px rgba(255, 182, 217, 0.4);
}

.booking-container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #d63384;
    font-size: 28px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #b30059;
}

select, input[type="date"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ff99c8;
    border-radius: 10px;
    font-size: 16px;
    background: #fff0f7;
}

button {
    width: 100%;
    padding: 14px;
    background: #ff4da6;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 18px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #e60073;
}

.message {
    text-align: center;
    font-size: 18px;
    margin-bottom: 20px;
}
.back-btn {
    display: block;
    text-align: center;
    margin-top: 15px;
    padding: 12px;
    background: #ff99c8;
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-size: 18px;
    transition: 0.3s;
}

.back-btn:hover {
    background: #d63384;
}

</style>

</head>
<body>

<div class="booking-container">
    <h2>حجز قاعة</h2>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>اختر القاعة:</label>
        <select name="hall_id" required>
            <?php
            $halls = $conn->query("SELECT * FROM halls");
            while ($hall = $halls->fetch_assoc()):
            ?>
                <option value="<?= $hall['hall_id'] ?>">
                    <?= htmlspecialchars($hall['hall_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>تاريخ الحجز:</label>
        <input type="date" name="booking_date" required>

        <button type="submit">احجز الآن</button>
                <a href="index.php" class="back-btn">الرجوع للصفحة الرئيسية</a>

    </form>
</div>

</body>
</html>
