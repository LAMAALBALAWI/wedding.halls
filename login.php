<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php';
include 'config.php'; // ← استدعاء الإيميل المخفي

// استدعاء PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

$error = "";

// إذا المستخدم مسجل دخول بالفعل
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// عند الضغط على تسجيل الدخول
if (isset($_POST['login'])) {

    if (!isset($_POST['agree'])) {
        $error = "يجب الموافقة على الشروط والأحكام قبل تسجيل الدخول.";
    } else {

        $username = trim($_POST['username']); 
        $password = $_POST['password'];

        // البحث عن المستخدم
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                // إنشاء كود تحقق
                $otp = rand(100000, 999999);

                $_SESSION['otp'] = $otp;
                $_SESSION['pending_user'] = $user['user_id'];

                // إرسال الكود
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $MAIL_USER;   // ← من config.php
                    $mail->Password   = $MAIL_PASS;   // ← من config.php
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom($MAIL_USER, 'Wedding Halls');
                    $mail->addAddress($user['email']);

                    $mail->isHTML(true);
                    $mail->Subject = 'رمز التحقق';
                    $mail->Body    = "رمز الدخول الخاص بك هو: <b>$otp</b>";

                    $mail->send();

                    header("Location: verify.php");
                    exit;

                } catch (Exception $e) {
                    $error = "تعذر إرسال رمز التحقق.";
                }

            } else {
                $error = "كلمة المرور غير صحيحة.";
            }

        } else {
            $error = "الحساب غير موجود.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>

    <style>
        body {
            font-family: 'Tahoma';
            background: #ffe6f2;
            padding: 40px;
        }

        .box {
            width: 380px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 0 15px #ffb6d9;
            border: 2px solid #ff99cc;
        }

        h2 {
            text-align: center;
            color: #d63384;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ff99cc;
            border-radius: 8px;
            background: #fff0f7;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #ff66b3;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: 0.3s;
        }

        button:disabled {
            background: #ffb3d9;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .terms {
            margin-top: 10px;
            font-size: 14px;
        }

        .terms a {
            color: #d63384;
            text-decoration: none;
            font-weight: bold;
        }

        .terms a:hover {
            text-decoration: underline;
        }

        .register {
            text-align: center;
            margin-top: 15px;
        }

        .register a {
            color: #d63384;
            text-decoration: none;
            font-weight: bold;
        }

        .register a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="box">
    <h2>تسجيل الدخول</h2>

    <?php if($error != ""): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="username" placeholder="البريد أو رقم الجوال" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>

        <div class="terms">
            <label>
                <input type="checkbox" id="agree" name="agree">
                أوافق على <a href="terms.php" target="_blank">الشروط والأحكام</a>
            </label>
        </div>

        <button type="submit" name="login" id="loginBtn" disabled>دخول</button>

    </form>

    <div class="register">
        ليس لديك حساب؟  
        <a href="register.php">إنشاء حساب جديد</a>
    </div>

</div>

<script>
    const agree = document.getElementById('agree');
    const loginBtn = document.getElementById('loginBtn');

    agree.addEventListener('change', function() {
        loginBtn.disabled = !this.checked;
    });
</script>

</body>
</html>
