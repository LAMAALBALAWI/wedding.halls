<?php
session_start();

if(!isset($_SESSION['otp'])){
    header("Location: login.php");
    exit;
}

$error = "";

if(isset($_POST['verify'])){
    $code = $_POST['code'];

    if($code == $_SESSION['otp']){
        
        // تسجيل الدخول النهائي
        $_SESSION['user_id'] = $_SESSION['pending_user'];

        // حذف بيانات التحقق
        unset($_SESSION['otp']);
        unset($_SESSION['pending_user']);

        header("Location: index.php");
        exit;

    } else {
        $error = "رمز التحقق غير صحيح.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>التحقق من الرمز</title>
<style>
body{
    font-family:'Segoe UI';
    background:#fff0f5;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.box{
    background:white;
    padding:25px;
    border-radius:12px;
    width:350px;
    text-align:center;
    box-shadow:0 4px 16px rgba(0,0,0,0.1);
}
input{
    width:100%;
    padding:10px;
    margin-top:10px;
    border-radius:8px;
    border:1px solid #ddd;
}
.btn{
    margin-top:15px;
    width:100%;
    padding:12px;
    background:#d63384;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
}
.error{
    background:#fdecea;
    color:#b71c1c;
    padding:10px;
    border-radius:8px;
    margin-bottom:10px;
}
</style>
</head>

<body>

<div class="box">
    <h3>أدخل رمز التحقق</h3>

    <?php if($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="code" placeholder="أدخل الرمز" required>
        <button class="btn" name="verify">تأكيد</button>
    </form>
</div>

</body>
</html>
