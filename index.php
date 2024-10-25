<!DOCTYPE html>
<html lang="en">

<head>
    <title>PHP - Lab</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php
    session_start();
    require_once("db.php");
    if (isset($_SESSION['userID'])) {
        header("location:show.php");
    }
    if (isset($_GET['error'])) {
        echo "<div id='error'>Login Error: " . $_GET['error'] . "</div>";
    }
    if (isset($_GET['errorA'])) {
        echo "<div id='error'>Access Error: " . $_GET['errorA'] . "</div>";
    }
    if (isset($_GET['success'])) {
        echo "<div id='error'>" . $_GET['success'] . "</div>";
    }
    ?>
    <script type="text/javascript">
        function showPass1() {
            x = document.getElementById("pass1");
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

        // const inputs = document.querySelectorAll('input');

        // inputs.forEach(input => {
        // if (input.value.trim() !== '') {
        //     input.classList.add('valid');
        // }

        // input.addEventListener('input', function() {
        //     if (input.value.trim() !== '') {
        //         input.classList.add('valid');
        //     } else {
        //         input.classList.remove('valid');
        //     }
        // });
        // });

        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
    <div id="lobung">
        <?php
        for ($i = 0; $i < 1500; $i++) {
            echo '<span class="back"></span>';
        }
        ?>

        <div id="login">
            <div class="content">
                <h1>Login</h1>
                <form method="post" action="loginProcess.php" autocomplete="off">
                    <div class="logemail">
                        <div class="loin">
                            <input required type="text" pattern="[a-zA-Z\-_0-9]+@[a-zA-Z]+\.[a-zA-Z.]+" name="email" autocomplete="new-password">
                            <p>Email</p>
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                    </div>
                    <div class="logpass">
                        <div class="loin">
                            <input id="pass1" type="password" name="pass" required autocomplete="new-password">
                            <p>Password</p>
                            <span class='pass-icon' id="PassIcon" onclick="showPass1()">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <!-- Show Password <input onclick="showPass1()" type="checkbox"> -->
                    <div class="forget">
                        <a href="LupaPass.php">Lupa Password?</a>
                    </div>
                    <div>
                        <button class="login" type="submit">Login</button>
                    </div>
                    <div class="regis">
                        <p>Gak punya akun? <a href="registration.php"> Register</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>