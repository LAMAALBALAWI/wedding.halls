<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'connect.php';
include 'header.php';
include 'sidebar.php';
?>

<div class="content">

    <!-- نموذج البحث -->
    <section class="search-form" style="margin-bottom:30px; text-align:center;">
        <form method="GET" action="index.php" style="display:inline-flex; gap:10px;">
            <input type="text" name="city" placeholder="ابحث باسم المدينة"
                   value="<?= isset($_GET['city']) ? $_GET['city'] : '' ?>"
                   style="padding:10px; width:250px; border-radius:6px; border:1px solid #ccc;">
            <button type="submit" 
                    style="background:#d63384; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer;">
                بحث
            </button>
        </form>
    </section>

    <h2 style="color:#d63384; margin-bottom:20px;">القاعات المتاحة</h2>

    <div class="halls-container" style="text-align:center;">

        <?php
        // استعلام القاعات
        $query = "SELECT * FROM halls WHERE 1=1";

        if (!empty($_GET['city'])) {
            $city = $conn->real_escape_string($_GET['city']);
            $query .= " AND city LIKE '%$city%'";
        }

        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {

            while ($hall = $result->fetch_assoc()) {

                // جلب أول صورة من مجلد القاعة
                $folder = "uploads/halls/" . $hall['hall_id'] . "/";
                $images = glob($folder . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);

                // إذا فيه صور نعرض أول صورة، إذا ما فيه نعرض صورة افتراضية
                $display_image = !empty($images) ? $images[0] : "no-image.png";
                ?>

                <div class="hall-card" style="
                    background:white;
                    border-radius:12px;
                    box-shadow:0 4px 12px rgba(0,0,0,0.1);
                    padding:15px;
                    margin-bottom:25px;
                    width:300px;
                    display:inline-block;
                    vertical-align:top;
                    margin-right:15px;
                    transition:0.3s;
                ">
                    <div style="overflow:hidden; border-radius:10px;">
                        <img src="<?= $display_image ?>" 
                             alt="صورة القاعة" 
                             style="width:100%; height:180px; object-fit:cover;">
                    </div>

                    <h3 style="margin:10px 0; color:#d63384; font-size:20px;">
                        <?= $hall['hall_name']; ?>
                    </h3>

                    <p style="color:#555; margin:5px 0 15px;">
                        <strong>المدينة:</strong> <?= $hall['city']; ?>
                    </p>

                    <a href="hall_details.php?hall_id=<?= $hall['hall_id']; ?>" 
                       style="
                            display:block;
                            background:#d63384;
                            color:white;
                            padding:10px;
                            text-align:center;
                            border-radius:6px;
                            text-decoration:none;
                            font-weight:bold;
                            transition:0.3s;
                       ">
                        عرض التفاصيل
                    </a>
                </div>

                <?php
            }

        } else {
            echo "<p style='color:#555;'>لا توجد قاعات في هذه المدينة</p>";
        }
        ?>

    </div>

<style>
.search-box {
    display:flex;
    gap:10px;
    margin-bottom:20px;
}
.search-box input {
    flex:1;
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
}
.search-box button {
    background:#d63384;
    color:white;
    padding:10px 20px;
    border-radius:8px;
    border:none;
}
</style>

</div>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
