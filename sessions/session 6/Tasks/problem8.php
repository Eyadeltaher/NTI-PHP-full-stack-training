<?php
function generatePass($length = 8) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $pass = "";

    for ($i = 0; $i < $length; $i++) {
        $pass .= $chars[rand(0, strlen($chars) - 1)];
    }

    return $pass;
}

echo generatePass(10);
?>
