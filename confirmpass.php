<?php
session_start();
require_once("db.php");

$ptrn = '/^[^<>\'\"]+$/';

if (!isset($_GET['code']) && !(isset($_POST['pass']) && $_SESSION['verfied'] == 1)){
    header("Location: index.php");
}
    if(isset($_GET['code'])){
    $code = $_GET['code'];
    if($code == $_SESSION['verif']) $_SESSION['verified'] = 1;
    else $_SESSION['verified'] = 0;
    }
    $email = $_SESSION['vemail'];
    $sql = "SELECT * FROM user WHERE email = '$email'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($user) {

        if (isset($_POST['pass'])) {
            $newPass = $_POST['pass'];
            if(!preg_match($ptrn, $newPass)){
                header("Refresh: 0;");
                exit();
            }
            $hashedPassword = password_hash($newPass, PASSWORD_BCRYPT);

            $updateQuery = "UPDATE user SET pass = ? WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$hashedPassword,  $user['id']]);

            header("Location: index.php?success=Password has been updated!");

            if (isset($_SESSION['userID'])) {
                header("location:profile.php");
                exit();
            }else{
                header("location:index.php");
                exit();
            }
            // exit();
        }
    }


?>
<html>

<head>
    <link rel="stylesheet" href="confirmpass.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <script type="text/javascript">
        function showPass2() {
            var x = document.getElementById("pass2");
            var i = document.getElementById("PassIcon").querySelector("i");
            if (x.type === "password") {
                x.type = "text";
                i.classList.remove("fa-eye");
                i.classList.add("fa-eye-slash")
            } else {
                x.type = "password";
                i.classList.remove("fa-eye-slash");
                i.classList.add("fa-eye");
            }
        }
    </script>
    <div id="confibung">
        <?php
        for ($i = 0; $i < 1500; $i++) {
            echo '<span class="back"></span>';
        }
        ?>
        
        <div class="container">
            <div class="form-box">
                <h1>Confirm Password</h1>
                <form method="post" action="" autocomplete="off">
                    <div class="regpass">
                        <div class="regin">
                            <input id="pass2" type="password" pattern="^[^\s<>]+$" name="pass" required autocomplete="new-password">
                            <span class='pass-icon' id="PassIcon" onclick="showPass2()">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                            <p>New Password</p>
                        </div>
                    </div>
                    <div>
                        <button class="submit-btn" type="submit">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>