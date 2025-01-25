<?php
function checkLogin() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function checkAdminAccess() {
    checkLogin();
    if ($_SESSION['level'] !== 'admin') {
        header("Location: unauthorized.php");
        exit();
    }
}

function checkTechnicianAccess() {
    checkLogin();
    if ($_SESSION['level'] !== 'technician' && $_SESSION['level'] !== 'technician') {
        header("Location: unauthorized.php");
        exit();
    }
}


function checkuserAccess() {
    checkLogin();
    if ($_SESSION['level'] !== 'user' && $_SESSION['level'] !== 'user') {
        header("Location: unauthorized.php");
        exit();
    }
}
function logout() {
    session_start();
    session_destroy();
    header("Location: index.php");
    exit();
}