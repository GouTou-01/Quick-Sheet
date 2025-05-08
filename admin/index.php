<?php
session_start();

// 检查是否有注销操作
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // 注销操作
    session_destroy();
    header('Location: login.php');
    exit;
} else {
    // 默认重定向到登录页面
    header('Location: login.php');
    exit;
}
?> 