<?php
session_start();
include 'connect.php';

if (!isset($_GET['hall_id'])) {
    die("رقم القاعة غير موجود");
}

$hall_id = intval($_GET['hall_id']);
$folder = "uploads/halls/$hall_id/";

// جلب بيانات القاعة
$stmt = $conn->prepare("SELECT * FROM halls WHERE hall_id = ?");
$stmt->bind_param("i", $hall_id);
$stmt->execute();
$hall = $stmt->get_result()->fetch_assoc();

if (!$hall) {
    die("القاعة غير موجودة");
}

// جلب بيانات المالك
$owner_id = $hall['user_id'];
$owner = $conn->query("SELECT * FROM users WHERE user_id = $owner_id")->fetch_assoc();

// جلب الصورة الرئيسية
$main_image = "";
if (file_exists("$folder/main.txt")) {
    $main_image = file_get_contents("$folder/main.txt");
}

// جلب كل الصور
$images = glob($folder . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
/* ---------------------- حفظ التقييم ---------------------- */

if (isset($_POST['add_rating'])) {

    $rating = intval($_POST['rating']);
    $comment = $conn->real_escape_string($_POST['comment']);
    $user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "مستخدم";

    $stmt = $conn->prepare("INSERT INTO ratings (hall_id, user_name, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isis", $hall_id, $user_name, $rating, $comment);
    $stmt->execute();

    header("Location: hall_details.php?hall_id=$hall_id");
    exit;
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title><?= $hall['hall_name'] ?></title>
<link rel="stylesheet" href="style.css">
<style>
.pink-btn {
    background: #d63384;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.2s;
}

.pink-btn:hover {
    background: #b82a6f;
}

.hall-wrapper {
    max-width: 900px;
    margin: auto;
    padding: 20px;
}

/* الحاوية الأساسية */
.hall-container {
    display: flex;
    gap: 25px;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

/* صورة القاعة */
.main-img {
    width: 100%;
    height: 350px;
    object-fit: cover;
    border-radius: 12px;
}

/* معلومات القاعة */
.info-box {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.hall-title {
    color: #d63384;
    font-size: 28px;
    margin-bottom: 10px;
}

.desc {
    background: #fff5fa;
    padding: 12px;
    border-radius: 8px;
    line-height: 1.7;
}

/* الأزرار */
.btns {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.action-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px;
    border-radius: 12px;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    transition: 0.2s;
    color: white;
}

.book-btn { background: #28a745; }
.book-btn:hover { background: #1e7e34; }

.msg-btn { background: #d63384; }
.msg-btn:hover { background: #b82a6f; }

/* معرض الصور */
.gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.gallery img {
    width: 200px;
    height: 150px;
    object-fit: cover;
    border-radius: 10px;
}

/* التقييمات */
.rating-section {
    background: #fff5fa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.rating-card {
    background: white;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
    border: 1px solid #f3d1e6;
}

.rating-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.stars {
    color: #d63384;
    font-weight: bold;
}

.add-rating-box {
    background: white;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #f3d1e6;
}

.rating-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

</style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="hall-wrapper">

    <div class="hall-container">

        <!-- صورة رئيسية -->
    <?php
// إذا فيه صورة رئيسية نستخدمها
if ($main_image && file_exists($folder . $main_image)) {
    $display_image = $main_image;
}
// إذا ما فيه صورة رئيسية نستخدم أول صورة موجودة
elseif (!empty($images)) {
    $display_image = basename($images[0]);
}
// إذا ما فيه ولا صورة
else {
    $display_image = "";
}
?>

<?php if ($display_image): ?>
    <img src="uploads/halls/<?= $hall_id ?>/<?= $display_image ?>" class="main-img">
<?php else: ?>
    <div style="width:100%; height:350px; background:#ddd; border-radius:12px;"></div>
<?php endif; ?>



        <!-- معلومات القاعة -->
        <div class="info-box">
            <h1 class="hall-title"><?= $hall['hall_name'] ?></h1>

            <p><strong>المنطقة:</strong> <?= $hall['region'] ?></p>
            <p><strong>المدينة:</strong> <?= $hall['city'] ?></p>
            <p><strong>السعة:</strong> <?= $hall['capacity'] ?> شخص</p>
            <p><strong>السعر:</strong> <?= $hall['price'] ?> ريال</p>

            <p class="desc"><strong>الوصف:</strong><br><?= nl2br($hall['description']) ?></p>

            <div class="btns">
                <a href="booking.php?hall_id=<?= $hall_id ?>" class="action-btn book-btn">📅 احجز الآن</a>
                <a href="chat.php?owner_id=<?= $owner_id ?>" class="action-btn msg-btn">💬 مراسلة المالك</a>
            </div>
        </div>

    </div>

    <!-- معرض الصور -->
    <h3>معرض الصور</h3>
    <div class="gallery">
        <?php foreach ($images as $img): ?>
            <img src="<?= $img ?>">
        <?php endforeach; ?>
    </div>

    <br><br>

    <!-- التقييمات -->
    <div class="rating-section">
        <h2>تقييمات الزوار</h2>

        <?php
        $ratings = $conn->query("SELECT * FROM ratings WHERE hall_id = $hall_id ORDER BY created_at DESC");

        if ($ratings->num_rows > 0) {
            while($rate = $ratings->fetch_assoc()) { ?>
            
                <div class="rating-card">
                    <div class="rating-header">
                        <strong><?= $rate['user_name'] ?></strong>
                        <span><?= $rate['created_at'] ?></span>
                    </div>
                    <p class="stars">⭐ <?= $rate['rating'] ?> / 5</p>
                    <p><?= $rate['comment'] ?></p>
                </div>

        <?php } } else { ?>
            <p>لا توجد تقييمات بعد.</p>
        <?php } ?>
    </div>

    <!-- إضافة تقييم -->
    <div class="add-rating-box">
        <h3>أضف تقييمك</h3>

        <form method="POST" class="rating-form">

            <label>التقييم:</label>
            <select name="rating" required>
                <option value="5">⭐⭐⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
                <option value="2">⭐⭐</option>
                <option value="1">⭐</option>
            </select>

            <label>تعليقك:</label>
            <textarea name="comment" required></textarea>

            <button type="submit" name="add_rating" class="pink-btn">إرسال التقييم</button>

        </form>
    </div>

</div>

</body>
</html>
