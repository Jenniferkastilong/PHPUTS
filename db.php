<?php
    $db = new PDO('mysql:host=localhost;dbname=uts', 'useruts','useruts');
    $db->query("CREATE TABLE IF NOT EXISTS user(uid INT(5) PRIMARY KEY AUTO_INCREMENT, username VARCHAR(20), email VARCHAR(50), pass VARCHAR(100))");
    $sq = $db->prepare("SELECT * FROM user WHERE id=?");
    $sq->execute([1]);
    $row = $sq->fetch(PDO::FETCH_ASSOC);
    if(!$row){
        $sq = $db->prepare("INSERT INTO user VALUES(?,?,?,?)");
        $pass = password_hash('admin', PASSWORD_BCRYPT);
        $sq->execute([1, 'admin', 'admin', $pass]);
    }