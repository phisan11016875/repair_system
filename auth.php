<<<<<<< HEAD
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
=======
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
>>>>>>> 19b2a46099fb6a0a5d18007dc5257919a6c8c9d4
}