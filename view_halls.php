<?php
session_start();
include 'connect.php';
include 'header.php';
include 'sidebar.php';

// رقم البائع من الجلسة
$client_id = $_SESSION['user_id'];

// جلب القاعات الخاصة بالبائع
$result = $conn->query("SELECT * FROM halls WHERE user_id = $client_id");
?>
<div class="content">

    <h1>قاعـاتي</h1>

    <!-- زر إضافة قاعة -->
    <a href="add_hall.php" 
       style="
            display:inline-block;
            background:#d63384;
            color:white;
            padding:10px 20px;
            border-radius:8px;
            text-decoration:none;
            font-weight:bold;
            margin-bottom:20px;
       ">
        + إضافة قاعة جديدة
    </a>

    <!-- جدول القاعات -->
    <table border="1" width="100%" style="border-collapse:collapse; margin-bottom:30px;">
        <tr style="background:#ffe6f2;">
            <th>اسم القاعة</th>
            <th>المدينة</th>
            <th>السعة</th>
            <th>السعر</th>
            <th>الإجراءات</th>
        </tr>

        <?php if ($result->num_rows > 0) { 
            while($row = $result->fetch_assoc()) { ?>
            
            <tr>
                <td><?= $row['hall_name']; ?></td>
                <td><?= $row['city']; ?></td>
                <td><?= $row['capacity']; ?></td>
                <td><?= $row['price']; ?></td>
                <td>
                    <a href="edit_hall.php?id=<?= $row['hall_id']; ?>" 
                       style="color:blue; font-weight:bold;">تعديل</a> |
                    <a href="delete_hall.php?id=<?= $row['hall_id']; ?>" 
                       style="color:red; font-weight:bold;"
                       onclick="return confirm('هل أنت متأكد من حذف القاعة؟');">
                       حذف
                    </a>
                </td>
            </tr>

        <?php } } else { ?>
            <tr><td colspan="5" style="text-align:center;">لا توجد قاعات مضافة بعد.</td></tr>
        <?php } ?>
    </table>

    <!-- بطاقات القاعات -->
    <div class="halls-container">
        <?php 
        $result->data_seek(0); // إعادة المؤشر لبداية النتائج لعرضها مرة ثانية

        while($row = $result->fetch_assoc()){ ?>

        <div class="hall-card" style="
            background:white;
            padding:15px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
            margin-bottom:20px;
            width:300px;
            display:inline-block;
            vertical-align:top;
            margin-right:15px;
        ">

            <!-- صورة القاعة -->
            <?php
            $folder = "uploads/halls/" . $row['hall_id'] . "/";
            $images = glob($folder . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);

            // إذا فيه صور نعرض أول صورة، إذا ما فيه نعرض صورة افتراضية
            $display_image = !empty($images) ? $images[0] : "no-image.png";
            ?>

            <img src="<?= $display_image ?>" 
                 style="width:100%; height:200px; object-fit:cover; border-radius:10px;">

            <h3><?= $row['hall_name']; ?></h3>
            <p>المدينة: <?= $row['city']; ?></p>
            <p>السعة: <?= $row['capacity']; ?></p>

            <a href="edit_hall.php?id=<?= $row['hall_id']; ?>" 
               style="display:block; background:#d63384; color:white; padding:10px; border-radius:6px; text-align:center; margin-top:10px;">
               تعديل
            </a>

            <a href="delete_hall.php?id=<?= $row['hall_id']; ?>" 
               style="display:block; background:#ff4d4d; color:white; padding:10px; border-radius:6px; text-align:center; margin-top:10px;"
               onclick="return confirm('هل أنت متأكد من حذف القاعة؟');">
               حذف
            </a>

        </div>

        <?php } ?>
    </div>

</div>
