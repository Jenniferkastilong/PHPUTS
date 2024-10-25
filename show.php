<!DOCTYPE html>
<html lang="en">

<head>
    <title>PHP - Lab</title>
    <link rel="stylesheet" href="show.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
        import Swal from 'sweetalert2';
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div>
        <?php
        session_start();
        // error_reporting(E_ALL ^ E_WARNING);
        require_once('db.php');
        if (!isset($_SESSION['userID'])) {
            $error = htmlspecialchars("No Access");
            header("location:index.php?errorA=$error");
        }
        if (time() - $_SESSION["loginTime"] > 7200) {
            session_unset();
            session_destroy();
            $error = htmlspecialchars("Session expired");
            header("location:index.php?errorA=$error");
        }
        if (isset($_GET['success'])) {
            $stype = $_GET['success'];
            $smsg = $stype == "edit" ? 'Edited' : ($stype == "delete" ? 'Deleted' : ($stype == "add" ? 'Added' : NULL));
            echo "<script>Swal.fire({
                title: '" .
                $smsg . " \""
                . $_GET['sname'] . "\" !',
                icon: 'success'
            });</script>";
        }
        if (isset($_POST['logout'])) {
            session_unset();
            session_destroy();
            $successMsg = htmlspecialchars("Logout Success");
            header("location:index.php?success=$successMsg");
        }
        if (isset($_GET['errorI'])) {
            echo "Error: " . $_GET['errorI'];
        }
        ?>
    </div>

    <div id="libung">
        <div id="nav">
            <nav class="navbar navbar-expand-lg navbar-light navbar-custom d-flex justify-content-between">
                <div class="container-fluid">
                    <h3 class="navbar-brand">PHP - LAB</h3>
                    <div class="d-flex justify-content-between">
                        <a class="btn me-2" href="profile.php">Profile</a>
                        <button class="btn  me-2" onclick="showAboutUs()">About Us</button>
                        <form method="post">
                            <button class="btn" type="submit" name="logout">Logout</button>
                        </form>
                    </div>
                </div>
        </div>
        </nav>
        <div>
            <div>
                <div>
                    <form method="post">
                        <input type='hidden' name="logout" value="1" />
                    </form>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <form method="get" id="order">
                            <select class="ms-3" name="ordering" onchange="document.getElementById('order').submit();">
                                Order By:
                                <option value="0" <?= (!isset($_GET['ordering']) || $_GET['ordering'] == 0) ? "selected" : NULL ?>>Default</option>
                                <option value="1" <?= (isset($_GET['ordering']) && $_GET['ordering'] == 1) ? "selected" : NULL ?>>Name ▲</option>
                                <option value="2" <?= (isset($_GET['ordering']) && $_GET['ordering'] == 2) ? "selected" : NULL ?>>Name ▼</option>
                                <option value="3" <?= (isset($_GET['ordering']) && $_GET['ordering'] == 3) ? "selected" : NULL ?>>Progress ▲</option>
                                <option value="4" <?= (isset($_GET['ordering']) && $_GET['ordering'] == 4) ? "selected" : NULL ?>>Progress ▼</option>
                            </select>
                        </form>
                    </div>
                    <div id="search">
                        <form method="get" id="srch">
                            <input type="text" pattern="[a-zA-Z_0-9\s]+" title="Only letter, number, underscore, or space" name="srch">
                            <button class="search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>
                </div>

                <div>
                    <div class="top">
                        <div>
                            <button id="add" class="text-center" onclick="addtdl()">Add Data</button>
                        </div>
                        <div class="ms-3 my-3 fs-3">To Do List:</div>
                    </div>

                    <div id="list">
                        <?php
                        $uid = $_SESSION['userID'];
                        $query = "SELECT * FROM todolist WHERE uid = $uid";
                        if (isset($_GET['srch'])) {
                            $search = $_GET['srch'];
                            $search = str_replace(' ', '_', $search);
                            $query .= " AND name LIKE '%$search%'";
                        }
                        if (isset($_GET['ordering'])) {
                            $order = $_GET['ordering'];
                            if ($order == 1) $query .= " ORDER BY name";
                            if ($order == 2) $query .= " ORDER BY name DESC";
                            if ($order == 3) $query .= " ORDER BY (completed/total)";
                            if ($order == 4) $query .= " ORDER BY (completed/total) DESC";
                        }
                        $sq = $db->query($query);
                        while ($data = $sq->fetch(PDO::FETCH_ASSOC)) {
                            $did = $data['id'];
                            $dname = $data['name'];
                            $dname2 = str_replace('_', ' ', $dname);
                            $completed = $data['completed'];
                            $total = $data['total'];
                            $compStyle = "style=\"background-image:linear-gradient(to right, ";
                            if ($data['total'] == 0) {
                                $compStyle .= "#bdc6d0 , #bdc6d0);\"";
                            } else {
                                $cStatus = $cStatus2 = floor($completed / $total * 100);
                                if ($cStatus != 100) $cStatus = $cStatus - 1.5 >= 0 ? $cStatus - 1.5 : 0;
                                if ($cStatus2 != 0) $cStatus2 = $cStatus2 + 1.5 <= 100 ? $cStatus2 + 1.5 : 100;
                                $compStyle .= "#6d8197 0%, #6d8197 $cStatus%, #d1d7df $cStatus2%, #d1d7df 100%);\"";
                            }
                            echo "<div class='d-flex justify-content-center'>
                        <button $compStyle class='listname' onclick='location.href=\"./detail.php?detail=$dname\"'>$dname2 ($completed/$total)</button>
                        <button class='edit' onclick=\"change($did,'$dname')\"><i class='fa-solid fa-pen-to-square'></i></button>
                        <button class='delete' onclick=\"del($did, '$dname')\"><i class='fa-solid fa-xmark'></i></button>
                        </div><br/>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addtdl() {
            Swal.fire({
                title: "Add todolist",
                html: `
                <form id="anama" action="prosesdata.php" method="post">
                <input name="aname" class="swal2-input">
                </form>
                `,
                showCancelButton: true,
                confirmButtonColor: "#6d8197",
                cancelButtonColor: "#bdc6d0",
                confirmButtonText: "Add"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("anama").submit();
                }
            });
        }

        function change(did, dnama) {
            Swal.fire({
                title: "Edit Name",
                html: `
                <form id="cnama" action="prosesdata.php" method="post">
                <input type="hidden" name="cid" value=` + did + `>
                <input name="cname" class="swal2-input" value=` + dnama + `>
                </form>
                `,
                showCancelButton: true,
                confirmButtonColor: "#6d8197",
                cancelButtonColor: "#bdc6d0",
                confirmButtonText: "Edit"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("cnama").submit();
                }
            });
        }

        function del(did, dnama) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                html: `
                <form id="dnama" action="prosesdata.php" method="post">
                <input type="hidden" name="did" value=` + did + `>
                <input name="dname" class="swal2-input" value=` + dnama + ` readonly>
                </form>
                `,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#6d8197",
                cancelButtonColor: "#bdc6d0",
                confirmButtonText: "Delete"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("dnama").submit();
                }
            });
        }

        function showAboutUs() {
            Swal.fire({
                title: 'About Us',
                html: `
                <div class='all'>
                    <h3>PHP Members</h3>
                    <ul class='us d-flex mt-5'>
                        <div class='d-flex justify-content-between'><div><p><strong>Name:</strong> Aditya Hiro Egawa<br><strong>NIM:</strong> 00000046971<br></div><div><img src="assets/hiro.jpeg" alt="Hiro" width="100"></p></div></div>
                        <div class='d-flex justify-content-between'><div><p><strong>Name:</strong> Davin Dick<br><strong>NIM:</strong> 00000108484<br></div><div><img src="assets/davin.JPG" alt="Davin" width="100"></p></div></div>
                        <div class='d-flex justify-content-between'><div><p><strong>Name:</strong> Jennifer Maria Daniella Kastilong<br><strong>NIM:</strong> 00000106205<br></div><div><img src="assets/jennifer.JPG" alt="Jennifer" width="100"></p></div></div>
                        <div class='d-flex justify-content-between'><div><p><strong>Name:</strong> Steven Lee<br><strong>NIM:</strong> 00000105886<br></div><div><img src="assets/steven.JPG" alt="Steven" width="100"></p></div></div>
                    </ul>
                </div>
            `,
                showCloseButton: true,
                confirmButtonColor: '#6d8197',
                confirmButtonText: 'Close'
            });
        }
    </script>

</body>

</html>