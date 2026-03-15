<?php
session_start();
include 'connect.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// استقبال البيانات
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

$allowed_status = ['approved', 'rejected'];

// التحقق من صحة البيانات
if ($booking_id <= 0 || !in_array($status, $allowed_status)) {
    header("Location: manage_booking.php");
    exit;
}

// تحديث حالة الحجز (فقط إذا كان pending)
$sql = "
UPDATE bookings
JOIN halls ON bookings.hall_id = halls.hall_id
SET bookings.status = ?
WHERE bookings.booking_id = ?
AND halls.user_id = ?
AND bookings.status = 'pending'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $status, $booking_id, $seller_id);
$stmt->execute();

// إذا تم التحديث بنجاح
if ($stmt->affected_rows > 0) {

    // جلب بيانات الحجز لإرسال إشعار
    $sql = "
    SELECT bookings.user_id AS customer_id, halls.hall_name
    FROM bookings
    JOIN halls ON bookings.hall_id = halls.hall_id
    WHERE bookings.booking_id = ?
    ";

    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("i", $booking_id);
    $stmt2->execute();
    $data = $stmt2->get_result()->fetch_assoc();

    if ($data) {
        $customer_id = $data['customer_id'];
        $hall_name = $data['hall_name'];

        // نص الإشعار
        $message = ($status === 'approved') 
            ? "تم قبول حجزك لقاعة ($hall_name) 🎉"
            : "تم رفض حجزك لقاعة ($hall_name) ❌";

        // إدخال الإشعار
        $stmt3 = $conn->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
        $stmt3->bind_param("is", $customer_id, $message);
        $stmt3->execute();
        $stmt3->close();
    }

    $stmt2->close();
}

$stmt->close();

// العودة لصفحة إدارة الحجوزات
header("Location: manage_booking.php");
exit;
?>
