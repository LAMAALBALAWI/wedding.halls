<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'connect.php';
include 'header.php';
include 'sidebar.php';

$seller_id = $_SESSION['user_id'];

// ربط القاعات التي بدون صاحب
$conn->query("UPDATE halls SET user_id = $seller_id WHERE user_id IS NULL");

// جلب جميع الحجوزات للقاعات الخاصة بالبائع
$stmt = $conn->prepare("
    SELECT 
        b.booking_id,
        b.created_at,
        b.booking_date,
        b.status,
        u.name AS customer_name,
        h.hall_name
    FROM bookings b
    JOIN halls h ON b.hall_id = h.hall_id
    JOIN users u ON b.user_id = u.user_id
    WHERE h.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();

// تقسيم الحجوزات حسب الحالة
$pending = [];
$approved = [];
$rejected = [];

while ($row = $result->fetch_assoc()) {

    // حساب الأيام المتبقية
    $today = new DateTime();
    $booking_day = new DateTime($row['booking_date']);
    $diff = $today->diff($booking_day)->days;

    // إضافة القيمة للصف
    $row['days_left'] = ($booking_day >= $today) ? $diff : 0;

    if ($row['status'] === 'pending') {
        $pending[] = $row;
    } elseif ($row['status'] === 'approved') {
        $approved[] = $row;
    } else {
        $rejected[] = $row;
    }
}
?>

<style>
.pending-row { background: #fff8d1; }   /* أصفر */
.approved-row { background: #d4f8d4; }  /* أخضر */
.rejected-row { background: #ffd4d4; }  /* أحمر */
</style>

<div class="content">
    <h2>طلبات الحجز</h2>

    <!-- الحجوزات قيد الانتظار -->
    <h3>الحجوزات قيد الانتظار</h3>
    <?php if (count($pending) > 0): ?>
        <table border="1" width="100%" cellpadding="10">
            <tr>
                <th>اسم العميل</th>
                <th>القاعة</th>
                <th>تاريخ الحجز</th>
                <th>الأيام المتبقية</th>
                <th>الإجراء</th>
            </tr>

            <?php foreach ($pending as $row): ?>
                <tr class="pending-row">
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['hall_name']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    <td><?= $row['days_left'] ?> يوم</td>
                    <td>
                        <a href="update_booking.php?id=<?= $row['booking_id'] ?>&status=approved"
                           onclick="return confirm('هل أنت متأكد من قبول هذا الحجز؟')">
                           قبول
                        </a>
                        |
                        <a href="update_booking.php?id=<?= $row['booking_id'] ?>&status=rejected"
                           onclick="return confirm('هل أنت متأكد من رفض هذا الحجز؟')">
                           رفض
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>لا توجد حجوزات قيد الانتظار.</p>
    <?php endif; ?>

    <br><hr><br>

    <!-- الحجوزات المقبولة -->
    <h3>الحجوزات المقبولة</h3>
    <?php if (count($approved) > 0): ?>
        <table border="1" width="100%" cellpadding="10">
            <tr>
                <th>اسم العميل</th>
                <th>القاعة</th>
                <th>تاريخ الحجز</th>
                <th>الأيام المتبقية</th>
            </tr>

            <?php foreach ($approved as $row): ?>
                <tr class="approved-row">
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['hall_name']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    <td><?= $row['days_left'] ?> يوم</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>لا توجد حجوزات مقبولة.</p>
    <?php endif; ?>

    <br><hr><br>

    <!-- الحجوزات المرفوضة -->
    <h3>الحجوزات المرفوضة</h3>
    <?php if (count($rejected) > 0): ?>
        <table border="1" width="100%" cellpadding="10">
            <tr>
                <th>اسم العميل</th>
                <th>القاعة</th>
                <th>تاريخ الحجز</th>
                <th>الأيام المتبقية</th>
            </tr>

            <?php foreach ($rejected as $row): ?>
                <tr class="rejected-row">
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['hall_name']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    <td><?= $row['days_left'] ?> يوم</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>لا توجد حجوزات مرفوضة.</p>
    <?php endif; ?>

</div>
