<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $configFile = __DIR__ . '/config/admins.json';
    if (file_exists($configFile)) {
        $config = json_decode(file_get_contents($configFile), true);
        $isValidUser = false;
        
        foreach ($config['admins'] as $admin) {
            if ($admin['username'] === $username && $admin['password'] === $password) {
                $isValidUser = true;
                break;
            }
        }
        
        if ($isValidUser) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            header('Location: admin.php');
            exit;
        }
    }
    $error = '用户名或密码错误';
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>​Quick Sheet 管理员登录</title>
    <link rel="icon" href="../assets/icons/favicon.svg" type="image/svg+xml">
    <link rel="alternate icon" href="../assets/icons/favicon.png" type="image/png">
    <link rel="shortcut icon" href="../assets/icons/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --primary-dark: #2980b9;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --animation-speed: 0.3s;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: var(--dark-color);
        }
        
        .login-container {
            width: 360px;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s ease;
            backdrop-filter: blur(10px);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h2 {
            font-size: 1.8rem;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .login-header p {
            color: #777;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color var(--animation-speed), box-shadow var(--animation-speed);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .form-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            transition: color var(--animation-speed);
        }
        
        .form-control:focus + .form-icon {
            color: var(--primary-color);
        }
        
        .error-message {
            background-color: #fff3f3;
            color: var(--danger-color);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            border-left: 4px solid var(--danger-color);
            animation: shakeX 0.5s ease;
        }
        
        @keyframes shakeX {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .error-message i {
            margin-right: 8px;
            font-size: 1rem;
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background var(--animation-speed), transform var(--animation-speed);
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .login-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .login-btn:active {
            transform: translateY(0);
        }
        
        .footer-text {
            text-align: center;
            margin-top: 1.5rem;
            color: #999;
            font-size: 0.8rem;
        }
        
        .footer-text a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .footer-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>​Quick Sheet 管理系统</h2>
            <p>请输入您的账号信息进行登录</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="用户名" required autocomplete="off">
                <i class="fas fa-user form-icon"></i>
            </div>
            
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="密码" required>
                <i class="fas fa-lock form-icon"></i>
            </div>
            
            <button type="submit" class="login-btn">登录</button>
        </form>
        
        <div class="footer-text">
            &copy; <?php echo date('Y'); ?> ​Quick Sheet 管理系统
        </div>
    </div>
</body>
</html> 