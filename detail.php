<!DOCTYPE html>
<html lang="en">

<head>
    <title>PHP - Lab</title>
    <link rel="stylesheet" href="detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
        import Swal from 'sweetalert2';
    </script>
</head>

<body>
    <script>
        function addtdl(detail) {
            Swal.fire({
                title: "Add todolist",
                html: `
                <form id="anama" action="prosesdata.php" method="post">
                <input type='hidden' name='mname' value='` + detail + `'>
                <input name="aname2" class="swal2-input">
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

        function change(did, dnama, detail) {
            Swal.fire({
                title: "Edit Name",
                html: `
                <form id="cnama" action="prosesdata.php" method="post">
                <input type="hidden" name="cid2" value=` + did + `>
                <input type='hidden' name='mname' value='` + detail + `'>
                <input name="cname2" class="swal2-input" value=` + dnama + `>
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

        function del(did, dnama, detail, comp) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                html: `
                <form id="dnama" action="prosesdata.php" method="post">
                <input type="hidden" name="did2" value=` + did + `>
                <input type='hidden' name='mname' value='` + detail + `'>
                <input type='hidden' name='comp' value='` + comp + `'>
                <input name="dname2" class="swal2-input" value=` + dnama + ` readonly>
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

        function mark(did) {
            console.log(did);
            document.getElementById(did).submit();
        }
    </script>
    <?php
    session_start(); 
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
    if (!isset($_GET['detail'])) {
        header('Location:show.php');
    }


    ?>
    <div>
        <div id="debung">
            <a href="show.php"><i class="back fs-4 fa-solid fa-arrow-left"></i></a>
            <div id='detail'>
                <div class="text-center">
                    <h1><?= str_replace('_', ' ', $_GET['detail']) ?></h1>
                </div>
                <div>
                    <button class="add" onclick="addtdl('<?= $_GET['detail'] ?>')">Add Task</button>
                </div>
                <div class="bung d-flex justify-content-between">
                    <div>
                        <form method="get" id="order2">
                            <select class="order" name="ordering" onchange="document.getElementById('order2').submit();">
                                Order By:
                                <option value="0" <?= (!isset($_GET['ordering']) || $_GET['ordering'] == 0) ? "selected" : NULL ?>>All</option>
                                <option value="1" <?= (isset($_GET['ordering']) && $_GET['ordering'] == 1) ? "selected" : NULL ?>>Not Completed</option>
                                <option value="2" <?= (isset($_GET['ordering']) && $_GET['ordering'] == 2) ? "selected" : NULL ?>>Completed</option>
                            </select>
                            <input type="hidden" name="detail" value="<?= $_GET['detail'] ?>">
                        </form>
                    </div>
                    <div id="search">
                        <form method="get" id="srch">
                            <input type="text" pattern="[a-zA-Z_0-9\s]+" title="Only letter, number, underscore, or space" name="srch">
                            <input type="hidden" name="detail" value="<?= $_GET['detail'] ?>">
                            <button class="search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>
                    <div>
                    </div>
                </div>
                <?php
                $detail = $_GET['detail'];
                $uid = $_SESSION['userID'];
                $query = "SELECT * FROM $detail WHERE uid = $uid";
                if (isset($_GET['srch'])) {
                    $search = $_GET['srch'];
                    $search = str_replace(' ', '_', $search);
                    $query .= " AND name LIKE '%$search%'"; 
                }
                if (isset($_GET['ordering'])) {
                    $order = $_GET['ordering'];
                    if ($order == 1) $query .= " AND completion=0";
                    if ($order == 2) $query .= " AND completion=1";
                }
                $sq = $db->query($query);
                while ($data = $sq->fetch(PDO::FETCH_ASSOC)) {
                    $did = $data['id'];
                    $dname = $dnameh = $data['name'];
                    $dname = str_replace('_', ' ', $dname);
                    $comp = $data['completion'];
                    if ($comp == 1) $dname = "<s>$dname</s>";
                    echo "
                            <div class='data d-flex justify-content-between'>
                                <form id='mnama$did' action='prosesdata.php' method='post'>
                                <input type='hidden' name='mid' value='$did'>
                                <input type='hidden' name='mname' value='$detail'>
                                <input type='hidden' name='mcomp' value='$comp'>
                                </form>
                                <button class='listname' onclick=\"mark('mnama$did')\">$dname</button>
                                <button class='edit' onclick=\"change($did,'$dnameh','$detail')\"><i class='fa-solid fa-pen-to-square'></i></button>
                                <button class='delete' onclick=\"del($did,'$dnameh','$detail',$comp)\"><i class='fa-solid fa-xmark'></i></button>
                            </div><br/>
                            ";
                }
                ?>
            </div>
        </div>
    </div>
    </div>
</body>

</html>