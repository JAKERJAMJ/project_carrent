<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header>
<nav class="navbar bg-body-tertiary">
        <div class="container-fluid d-flex justify-content-between">
            <a class="navbar-brand" href="index.php">เมืองเลยรถเช่า</a>
            <div class="d-flex">
                <div class="me-3">
                    <a class="nav-link active text-dark" aria-current="page" href="show_car.php">รถยนต์ส่วนตัว</a>
                </div>
                <div class="me-3">
                    <a class="nav-link active text-dark" aria-current="page" href="show_packet.php">แพ็คเกจท่องเที่ยว</a>
                </div>
                <div class="me-3">
                    <a class="nav-link active text-dark" aria-current="page" href="about_us.php">เกี่ยวกับเรา</a>
                </div>
            </div>

            <div class="signinbtn">
            <?php if ($current_page != 'login.php' && $current_page != 'register.php') : ?>
                    <a href="login.php"><button type="button" class="btn btn-primary">SignIn</button></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>