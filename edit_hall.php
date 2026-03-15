<?php
include 'connect.php';

$hall_id = $_GET['id'];

// جلب بيانات القاعة
$sql = "SELECT * FROM halls WHERE hall_id=$hall_id";
$result = mysqli_query($conn, $sql);
$hall = mysqli_fetch_assoc($result);

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name        = mysqli_real_escape_string($conn, $_POST['hall_name']);
    $capacity    = (int) $_POST['capacity'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // الصورة الحالية
    $current_image = $hall['image'];

    // هل رفع صورة جديدة؟
    if (!empty($_FILES['image']['name'])) {

        // اسم جديد للصورة مع المسار الصحيح
        $new_image = 'uploads/' . time() . '_' . basename($_FILES['image']['name']);

        // رفع الصورة
        move_uploaded_file($_FILES['image']['tmp_name'], $new_image);

        // استبدال الصورة القديمة بالجديدة
        $image_to_save = $new_image;

    } else {
        // لو ما رفع صورة جديدة، نخلي القديمة
        $image_to_save = $current_image;
    }

    // تحديث البيانات
    $update = "
        UPDATE halls 
        SET hall_name='$name',
            capacity='$capacity',
            description='$description',
            image='$image_to_save'
        WHERE hall_id=$hall_id
    ";

    mysqli_query($conn, $update);

    header("Location: view_halls.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>تعديل القاعة</title>
<link rel="stylesheet" href="style.css">
<style>
.edit-box {
    max-width: 600px;
    margin: 40px auto;
    background: #ffeef5;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.edit-box img {
    width: 100%;
    max-height: 250px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 15px;
}
</style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="edit-box">
<h2>تعديل القاعة</h2>

<!-- عرض الصورة الحالية -->
<?php if (!empty($hall['image'])): ?>
    <img src="<?= $hall['image'] ?>" alt="صورة القاعة">
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <label>اسم القاعة:</label>
    <input type="text" name="hall_name" value="<?= $hall['hall_name']; ?>" required>

    <label>السعة:</label>
    <input type="number" name="capacity" value="<?= $hall['capacity']; ?>" required>

    <label>الوصف:</label>
    <textarea name="description" rows="4"><?= $hall['description']; ?></textarea>

    <label>تغيير الصورة (اختياري):</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit">تحديث</button>
</form>
</div>

</body>
</html>
