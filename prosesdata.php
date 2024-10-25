<?php
require_once "db.php";
session_start();
$ptrn = '/^[^<>\'\".,]+$/';
if (isset($_POST['did'])) {
    $sid = $_POST['did'];
    $sq = $db->query("DELETE FROM todolist WHERE id=$sid");
    $sname = $_POST['dname'];
    $sname = str_replace(' ', '_', $sname);
    $que = "DROP TABLE $sname";
    $sq = $db->query($que);
    $successMsg = "success=delete&sname=$sname";
    header("location: show.php?$successMsg");
}
if (isset($_POST['cid'])) {
    $sid = $_POST['cid'];
    $sname = $_POST['cname'];
    $sname = str_replace(' ', '_', $sname);
    if(preg_match($ptrn, $sname) == 0){
        $errorMsg= "Unexpected Input";
        header("location: show.php?errorI=$errorMsg");
        exit();
    }
    $sq = $db->query("SELECT name FROM todolist WHERE id=$sid");
    $row = $sq->fetch(PDO::FETCH_COLUMN);
    $pname = $row;
    $pname = str_replace(' ', '_', $pname);
    $que = "ALTER TABLE $pname RENAME $sname";
    $sq = $db->query($que);
    $que = "UPDATE todolist SET name='$sname' WHERE id=$sid";
    $sq = $db->query($que);
    
    $successMsg = "success=edit&sname=$sname";
    header("location: show.php?$successMsg");
}
if (isset($_POST['aname'])) {
    $sname = $_POST['aname'];
    $sname = str_replace(" ", "_", $sname);
    if(preg_match($ptrn, $sname) == 0){
        $errorMsg= "Unexpected Input";
        header("location: show.php?errorI=$errorMsg");
        exit();
    }
    $que = "SELECT * FROM todolist WHERE name='$sname'";
    $sq = $db->prepare($que);
    $sq->execute();
    $row = $sq->fetch(PDO::FETCH_ASSOC);
    if($row){
        $errorMsg = htmlspecialchars("Nama to do list sudah ada");
        header("location: show.php?errorI=$errorMsg");
        exit();
    }
    $uid = $_SESSION['userID'];
    $que = "INSERT INTO todolist(name, uid) VALUES ('$sname' , $uid)";
    $sq = $db->query($que);
    $que = "CREATE TABLE $sname (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(30), completion INT, uid INT(5))";
    $sq = $db->query($que);
    $successMsg = "success=add&sname=$sname";
    header("location: show.php?$successMsg");
}

if(isset($_POST['mid'])){
    $id = $_POST['mid'];
    $name = $_POST['mname'];
    $name = str_replace(' ', '_', $name);
    $comp = $_POST['mcomp'];
    $ncomp;
    if($comp == 0) {
        $ncomp = 1;
        $que = "UPDATE todolist SET completed = completed+1 WHERE name = '$name'"; 
        $sq = $db->query($que);
    } 
    else {
        $ncomp = 0;
        $que = "UPDATE todolist SET completed = completed-1 WHERE name = '$name'"; 
        $sq = $db->query($que);
    }
    $que = "UPDATE $name SET completion = $ncomp WHERE id = $id";
    $sq = $db->query($que);
    header("location: detail.php?detail=$name");
}
if (isset($_POST['did2'])) {
    $sid = $_POST['did2'];
    $name = $_POST['mname'];
    $name = str_replace(' ', '_', $name);
    $comp = $_POST['comp'];
    $que = "DELETE FROM $name WHERE id=$sid";
    $sq = $db->query($que);
    $que = "UPDATE todolist SET total = total-1 WHERE name = '$name'";
    $sq = $db->query($que);
    if($comp == 1){
        $que = "UPDATE todolist SET completed = completed-1 WHERE name = '$name'";
        $sq = $db->query($que);
    }
    $sname = $_POST['dname2'];
    header("location: detail.php?detail=$name");
}
if (isset($_POST['cid2'])) {
    $sid = $_POST['cid2'];
    $sname = $_POST['cname2'];
    $sname = str_replace(' ', '_', $sname);
    if(preg_match($ptrn, $sname) == 0){
        $errorMsg= "Unexpected Input";
        header("location: show.php?errorI=$errorMsg");
        exit();
    }
    $name = $_POST['mname'];
    $name = str_replace(' ', '_', $name);
    $que = "UPDATE $name SET name='$sname' WHERE id=$sid";
    $sq = $db->query($que);
    header("location: detail.php?detail=$name");
}
if (isset($_POST['aname2'])) {
    $uid = $_SESSION['userID'];
    $sname = $_POST['aname2'];
    $sname = str_replace(' ', '_', $sname);
    if(preg_match($ptrn, $sname) == 0){
        $errorMsg= "Unexpected Input";
        header("location: show.php?errorI=$errorMsg");
        exit();
    }
    $name = $_POST['mname'];
    $name = str_replace(' ', '_', $name);
    $que = "INSERT INTO $name (name , completion, uid) VALUES ('$sname' , 0, $uid)";
    $sq = $db->query($que);
    $que = "UPDATE todolist SET total = total+1 WHERE name = '$name'";
    $sq = $db->query($que);
    header("location: detail.php?detail=$name");
}