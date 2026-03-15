<?php
session_start();
include 'connect.php';

// التحقق من وجود owner_id
if (!isset($_GET['owner_id']) || $_GET['owner_id'] === '') {
    die("خطأ: لا يوجد owner_id في الرابط.");
}

$user_id = $_SESSION['user_id'];
$owner_id = intval($_GET['owner_id']); // حماية + تحويل رقم

// تعليم الرسائل كمقروءة
$mark = $conn->prepare("
    UPDATE messages 
    SET is_read = 1 
    WHERE sender_id = ? AND receiver_id = ?
");
$mark->bind_param("ii", $owner_id, $user_id);
$mark->execute();

// إرسال رسالة
if (isset($_POST['send']) && isset($_POST['message']) && $_POST['message'] !== '') {

    $content = $conn->real_escape_string($_POST['message']);

    $sender_id = $user_id;
    $receiver_id = $owner_id;
    $subject = "";

    $conn->query("
        INSERT INTO messages (sender_id, receiver_id, subject, content, sent_at)
        VALUES ('$sender_id', '$receiver_id', '$subject', '$content', NOW())
    ");
}

// جلب الرسائل
$messages = $conn->query("
    SELECT * FROM messages
    WHERE (sender_id = $user_id AND receiver_id = $owner_id)
       OR (sender_id = $owner_id AND receiver_id = $user_id)
    ORDER BY sent_at ASC
");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>المحادثة</title>

<style>
.content {
    max-width: 700px;
    margin: auto;
    margin-top: 30px;
}

.back-btn {
    font-size: 22px;
    text-decoration: none;
    color: #d63384;
    margin-bottom: 10px;
    display: inline-block;
}

.chat-box {
    background: #f7f7f7;
    padding: 15px;
    border-radius: 10px;
    height: 450px;
    overflow-y: auto;
    border: 1px solid #ddd;
}

.date-divider {
    text-align: center;
    margin: 15px 0;
    color: #777;
    font-size: 13px;
}

.msg {
    max-width: 70%;
    padding: 10px 14px;
    border-radius: 10px;
    margin-bottom: 10px;
    position: relative;
    font-size: 15px;
}

.msg.me {
    background: #d63384;
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 0;
}

.msg.other {
    background: #ffffff;
    color: #333;
    border: 1px solid #ddd;
    margin-right: auto;
    border-bottom-left-radius: 0;
}

.msg-time {
    font-size: 11px;
    color: #eee;
    margin-top: 5px;
    text-align: right;
}

.msg.other .msg-time {
    color: #999;
}

.send-box {
    display: flex;
    margin-top: 15px;
}

.send-box input {
    flex: 1;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}

.send-box button {
    background: #d63384;
    color: white;
    border: none;
    padding: 12px 18px;
    margin-right: 10px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
}

.send-box button:hover {
    background: #b82a6f;
}
</style>

</head>
<body>

<div class="content">

    <a href="index.php" class="back-btn">←</a>

    <h2>المحادثة</h2>

    <div class="chat-box" id="chatBox">

        <?php
        $last_date = "";
        while ($row = $messages->fetch_assoc()) {

            $msg_date = date('Y-m-d', strtotime($row['sent_at']));

            if ($msg_date != $last_date) {
                echo "<div class='date-divider'>$msg_date</div>";
                $last_date = $msg_date;
            }

            $class = ($row['sender_id'] == $user_id) ? "me" : "other";
        ?>

        <div class="msg <?= $class ?>">
            <?= htmlspecialchars($row['content']) ?>
            <div class="msg-time"><?= date('H:i', strtotime($row['sent_at'])) ?></div>
        </div>

        <?php } ?>

    </div>

    <form method="POST" class="send-box">
        <input type="text" name="message" placeholder="اكتب رسالتك..." required>
        <button type="submit" name="send">إرسال</button>
    </form>
</div>

<script>
// سحب تلقائي لأسفل المحادثة
var chatBox = document.getElementById("chatBox");
chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>
