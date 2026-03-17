<?php
session_start();
include 'connect.php';

// التحقق من وجود booking_id
if (!isset($_GET['booking_id'])) {
    die("خطأ: رقم الحجز غير موجود.");
}

$booking_id = intval($_GET['booking_id']);

// تحديث حالة الحجز مباشرة بدون انتظار
$conn->query("UPDATE bookings SET status='approved' WHERE booking_id = $booking_id");

// جلب بيانات الحجز
$booking = $conn->query("SELECT * FROM bookings WHERE booking_id = $booking_id")->fetch_assoc();
if (!$booking) {
    die("خطأ: الحجز غير موجود.");
}

$hall_id = $booking['hall_id'];

// جلب بيانات القاعة لمعرفة السعر
$hall = $conn->query("SELECT * FROM halls WHERE hall_id = $hall_id")->fetch_assoc();
$price = $hall['price'];

// حساب العمولة 5%
$commission = $price * 0.05;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>دفع العمولة</title>

<style>
.box {
    max-width: 450px;
    margin: 60px auto;
    padding: 25px;
    background: #fff5fa;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #f3c6dd;
}

button {
    display: block;
    margin: 15px auto;
    padding: 12px;
    border-radius: 10px;
    font-size: 18px;
    font-weight: bold;
    width: 100%;
    border: none;
    cursor: not-allowed;
    background: #f7cfe3;
    color: white;
    transition: 0.3s;
}

button.active {
    background: #d63384;
    cursor: pointer;
}

input[type="file"] {
    margin: 15px 0;
    padding: 10px;
}
</style>

</head>
<body>

<div class="box">
    <h2>دفع عمولة المنصة</h2>

    <p>سعر القاعة: <strong><?= $price ?> ريال</strong></p>
    <p>عمولة المنصة (5%): <strong><?= $commission ?> ريال</strong></p>

    <p>الرجاء رفع صورة إيصال الدفع لإرسالها للإدارة.</p>

    <!-- نموذج Formspree -->
    <form action="https://formspree.io/f/mqedbgek" method="POST" enctype="multipart/form-data">

        <!-- إرسال بيانات إضافية مع الإيميل -->
        <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
        <input type="hidden" name="hall_price" value="<?= $price ?>">
        <input type="hidden" name="commission" value="<?= $commission ?>">

        <label>رفع الإيصال:</label>
        <input id="receiptInput" type="file" name="receipt" accept="image/*" required>

        <button id="payBtn" type="submit" disabled>إرسال الإيصال</button>

    </form>

    <p style="margin-top:20px; color:#28a745; font-weight:bold;">
        ✔ تم تأكيد الحجز مباشرة — يمكنك الآن إكمال الإجراءات بدون انتظار.
    </p>
</div>

<script>
// تفعيل الزر عند رفع صورة
document.getElementById("receiptInput").addEventListener("change", function() {
    let btn = document.getElementById("payBtn");

    if (this.files.length > 0) {
        btn.disabled = false;
        btn.classList.add("active");
    } else {
        btn.disabled = true;
        btn.classList.remove("active");
    }
});
</script>

</body>
</html>
