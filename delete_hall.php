<?php
include 'connect.php';

if (!isset($_GET['id'])) {
    die("رقم القاعة غير موجود");
}

$hall_id = intval($_GET['id']);

// تحقق هل يوجد حجوزات مرتبطة بالقاعة
$check = mysqli_query($conn, "SELECT * FROM bookings WHERE hall_id = $hall_id");

if (mysqli_num_rows($check) > 0) {
    // يوجد حجوزات → لا نحذف
    echo "
    <script>
        alert('لا يمكنك حذف القاعة لأن عليها حجوزات.');
        window.location.href = 'view_halls.php';
    </script>
    ";
    exit;
}

// إذا ما فيه حجوزات → نحذف القاعة
mysqli_query($conn, "DELETE FROM halls WHERE hall_id = $hall_id");

// حذف مجلد الصور
$folder = "uploads/halls/$hall_id/";
if (is_dir($folder)) {
    array_map('unlink', glob("$folder/*.*"));
    rmdir($folder);
}

header("Location: view_halls.php");
exit;
?>
