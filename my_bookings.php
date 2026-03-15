<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'connect.php';
include 'header.php';
include 'sidebar.php';
$user_id = $_SESSION['user_id'];

// جلب جميع الحجوزات الخاصة بالعميل
$stmt = $conn->prepare("
    SELECT 
        b.booking_id,
        b.booking_date,
        b.status,
        h.hall_name
    FROM bookings b
    JOIN halls h ON b.hall_id = h.hall_id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// تقسيم الحجوزات حسب الحالة
$pending = [];
$approved = [];
$rejected = [];

while ($row = $result->fetch_assoc()) {
    if ($row['status'] === 'pending') {
        $pending[] = $row;
    } elseif ($row['status'] === 'approved') {
        $approved[] = $row;
    } else {
        $rejected[] = $row;
    }
}
?>

<div class="content">
    <h2>حجوزاتي</h2>

    <!-- الحجوزات قيد الانتظار -->
    <h3>الحجوزات قيد الانتظار</h3>
    <?php if (count($pending) > 0): ?>
        <table border="1" width="100%" cellpadding="10">
            <tr>
                <th>القاعة</th>
                <th>تاريخ الطلب</th>
                <th>الحالة</th>
            </tr>

            <?php foreach ($pending as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['hall_name']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    <td>قيد الانتظار</td>
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
                <th>القاعة</th>
                <th>تاريخ الطلب</th>
                <th>الحالة</th>
            </tr>

            <?php foreach ($approved as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['hall_name']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    <td>✔️ مقبول</td>
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
                <th>القاعة</th>
                <th>تاريخ الطلب</th>
                <th>الحالة</th>
            </tr>

            <?php foreach ($rejected as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['hall_name']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    <td>❌ مرفوض</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>لا توجد حجوزات مرفوضة.</p>
    <?php endif; ?>

</div>
