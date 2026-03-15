<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'connect.php';
include 'header.php';
include 'sidebar.php';

?>

<div class="content">
    <h2>إدارة الحجوزات</h2>

    <table border="1" width="100%" cellpadding="10">
        <tr>
            <th>اسم العميل</th>
            <th>القاعة</th>
            <th>تاريخ الحجز</th>
            <th>الحالة / الإجراء</th>
        </tr>

        <?php
        $stmt = $conn->prepare("
            SELECT 
                b.booking_id,
                u.name AS customer_name,
                h.hall_name,
                b.booking_date,
                b.status
            FROM bookings b
            JOIN users u ON b.user_id = u.user_id
            JOIN halls h ON b.hall_id = h.hall_id
            ORDER BY b.booking_date ASC
        ");

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                if ($row['status'] === 'pending') {
                    $status_text = 'قيد المراجعة';
                } elseif ($row['status'] === 'approved') {
                    $status_text = 'مؤكد';
                } elseif ($row['status'] === 'rejected') {
                    $status_text = 'مرفوض';
                } else {
                    $status_text = 'غير معروف';
                }
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['hall_name']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>

                    <td>
                    <?php if ($row['status'] === 'pending'): ?>
                        <a href="payment_request.php?booking_id=<?= $row['booking_id'] ?>"
                           onclick="return confirm('قبل قبول الحجز يجب دفع عمولة المنصة. هل تريد المتابعة؟')">
                             قبول
                        </a>
                        |
                        <a href="update_booking.php?id=<?= $row['booking_id'] ?>&status=rejected"
                           onclick="return confirm('هل أنت متأكد من رفض الحجز؟')">
                             رفض
                        </a>
                    <?php else: ?>
                        <strong><?= $status_text ?></strong>
                    <?php endif; ?>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='4'>لا توجد حجوزات</td></tr>";
        }
        ?>
    </table>
</div>
