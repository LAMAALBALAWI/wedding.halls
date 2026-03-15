<?php
include "connect.php";

if(isset($_POST['submit'])) {
    $hall_id = $_POST['hall_id'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $sql = "UPDATE halls SET image='".basename($_FILES["image"]["name"])."' WHERE id=$hall_id";
            $conn->query($sql);
            echo "تم رفع الصورة بنجاح";
        } else {
            echo "حدث خطأ أثناء رفع الصورة";
        }
    } else {
        echo "الملف ليس صورة صالحة";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>رفع صورة القاعة</title>
</head>
<body>
<h2>رفع صورة القاعة</h2>
<form action="" method="post" enctype="multipart/form-data">
<input type="number" name="hall_id" placeholder="رقم القاعة" required>
<input type="file" name="image" required>
<button type="submit" name="submit">رفع الصورة</button>
</form>
</body>
</html>