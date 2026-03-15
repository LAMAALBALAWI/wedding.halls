<?php
include 'connect.php';
session_start();

$user_id = $_SESSION['user_id'];

// جلب قائمة الأشخاص اللي تواصلوا معي
$conversations = $conn->query("
    SELECT 
        m.*,
        u.name AS other_name
    FROM messages m
    JOIN users u 
        ON (CASE 
                WHEN m.sender_id = $user_id THEN m.receiver_id = u.user_id
                ELSE m.sender_id = u.user_id
            END)
    WHERE m.sender_id = $user_id OR m.receiver_id = $user_id
    GROUP BY 
        CASE 
            WHEN m.sender_id = $user_id THEN m.receiver_id
            ELSE m.sender_id
        END
    ORDER BY m.sent_at DESC
");
?>

<div class="content">
    <a href="index.php" class="back-btn">←</a>
<h2>قائمة المحادثات</h2>
    <div class="chat-list">

        <?php while ($row = $conversations->fetch_assoc()) { 

            // تحديد الطرف الآخر
            $other_id = ($row['sender_id'] == $user_id) 
                        ? $row['receiver_id'] 
                        : $row['sender_id'];
        ?>

        <a class="chat-item" href="chat.php?owner_id=<?= $other_id ?>">
            <div class="chat-name"><?= $row['other_name'] ?></div>
            <div class="chat-last"><?= $row['content'] ?></div>
            <div class="chat-time"><?= date('H:i', strtotime($row['sent_at'])) ?></div>
        </a>

        <?php } ?>

    </div>
</div>

<style>
.content {
    padding:20px;
}

h2 {
    margin-bottom:15px;
    color:#d63384;
}

.chat-list {
    display:flex;
    flex-direction:column;
    gap:10px;
}

.chat-item {
    display:flex;
    flex-direction:column;
    background:white;
    padding:12px;
    border-radius:10px;
    border:1px solid #f3c6dd;
    text-decoration:none;
    color:#333;
    position:relative;
    transition:0.2s;
}

.chat-item:hover {
    background:#ffeef7;
}

.chat-name {
    font-weight:bold;
    color:#b82a6f;
    margin-bottom:5px;
}

.chat-last {
    font-size:14px;
    color:#555;
}

.chat-time {
    position:absolute;
    top:10px;
    right:10px;
    font-size:12px;
    color:#777;
}
</style>
