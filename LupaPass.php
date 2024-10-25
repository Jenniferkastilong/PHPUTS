<html>

<head>
    <title>PHP - Lab</title>
    <link rel="stylesheet" href="LupaPass.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php
    session_start();
    require_once("db.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    if (isset($_GET['error'])) {
        echo "Login Error: " . $_GET['error'];
    }
    if (isset($_GET['errorA'])) {
        echo "Access Error: " . $_GET['errorA'];
    }
    if (isset($_GET['success'])) {
        echo $_GET['success'];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cekmail = $_POST['email'] ?? null;


        $sql = "SELECT email, username FROM user WHERE email = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$cekmail]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $mail = $row['email'];
            $nama = $row['username'];
            $code = random_bytes(16);
            $code = bin2hex($code);
            $code = strtoupper(base_convert($code, 16, 36));
            $_SESSION['verif'] = $code;
            $_SESSION['vemail'] = $row['email'];


            require './PHPMailer/src/Exception.php';
            require './PHPMailer/src/PHPMailer.php';
            require './PHPMailer/src/SMTP.php';

            $email = new PHPMailer();

            $email->isSMTP();
            $email->SMTPDebug = SMTP::DEBUG_OFF;
            $email->Host = 'smtp.gmail.com';
            $email->Port = 465;
            $email->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $email->SMTPAuth = true;


            $email->Username = 'webprognoreply@gmail.com';
            $email->Password = 'nshm fzsz nfrj ucmy';
            $email->setFrom('no-reply@php-lab.com', 'PHP - Lab');
            $email->addAddress($mail, $nama);

            $email->Subject = 'Reset Password PHP - Lab';
            $body = "Halo, " . $nama . "<br />Jika ini bukan dirimu maka abaikan email ini. <br />"
                . "Klik link berikut untuk reset password: "
                . "<a href='http://localhost/wwUtsLab/confirmpass.php?code=" . $code . "'>Reset Password</a>";
            $email->Body = $body;
            $email->isHTML(true);
            $email->AltBody = 'Silakan verifikasi email: http://localhost/wwUtsLab/confirmpass.php?code=' . $code;

            if ($email->send()) {
                $successMsg = "<div id='error'>Reset link has been sent! Please check your email.</div>";
                header("location: LupaPass.php?success=$successMsg");
                exit();
            } else {
                echo "<div id='error'>Email gagal dikirim: " . $email->ErrorInfo . "</div>";
            }
        } else {
            echo "<div id='error'>Email tidak ditemukan. Pastikan email yang Anda masukkan sudah benar.</div>";
        }
    }
    ?>

    <div id="forbung">
        <?php
        for ($i = 0; $i < 1500; $i++) {
            echo '<span class="back"></span>';
        }
        ?>
        <div class="container">
            <div class="form-box">
                <h1>Reset Password</h1>
                <form method="post" action="" autocomplete="off">
                    <div class="input-group">
                        <div class="forget">
                            <input required type="email" pattern="[a-zA-Z\-_0-9]+@[a-zA-Z]+\.[a-zA-Z\.]+" name="email" tocomplete="new-password">
                            <p>Email</p>
                            <i class="email-icon fa-solid fa-envelope"></i>
                        </div>
                    </div>
                    <div>
                        <button class="submit-btn" type="submit">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('input');

        inputs.forEach(input => {
            if (input.value.trim() !== '') {
                input.classList.add('valid');
            }

            input.addEventListener('input', function() {
                if (input.value.trim() !== '') {
                    input.classList.add('valid');
                } else {
                    input.classList.remove('valid');
                }
            });
        });
    </script>
</body>

</html>