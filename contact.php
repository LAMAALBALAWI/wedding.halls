
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>تواصل معنا</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="content" style="max-width:800px; margin:auto; margin-top:40px;">

    <h2 style="color:#d63384;">تواصل معنا</h2>

    <?php if (!empty($success)): ?>
        <p style="color:green;"><?= $success ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

 <form action="https://formspree.io/f/mqedbgek" method="POST" style="margin-top:20px;">
        <label>الاسم</label>
        <input type="text" name="name" required style="width:100%; padding:10px; margin-bottom:10px;">

        <label>البريد الإلكتروني</label>
        <input type="email" name="email" required style="width:100%; padding:10px; margin-bottom:10px;">

        <label>الرسالة</label>
        <textarea name="message" required style="width:100%; padding:10px; height:120px; margin-bottom:10px;"></textarea>

        <button type="submit" class="btn" style="background:#d63384; color:white; padding:10px 20px; border:none; cursor:pointer;">
            إرسال
        </button>
    </form>
<div style="margin-top:20px;">
    <a href="index.php" style="
        background:#d63384;
        color:white;
        padding:10px 20px;
        text-decoration:none;
        border-radius:5px;
        display:inline-block;
    ">
        العودة للصفحة الرئيسية
    </a>
</div>

</div>

<?php include 'footer.php'; ?>

</body>
</html>
