<?php
session_start();
require_once('db.php');
$ptrn = '/^[^<>\'\"]+$/';
if (!isset($_SESSION['userID'])) {
    header("location:index.php");
    exit();
}

$id = $_SESSION['userID'];
$sq = $db->prepare('SELECT * FROM user WHERE id = ?');
$sq->execute([$id]);
$row = $sq->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $nama = $row['username'];
    $email = $row['email'];
} else {
    echo "User tidak ditemukan.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
    $newUsername = $_POST['username'];
    if(!preg_match($ptrn, $newUsername)){
        $error = "Unexpected Input";
        header("location:index.php?error=$error");
        exit();
    }
    $updateQuery = $db->prepare("UPDATE user SET username = ? WHERE id = ?");
    $updateQuery->execute([$newUsername, $id]);

    $_SESSION['username'] = $newUsername;
    header("location:profile.php?success=Username updated successfully");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $stmt = $db->prepare('SELECT * FROM todolist WHERE uid = ?');
    $stmt->execute([$id]);

    while ($todolist = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $name = $todolist['name'];
        $sq = $db->query("DROP TABLE $name");
    }
    $sq = $db->query("DELETE FROM todolist WHERE uid = $id");

    $deleteQuery = $db->prepare('DELETE FROM user WHERE id = ?');
    $deleteQuery->execute([$id]);

    session_unset();
    session_destroy();
    header("location:index.php?success=Account deleted successfully");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div id="probung">
        <?php
        for ($i = 0; $i < 1500; $i++) {
            echo '<span class="back"></span>';
        }
        ?>

        <div id="edit">
            <div class="content">
                <a href="show.php"><i class="backs fs-4 fa-solid fa-arrow-left"></i></a>
                <div id="top" class="d-flex justify-content-end position-absolute">
                    <div class="reset">
                        <a href="LupaPass.php" class="btn btn-secondary"><span class="fas fa-refresh fa-1x" data-fa-mask="fas fa-lock" data-fa-transform="shrink-8 down-3"></span></a>
                    </div>
                    <div class="tooltip-container">
                        <form method="post" id="delete-form">
                            <button class="delete" type="button" class="btn btn-danger" id="delete-btn"><i class="fa-solid fa-trash"></i></button>
                            <input type="hidden" name="delete" value="1">
                        </form>
                    </div>
                </div>
                <h1>PROFILE</h1>
                <form method="post" action="" autocomplete="off">
                    <div>
                        <label class="user">Username</label>
                        <span id="username-display"><?php echo htmlspecialchars($nama); ?></span>
                        <input type="hidden" id="username-input" name="username" value="<?php echo htmlspecialchars($nama); ?>">
                    </div>
                    <div>
                        <label class="email">Email</label>
                        <span id="email-display"><?php echo htmlspecialchars($email); ?></span>
                        <input type="hidden" id="email-input" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    </div>
                    <button id="submit" type="submit" class="btn">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('username-display').addEventListener('click', function() {
            Swal.fire({
                title: 'Edit Username',
                input: 'text',
                inputValue: document.getElementById('username-input').value,
                showCancelButton: true,
                confirmButtonColor: '#6d8197',
                cancelButtonColor: '#bdc6d0',
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to write something!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('username-input').value = result.value;
                    document.getElementById('username-display').textContent = result.value;
                }
            });
        });

        document.getElementById('email-display').addEventListener('click', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Edit Email',
                text: 'Sorry, your email cannot be edited.',
                confirmButtonColor: '#6d8197',
                confirmButtonText: 'OK'
            });
        });

        document.getElementById('delete-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6d8197',
                cancelButtonColor: '#bdc6d0',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>