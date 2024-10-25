<?php
    session_start();
    require_once('db.php');
    $email = htmlspecialchars($_POST['email']);
    $pass = htmlspecialchars($_POST['pass']);
    $sq = $db->prepare('SELECT * FROM user WHERE email=?');
    $sq->execute([$email]);
    $row = $sq->fetch(PDO::FETCH_ASSOC);
    if(!$row){
        $error = htmlspecialchars("Email not found");
        header("location: index.php?error=$error");
    }
    else{
        if(!password_verify($pass,$row['pass'])){
            $error = htmlspecialchars("Wrong password");
            header("location: index.php?error=$error");
        }
        else{
            $_SESSION['userID'] = $row['id'];
            $_SESSION['loginTime'] = time();
            header("location: show.php");
        }
    }