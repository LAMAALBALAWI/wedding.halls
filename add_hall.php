<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = "";

if (isset($_POST['submit'])) {

    $hall_name   = mysqli_real_escape_string($conn, $_POST['hall_name']);
    $region      = mysqli_real_escape_string($conn, $_POST['region']);
    $city        = mysqli_real_escape_string($conn, $_POST['city']);
    $capacity    = (int) $_POST['capacity'];
    $price       = (float) $_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $user_id     = $_SESSION['user_id'];

    // إدخال القاعة بدون صور
    $query = "
        INSERT INTO halls (hall_name, region, city, capacity, price, description, user_id)
        VALUES ('$hall_name', '$region', '$city', '$capacity', '$price', '$description', '$user_id')
    ";

    if (mysqli_query($conn, $query)) {

        $hall_id = mysqli_insert_id($conn);

        // إنشاء مجلد القاعة
        $folder = "uploads/halls/$hall_id/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        // دالة ضغط الصور (معدّلة بالكامل)
        function compressImage($source, $destination, $quality = 70) {
            $info = getimagesize($source);

            if ($info['mime'] == 'image/jpeg') {
                $image = imagecreatefromjpeg($source);
            } elseif ($info['mime'] == 'image/png') {
                $image = imagecreatefrompng($source);
            } elseif ($info['mime'] == 'image/gif') {
                $image = imagecreatefromgif($source);
                    } elseif ($info['mime'] == 'image/wepb') {
                $image = imagecreatefromgif($source);
            } else {
                return false; // نوع غير مدعوم
            }

            imagejpeg($image, $destination, $quality);
            return true;
        }

        // رفع الصور المتعددة
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {

            if (!empty($tmp_name)) {

                $file_name = time() . "_" . $_FILES['images']['name'][$key];
                $destination = $folder . $file_name;

                // ضغط الصورة
                compressImage($tmp_name, $destination, 70);
            }
        }

        header("Location: manage_hall_images.php?hall_id=$hall_id");
        exit;

    } else {
        $error = "خطأ في قاعدة البيانات: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إضافة قاعة</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="add-hall-box">
<h2>إضافة قاعة جديدة</h2>

<?php if (!empty($error)): ?>
    <div class="error"><?= $error ?></div>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data">

    <label>اسم القاعة:</label>
    <input type="text" name="hall_name" required>

    <label>المنطقة:</label>
    <select id="region" name="region" required>
        <option value="">اختر المنطقة</option>
        <option value="الرياض">الرياض</option>
        <option value="مكة المكرمة">مكة المكرمة</option>
        <option value="المدينة المنورة">المدينة المنورة</option>
        <option value="القصيم">القصيم</option>
        <option value="الشرقية">الشرقية</option>
        <option value="عسير">عسير</option>
        <option value="تبوك">تبوك</option>
        <option value="حائل">حائل</option>
        <option value="الحدود الشمالية">الحدود الشمالية</option>
        <option value="جازان">جازان</option>
        <option value="نجران">نجران</option>
        <option value="الباحة">الباحة</option>
        <option value="الجوف">الجوف</option>
    </select>

    <label>المدينة:</label>
    <select id="city" name="city" required>
        <option value="">اختر المدينة</option>
    </select>

    <label>السعة:</label>
    <input type="number" name="capacity" required>

    <label>السعر:</label>
    <input type="number" name="price" required>

    <label>الوصف:</label>
    <textarea name="description" rows="4"></textarea>

    <label>صور القاعة (يمكن رفع أكثر من صورة):</label>
    <input type="file" name="images[]" multiple accept="image/*">

    <button type="submit" name="submit">إضافة القاعة</button>

</form>
</div>

<script>
const cities = {
    "الرياض": ["الرياض","الخرج","الدرعية","الزلفي","المجمعة","القويعية","وادي الدواسر","الأفلاج","حوطة بني تميم","عفيف","الدوادمي"],
    "مكة المكرمة": ["مكة","جدة","الطائف","الليث","القنفذة","رابغ","الجموم"],
    "المدينة المنورة": ["المدينة","ينبع","العلا","بدر","خيبر","المهد"],
    "القصيم": ["بريدة","عنيزة","الرس","البكيرية","المذنب","البدائع"],
    "الشرقية": ["الدمام","الخبر","الظهران","الأحساء","الجبيل","القطيف","حفر الباطن"],
    "عسير": ["أبها","خميس مشيط","محايل","بيشة","رجال ألمع"],
    "تبوك": ["تبوك","ضباء","الوجه","أملج","حقل","تيماء"],
    "حائل": ["حائل","بقعاء","الشنان","الغزالة"],
    "الحدود الشمالية": ["عرعر","رفحاء","طريف"],
    "جازان": ["جازان","صبيا","صامطة","أبو عريش","العارضة"],
    "نجران": ["نجران","شرورة","حبونا"],
    "الباحة": ["الباحة","بلجرشي","المندق","المخواة"],
    "الجوف": ["سكاكا","القريات","دومة الجندل"]
};

document.getElementById("region").addEventListener("change", function() {
    let region = this.value;
    let citySelect = document.getElementById("city");

    citySelect.innerHTML = "<option value=''>اختر المدينة</option>";

    if (cities[region]) {
        cities[region].forEach(function(city) {
            let opt = document.createElement("option");
            opt.value = city;
            opt.textContent = city;
            citySelect.appendChild(opt);
        });
    }
});
</script>

</body>
</html>
