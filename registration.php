<!DOCTYPE html>
<html lang="en">

<head>
    <title>PHP - Lab</title>
    <link rel="stylesheet" href="registration.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <?php
    session_start();
    require_once("db.php");
    $ptrn = '/^[^<>\'\"]+$/';


    if (isset($_SESSION['userID'])) {
        header("location:show.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mail = $_POST['email'] ?? null;
        $nama = $_POST['username'] ?? null;
        $password = $_POST['pass'] ?? null;

        if (!$mail || !$nama || !$password) {
            echo "Data tidak lengkap. Silakan isi form dengan benar.";
            exit();
        }

        if (!preg_match($ptrn, $nama) || !preg_match($ptrn, $mail) || !preg_match($ptrn, $_POST['pass'])) {
            $error = "Unexpected Input";
            header("Location: index.php?error=$error");
            exit();
        }

        $sql = "SELECT * FROM user WHERE email = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$mail]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            echo "Email sudah terdaftar.";
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $insertQuery = "INSERT INTO user (username, email, pass) VALUES (?, ?, ?)";
        $stmt = $db->prepare($insertQuery);
        $stmt->execute([$nama, $mail, $hashedPassword]);
        $successMsg = "Register Success";
        header("Location:index.php?success=$successMsg");
    }
    ?>

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

        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('input');

            inputs.forEach(input => {
                if (input.value.trim() !== '') {
                    input.classList.add('valid');
                }

                input.addEventListener('input', function () {
                    if (input.value.trim() !== '') {
                        input.classList.add('valid');
                    } else {
                        input.classList.remove('valid');
                    }
                });
            });
        });
    </script>

    <div id="rebung">
        <?php
        for ($i = 0; $i < 1500; $i++) {
            echo '<span class="back"></span>';
        }
        ?>

        <div id="regis">
            <div class="content">
                <h1>Register</h1>
                <form method="post" action="" autocomplete="off">
                    <div class="reguser">
                        <div class="regin">
                            <input required type="text" pattern="[a-zA-Z_0-9\s]+"
                                title="Only letter, number, underscore, or space" name="username"
                                autocomplete="new-password">
                            <p>Username</p>
                            <i class="user-icon fa-solid fa-user"></i>
                        </div>
                    </div>
                    <div class="regemail">
                        <div class="regin">
                            <input required type="email" pattern="[a-zA-Z\-_0-9]+@[a-zA-Z]+\.[a-zA-Z\.]+" name="email"
                                tocomplete="new-password">
                            <p>Email</p>
                            <i class="email-icon fa-solid fa-envelope"></i>
                        </div>
                    </div>
                    <div class="regpass">
                        <div class="regin">
                            <input id="pass2" type="password" name="pass" required autocomplete="new-password">
                            <span class='pass-icon' id="PassIcon" onclick="showPass2()">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                            <p>Password</p>
                        </div>
                    </div>
                    <div>
                        <button class="regis" type="submit">Registration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>