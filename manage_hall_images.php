<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$hall_id = intval($_GET['hall_id']);
$folder = "uploads/halls/$hall_id/";

if (!file_exists($folder)) mkdir($folder, 0777, true);

// دالة ضغط الصور مع الحفاظ على الصيغة الأصلية
function compressImage($source, $destination, $quality = 70) {
    $info = getimagesize($source);
    if ($info === false) return false;

    $mime = $info['mime'];

    switch ($mime) {

        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            imagejpeg($image, $destination, $quality);
            break;

        case 'image/png':
            $image = imagecreatefrompng($source);
            // PNG تستخدم مستوى ضغط من 0 إلى 9
            imagepng($image, $destination, 6);
            break;

        case 'image/gif':
            $image = imagecreatefromgif($source);
            imagegif($image, $destination);
            break;

        case 'image/webp':
            $image = imagecreatefromwebp($source);
            imagewebp($image, $destination, $quality);
            break;

        default:
            return false;
    }

    return true;
}

// رفع صور جديدة
if (isset($_POST['upload'])) {
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        if (!empty($tmp_name)) {

            $file_name = time() . "_" . $_FILES['images']['name'][$key];
            $destination = $folder . $file_name;

            compressImage($tmp_name, $destination, 70);
        }
    }

    header("Location: manage_hall_images.php?hall_id=$hall_id");
    exit;
}

// حذف صورة
if (isset($_GET['delete'])) {
    $file = basename($_GET['delete']);
    if (file_exists($folder . $file)) {
        unlink($folder . $file);
    }
    header("Location: manage_hall_images.php?hall_id=$hall_id");
    exit;
}

// تحديد صورة رئيسية
if (isset($_GET['main'])) {
    file_put_contents("$folder/main.txt", $_GET['main']);
    header("Location: manage_hall_images.php?hall_id=$hall_id");
    exit;
}

// إصلاح خطأ الامتدادات
$images = glob($folder . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);

$main_image = file_exists("$folder/main.txt") ? file_get_contents("$folder/main.txt") : "";
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إدارة صور القاعة</title>

<style>
body {
    background: #fff0f7;
    font-family: "Tajawal", sans-serif;
}

.back-btn {
    display: inline-block;
    margin: 15px;
    padding: 10px 15px;
    background: #d63384;
    color: white;
    text-decoration: none;
    border-radius: 8px;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 0 15px rgba(0,0,0,0.08);
}

h2 {
    color: #d63384;
    text-align: center;
    margin-bottom: 25px;
}

.upload-box {
    background: #fff5fa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    border: 1px solid #f3c6dd;
}

.upload-btn {
    background: #d63384;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    margin-top: 10px;
    cursor: pointer;
    font-size: 16px;
}

.gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}

.card {
    background: #fff;
    border-radius: 12px;
    padding: 10px;
    border: 1px solid #f3c6dd;
    box-shadow: 0 0 8px rgba(0,0,0,0.05);
    text-align: center;
}

.card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 10px;
}

.btn {
    display: block;
    margin-top: 10px;
    padding: 8px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    color: white;
}

.delete-btn {
    background: #dc3545;
}

.main-btn {
    background: #28a745;
}

.main-label {
    background: #ffc107;
    color: #000;
    padding: 5px 10px;
    border-radius: 8px;
    font-size: 14px;
    display: inline-block;
    margin-top: 8px;
}
</style>
</head>

<body>

<a href="view_halls.php" class="back-btn">⬅ العودة للصفحة الرئيسية</a>

<div class="container">

<h2>إدارة صور القاعة</h2>

<div class="upload-box">
    <form method="POST" enctype="multipart/form-data">
        <label>رفع صور جديدة:</label>
        <input type="file" name="images[]" multiple accept="image/*">
        <button class="upload-btn" type="submit" name="upload">رفع الصور</button>
    </form>
</div>

<div class="gallery">

<?php foreach ($images as $img): 
    $file = basename($img);
?>
    <div class="card">
        <img src="<?= $img ?>">

        <?php if ($file == $main_image): ?>
            <div class="main-label">⭐ الصورة الرئيسية</div>
        <?php endif; ?>

        <a class="btn delete-btn" 
           href="?hall_id=<?= $hall_id ?>&delete=<?= $file ?>"
           onclick="return confirm('هل تريد حذف الصورة؟')">حذف</a>

        <a class="btn main-btn" 
           href="?hall_id=<?= $hall_id ?>&main=<?= $file ?>">تعيين كصورة رئيسية</a>
    </div>
<?php endforeach; ?>

</div>

</div>

</body>
</html>
