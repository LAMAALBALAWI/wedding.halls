<?php
session_start();
include 'connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    // التحقق من تطابق كلمة المرور
    if ($password !== $confirm) {
        $error = "تأكيد كلمة المرور لا يطابق.";
    } else {

        // التحقق من عدم وجود حساب مسبق
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR phone = ? LIMIT 1");
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "البريد الإلكتروني أو رقم الجوال مستخدم بالفعل.";
        } else {

            // تشفير كلمة المرور
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // إدخال المستخدم كبائع is_seller = 1
            $stmt = $conn->prepare("
                INSERT INTO users (name, email, phone, password, is_seller)
                VALUES (?, ?, ?, ?, 1)
            ");
            $stmt->bind_param("ssss", $name, $email, $phone, $hash);

            if ($stmt->execute()) {
                $success = "تم إنشاء الحساب  بنجاح!";
            } else {
                $error = "حدث خطأ أثناء إنشاء الحساب.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إنشاء حساب </title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
*{box-sizing:border-box;margin:0;padding:0;}

body{
    font-family:'Segoe UI',Tahoma,sans-serif;
    background:#fff0f5;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    padding:20px;
}

.container{
    background:#fff;
    width:100%;
    max-width:420px;
    border-radius:12px;
    box-shadow:0 4px 16px rgba(0,0,0,0.1);
    padding:30px 25px;
    text-align:center;
}

h3{
    color:#d63384;
    margin-bottom:20px;
}

input{
    width:100%;
    padding:10px;
    margin:8px 0 15px;
    border-radius:8px;
    border:1px solid #ddd;
}

.btn{
    display:block;
    width:100%;
    padding:12px;
    background:#d63384;
    color:white;
    border:none;
    border-radius:8px;
    font-size:16px;
    margin-top:10px;
    cursor:pointer;
}

.btn:hover{
    background:#b82a6f;
}

.message{
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
    font-size:14px;
}

.error{background:#fdecea;color:#b71c1c;border:1px solid #f5c6cb;}
.success{background:#e6f4ea;color:#256029;border:1px solid #c3e6cb;}

.back-btn{
    display:inline-block;
    margin-bottom:15px;
    color:#333;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="container">

    <a href="login.php" class="back-btn">← رجوع</a>

    <h3>إنشاء حساب </h3>

    <?php if ($error): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
        <a href="login.php" class="btn">تسجيل الدخول</a>
    <?php else: ?>

    <form method="POST">

        <input type="text" name="name" placeholder="الاسم " required>

        <input type="email" name="email" placeholder="البريد الإلكتروني" required>

        <input type="text" name="phone" placeholder="رقم الجوال" required>

        <input type="password" name="password" placeholder="كلمة المرور" required>

        <input type="password" name="confirm" placeholder="تأكيد كلمة المرور" required>

        <button type="submit" class="btn">إنشاء الحساب</button>

    </form>

    <?php endif; ?>

</div>

</body>
</html>
