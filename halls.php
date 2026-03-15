<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>القاعات - موقع القاعات</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="content">

    <h1 style="color:#d63384; text-align:right;">جميع القاعات</h1>

    <!-- نموذج بحث أعلى الصفحة -->
    <section class="search-form" style="
        background:#ffe6f0;
        padding:20px;
        border-radius:10px;
        margin-bottom:30px;
        box-shadow:0 0 5px rgba(0,0,0,0.1);
        text-align:center;
    ">
        <form action="search.php" method="GET">

            <input type="text" name="city" placeholder="المدينة"
                style="padding:10px; width:200px; border:1px solid #ddd; border-radius:5px;">

            <input type="date" name="date"
                style="padding:10px; width:180px; border:1px solid #ddd; border-radius:5px;">

            <input type="number" name="capacity" placeholder="عدد الأشخاص"
                style="padding:10px; width:180px; border:1px solid #ddd; border-radius:5px;">

            <button type="submit" class="book-btn">بحث</button>

        </form>
    </section>

    <!-- عرض القاعات -->
    <div class="halls-container">

        <!-- قاعة (نموذج) -->
        <div class="hall-card">
            <h2>قاعة روز</h2>
            <p>المدينة: تبوك</p>
            <p>السعة: 250 شخص</p>
            <a href="hall_details.php?id=1" class="book-btn">عرض التفاصيل</a>
        </div>

        <div class="hall-card">
            <h2>قاعة الماس</h2>
            <p>المدينة: تبوك</p>
            <p>السعة: 350 شخص</p>
            <a href="hall_details.php?id=2" class="book-btn">عرض التفاصيل</a>
        </div>

        <div class="hall-card">
            <h2>قاعة ليالينا</h2>
            <p>المدينة: تبوك</p>
            <p>السعة: 200 شخص</p>
            <a href="hall_details.php?id=3" class="book-btn">عرض التفاصيل</a>
        </div>

        <div class="hall-card">
            <h2>قاعة مون لايت</h2>
            <p>المدينة: المدينة المنورة</p>
            <p>السعة: 400 شخص</p>
            <a href="hall_details.php?id=4" class="book-btn">عرض التفاصيل</a>
        </div>

        <div class="hall-card">
            <h2>قاعة الفخامة</h2>
            <p>المدينة: جدة</p>
            <p>السعة: 500 شخص</p>
            <a href="hall_details.php?id=5" class="book-btn">عرض التفاصيل</a>
        </div>

    </div>

</div>

</body>
</html>