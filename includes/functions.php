<?php
function alert($msg, $url = null) {
    $script = "<script>alert('$msg')";
    if ($url) $script .= "; window.location.href='$url'";
    echo $script . ";</script>";
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>