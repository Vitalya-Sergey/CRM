<?php
function getUserType($db) {

    $token = $_SESSION['token'];
    $user= $db->query(
        "SELECT * FROM users WHERE token='$token'"
    )->fetchAll();
    if (!$user) {
        return null;
    }
    return $user[0]['type'];
}
?>
