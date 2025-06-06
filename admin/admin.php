<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 检查必要目录
$requiredDirs = [
    '../content' => '内容存储目录',
    '../images' => '图片上传目录'
];

$missingDirs = [];
foreach ($requiredDirs as $dir => $description) {
    if (!file_exists($dir)) {
        $missingDirs[$dir] = $description;
    } elseif (!is_writable($dir)) {
        $missingDirs[$dir] = $description . "（目录存在但没有写入权限）";
    }
}

// 如果有缺失的目录，只显示错误信息
if (!empty($missingDirs)) {
    ?>
    <!DOCTYPE html>
    <html lang="zh">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>​Quick Sheet​​ 系统错误</title>
        <link rel="icon" href="../assets/icons/favicon.svg" type="image/svg+xml">
        <style>
            :root {
                --primary-color: #2c3e50;
                --secondary-color: #3498db;
                --danger-color: #e74c3c;
                --bg-color: #f8f9fa;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                background-color: var(--bg-color);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }

            .error-container {
                background: white;
                padding: 2rem;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                max-width: 600px;
                width: 100%;
            }

            .error-title {
                color: var(--danger-color);
                font-size: 1.5rem;
                margin-bottom: 1rem;
                padding-bottom: 1rem;
                border-bottom: 2px solid #ffecec;
            }

            .error-message {
                background-color: #fff3f3;
                padding: 1.5rem;
                border-radius: 6px;
                margin: 1rem 0;
                color: #666;
                line-height: 1.8;
            }

            .error-message strong {
                color: var(--danger-color);
                display: block;
                margin-bottom: 1rem;
            }

            .error-list {
                margin: 1rem 0;
                padding-left: 1.5rem;
            }

            .error-list li {
                margin-bottom: 0.5rem;
                color: #666;
            }

            .error-footer {
                margin-top: 1.5rem;
                padding-top: 1rem;
                border-top: 1px solid #eee;
                font-size: 0.9rem;
                color: #666;
            }

            .back-link {
                display: inline-block;
                margin-top: 1rem;
                color: var(--secondary-color);
                text-decoration: none;
            }

            .back-link:hover {
                text-decoration: underline;
            }

            code {
                background: #f8f9fa;
                padding: 0.2em 0.4em;
                border-radius: 3px;
                font-family: Consolas, Monaco, 'Courier New', monospace;
                font-size: 0.9em;
                color: var(--danger-color);
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1 class="error-title">系统初始化错误</h1>
            <div class="error-message">
                <strong>无法访问管理界面</strong>
                系统检测到以下必需的目录不存在或没有正确的权限设置：
                <ul class="error-list">
                <?php foreach ($missingDirs as $dir => $desc): ?>
                    <li><code><?php echo htmlspecialchars($dir); ?></code> - <?php echo htmlspecialchars($desc); ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
            <div class="error-footer">
                <p>请按照以下步骤解决问题：</p>
                <ol class="error-list">
                    <li>在网站根目录下创建缺失的目录</li>
                    <li>确保目录权限设置为 755</li>
                    <li>确保目录所有者为 www 用户</li>
                    <li>确保目录用户组为 www</li>
                </ol>
                <p>完成以上步骤后，请刷新页面。</p>
                <a href="admin.php" class="back-link">刷新页面</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$contentFile = '../content/content.json';
$content = [];

// 读取当前内容
if (file_exists($contentFile)) {
    $content = json_decode(file_get_contents($contentFile), true) ?: [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($missingDirs)) {
    if (isset($_POST['content'])) {
        // 检查内容是否为空（包括空HTML标签）
        $strippedContent = trim(strip_tags($_POST['content']));
        
        if (empty($strippedContent)) {
            // 使用默认内容
            $newContent = [
                'title' => 'Welcome to Quick Sheet',
                'content' => '<h2>👋 欢迎使用 Quick Sheet</h2>
<p>这是一个简单、高效的轻量级内容管理系统。您可以通过以下步骤开始使用：</p>
<ol>
    <li>访问 <code>/admin</code> 路径登录到管理后台</li>
    <li>使用编辑器创建您的内容</li>
    <li>支持富文本编辑、代码高亮、图片上传等功能</li>
</ol>
<blockquote>
    <p>开始创作精彩内容吧！</p>
</blockquote>'
            ];
        } else {
            $newContent = [
                'title' => $_POST['title'] ?? '',
                'content' => $_POST['content']
            ];
        }
        
        // 保存内容
        if (!is_writable(dirname($contentFile))) {
            $error = '保存失败：content 目录没有写入权限';
        } else if (!@file_put_contents($contentFile, json_encode($newContent, JSON_UNESCAPED_UNICODE))) {
            $error = '保存失败：无法写入文件，请检查文件权限';
        } else {
            header('Location: admin.php?saved=1');
            exit;
        }
    }
    
    // 处理图片上传
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../images/';
        
        if (!is_writable($uploadDir)) {
            echo json_encode(['error' => 'images 目录没有写入权限']);
            exit;
        }
        
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            echo json_encode(['location' => '../images/' . $fileName]);
            exit;
        } else {
            echo json_encode(['error' => '图片上传失败，请检查目录权限']);
            exit;
        }
    }

    // 处理粘贴的图片
    if (isset($_POST['image_data'])) {
        $uploadDir = '../images/';
        
        if (!is_writable($uploadDir)) {
            echo json_encode(['error' => 'images 目录没有写入权限']);
            exit;
        }

        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['image_data']));
        $fileName = time() . '_pasted_image.png';
        $uploadFile = $uploadDir . $fileName;
        
        if (file_put_contents($uploadFile, $imageData)) {
            echo json_encode(['location' => '../images/' . $fileName]);
            exit;
        } else {
            echo json_encode(['error' => '图片保存失败，请检查目录权限']);
            exit;
        }
    }
}

$currentContent = $content['content'] ?? '';
$currentTitle = $content['title'] ?? '';

// 获取保存状态
$saved = isset($_GET['saved']) && $_GET['saved'] == '1';
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>​Quick Sheet​​ 内容管理</title>
    <link rel="icon" href="../assets/icons/favicon.svg" type="image/svg+xml">
    <link rel="alternate icon" href="../assets/icons/favicon.png" type="image/png">
    <link rel="shortcut icon" href="../assets/icons/favicon.ico">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/monokai-sublime.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --text-color: #333;
            --bg-color: #f8f9fa;
            --border-color: #e9ecef;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--bg-color);
            min-height: 100vh;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            width: 95%;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
            padding: 0 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 300;
            letter-spacing: -0.5px;
            color: rgba(255, 255, 255, 0.95);
        }

        .user-panel {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-info {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            position: relative;
            padding-left: 1.8rem;
        }

        .user-info::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 1.4rem;
            height: 1.4rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.9);
        }

        .logout {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1.2rem;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            font-size: 0.95rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logout:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .container {
                width: 92%;
                padding: 15px;
            }

            .header {
                padding: 1.2rem 0;
            }

            .header .container {
                padding: 0 15px;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .user-info {
                font-size: 0.9rem;
                padding-left: 1.6rem;
            }

            .user-info::before {
                width: 1.2rem;
                height: 1.2rem;
            }

            .logout {
                padding: 0.4rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                width: 90%;
                padding: 12px;
            }

            .header {
                padding: 1rem 0;
            }

            .header .container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .header h1 {
                font-size: 1.4rem;
            }

            .user-panel {
                flex-direction: column;
                gap: 0.8rem;
                width: 100%;
            }

            .user-info {
                font-size: 0.85rem;
            }

            .logout {
                width: 100%;
                text-align: center;
                padding: 0.5rem;
            }
        }

        .main-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .editor-container {
            margin-bottom: 2rem;
        }

        #editor {
            height: 500px;
            margin-bottom: 1.5rem;
            border-radius: 4px;
        }

        .ql-toolbar.ql-snow {
            border-radius: 4px 4px 0 0;
            border-color: var(--border-color);
        }

        .ql-container.ql-snow {
            border-radius: 0 0 4px 4px;
            border-color: var(--border-color);
        }

        .ql-editor {
            font-size: 1.1rem;
            line-height: 1.8;
        }

        button[type="submit"] {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }

        button[type="submit"]:hover {
            background: #2980b9;
        }

        .preview {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 2rem;
        }

        .preview h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        #preview-content {
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            min-height: 200px;
        }

        .message {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .error {
            background-color: #fff3f3;
            color: var(--danger-color);
            border: 1px solid #ffcdd2;
        }

        .success {
            background-color: #e8f5e9;
            color: var(--success-color);
            border: 1px solid #c8e6c9;
        }

        .message strong {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 1.1em;
        }

        .message ul {
            margin: 0.5rem 0 0.5rem 1.5rem;
        }

        .message li {
            margin-bottom: 0.3rem;
        }

        .history {
            margin-top: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 2rem;
        }

        .history h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .history-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-info {
            color: #666;
            font-size: 0.9rem;
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        pre {
            background: #23241f;
            color: #f8f8f2;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
            overflow-x: auto;
        }

        code {
            font-family: 'Consolas', 'Monaco', 'Courier New', Courier, monospace;
            font-size: 0.9rem;
        }

        /* 添加行内代码样式 */
        .ql-editor code {
            background-color: #f0f0f0;
            padding: 0.2em 0.4em;
            border-radius: 3px;
            color: #e83e8c;
            font-family: 'Consolas', 'Monaco', 'Courier New', Courier, monospace;
            font-size: 0.9em;
            white-space: pre-wrap;
        }

        /* 确保代码块和行内代码样式不冲突 */
        .ql-editor pre code {
            background-color: #23241f;
            color: #f8f8f2;
            padding: 1rem;
            border-radius: 4px;
            display: block;
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .main-content, .preview, .history {
                padding: 1rem;
            }

            #editor {
                height: 400px;
            }

            button[type="submit"] {
                width: 100%;
            }
        }

        .title-input {
            width: 100%;
            padding: 0.8rem;
            font-size: 1.2rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            margin-bottom: 1rem;
            font-family: inherit;
        }

        .title-input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        /* 添加链接工具提示样式 */
        .ql-tooltip {
            background-color: white !important;
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
            padding: 8px !important;
            border-radius: 4px !important;
        }

        .ql-tooltip input[type=text] {
            width: 200px !important;
            padding: 4px 8px !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 3px !important;
            margin-right: 8px !important;
        }

        .ql-tooltip a.ql-action,
        .ql-tooltip a.ql-remove {
            color: var(--secondary-color) !important;
            text-decoration: none !important;
            margin-left: 8px !important;
        }

        /* 添加编辑/预览切换样式 */
        .tab-container {
            margin-bottom: 1rem;
            display: flex;
            border-bottom: 1px solid var(--border-color);
        }
        
        .tab-button {
            padding: 0.8rem 1.5rem;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            color: #666;
            transition: all 0.3s;
        }
        
        .tab-button.active {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
        }
        
        .tab-button:hover:not(.active) {
            color: var(--primary-color);
            border-bottom-color: #ddd;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* 预览区域样式 */
        #preview-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            min-height: 500px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.8;
            font-size: 1.1rem;
            overflow-x: auto;
        }
        
        /* 让预览与前端展示保持一致的样式 */
        #preview-container h1, #preview-container h2, #preview-container h3, 
        #preview-container h4, #preview-container h5, #preview-container h6 {
            color: var(--primary-color);
            margin: 1.5rem 0 1rem;
            line-height: 1.4;
        }
        
        #preview-container h1 { font-size: 2.2rem; }
        #preview-container h2 { font-size: 1.8rem; }
        #preview-container h3 { font-size: 1.5rem; }
        
        #preview-container p {
            margin-bottom: 1.2rem;
        }
        
        #preview-container img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            margin: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        #preview-container a {
            color: #3498db;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color 0.3s;
            word-break: break-all;
        }
        
        #preview-container a:hover {
            border-bottom-color: #3498db;
        }
        
        #preview-container ul, #preview-container ol {
            margin: 1rem 0;
            padding-left: 2rem;
        }
        
        #preview-container li {
            margin-bottom: 0.5rem;
        }
        
        #preview-container blockquote {
            border-left: 4px solid var(--primary-color);
            padding-left: 1rem;
            margin: 1rem 0;
            color: #666;
            font-style: italic;
        }
        
        #preview-container pre {
            position: relative;
            background: #23241f;
            color: #f8f8f2;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        
        #preview-container code:not(pre code) {
            background-color: #f0f0f0;
            padding: 0.2em 0.4em;
            border-radius: 3px;
            color: #e83e8c;
            font-family: 'Consolas', 'Monaco', 'Courier New', Courier, monospace;
            font-size: 0.9em;
            white-space: pre-wrap;
        }
        
        #preview-container pre code {
            background-color: #23241f;
            color: #f8f8f2;
            font-family: 'Consolas', 'Monaco', 'Courier New', Courier, monospace;
            font-size: 0.9em;
        }
        
        /* 复制按钮样式 */
        .copy-button {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.3rem 0.6rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 3px;
            color: #fff;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background-color 0.2s;
            opacity: 0.8;
            z-index: 10;
        }
        
        pre:hover .copy-button {
            opacity: 1;
        }
        
        .copy-button:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .copy-button.copied {
            background: #2ecc71;
        }

    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1>​Quick Sheet 管理系统</h1>
            <div class="user-panel">
                <span class="user-info"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="index.php?action=logout" class="logout">退出登录</a>
            </div>
        </div>
    </header>

    <main class="container">
        <?php if (isset($error)): ?>
            <div class="message error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($saved): ?>
            <div class="message success">
                <strong>成功！</strong>内容已保存。
            </div>
        <?php endif; ?>

        <div class="main-content">
            <form method="POST" id="contentForm">
                <div class="form-group">
                    <label class="form-label" for="title">页面标题（可选）</label>
                    <input type="text" id="title" name="title" class="title-input" 
                           value="<?php echo htmlspecialchars($currentTitle); ?>" 
                           placeholder="输入页面标题，不填则不显示">
                </div>
                
                <!-- 添加编辑/预览切换选项卡 -->
                <div class="tab-container">
                    <button type="button" class="tab-button active" data-tab="editor-tab">编辑内容</button>
                    <button type="button" class="tab-button" data-tab="preview-tab">预览效果</button>
                </div>
                
                <!-- 编辑器选项卡 -->
                <div class="tab-content active" id="editor-tab">
                    <div class="editor-container">
                        <div id="editor"><?php echo $currentContent; ?></div>
                        <input type="hidden" name="content" id="hiddenContent">
                    </div>
                </div>
                
                <!-- 预览选项卡 -->
                <div class="tab-content" id="preview-tab">
                    <div id="preview-container"></div>
                </div>
                
                <button type="submit">保存更改</button>
            </form>
        </div>
    </main>

    <!-- 删除确认对话框 -->

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        // 首先初始化 highlight.js
        hljs.configure({
            languages: ['javascript', 'html', 'css', 'php', 'python', 'java', 'cpp', 'sql']
        });

        // 添加 Quill 中文本地化
        const zhCN = {
            placeholder: '请输入内容...',
            toolbar: {
                header: {
                    '1': '标题1',
                    '2': '标题2',
                    '3': '标题3',
                    '4': '标题4',
                    '5': '标题5',
                    '6': '标题6',
                    'normal': '正文'
                },
                bold: '加粗',
                italic: '斜体',
                underline: '下划线',
                strike: '删除线',
                link: '链接',
                image: '图片',
                'code-block': '代码块',
                'list': {
                    'ordered': '有序列表',
                    'bullet': '无序列表'
                },
                blockquote: '引用块',
                clean: '清除格式',
                color: '文字颜色',
                background: '背景颜色',
                code: '行内代码'
            }
        };

        // 扩展 Quill 以支持中文界面
        const Tooltip = Quill.import('ui/tooltip');
        class CustomTooltip extends Tooltip {
            constructor(quill, bounds) {
                super(quill, bounds);
                this.textbox.setAttribute('placeholder', '请输入链接地址...');
                this.root.querySelector('a.ql-action').textContent = '保存';
                this.root.querySelector('a.ql-remove').textContent = '移除';
            }
        }
        Quill.register('ui/tooltip', CustomTooltip, true);

        // 修复语法高亮问题
        const Module = Quill.import('core/module');
        class CustomSyntax extends Module {
            constructor(quill, options) {
                super(quill, options);
                this.options = options;
                this.highlight = options.highlight || this.highlight.bind(this);
                this.quill.on('text-change', this.update.bind(this));
                this.update();
            }
            
            highlight(text) {
                return hljs.highlightAuto(text).value;
            }
            
            update() {
                this.quill.container.querySelectorAll('pre code').forEach(block => {
                    try {
                        if (block.textContent) {
                            block.innerHTML = this.highlight(block.textContent);
                        }
                    } catch (e) {
                        console.error('Error highlighting:', e);
                    }
                });
            }
        }
        Quill.register('modules/syntax', CustomSyntax, true);

        // 添加行内代码格式
        const Inline = Quill.import('blots/inline');
        class CodeInline extends Inline { }
        CodeInline.blotName = 'code';
        CodeInline.tagName = 'code';
        Quill.register(CodeInline);

        // 添加自定义图片调整大小功能
        const Image = Quill.import('formats/image');
        class ResizableImage extends Image {
            static create(value) {
                const node = super.create(value);
                node.setAttribute('src', value);
                node.setAttribute('data-original-src', value);
                // 设置默认宽度样式
                node.style.maxWidth = '100%';
                return node;
            }
            
            static formats(domNode) {
                const formats = {};
                if (domNode.style.width) {
                    formats['width'] = domNode.style.width;
                }
                return formats;
            }
            
            format(name, value) {
                if (name === 'width') {
                    if (value) {
                        this.domNode.style.width = value;
                    } else {
                        this.domNode.style.width = '';
                    }
                } else {
                    super.format(name, value);
                }
            }
        }
        
        ResizableImage.blotName = 'image';
        ResizableImage.tagName = 'img';
        Quill.register(ResizableImage, true);
        
        // 初始化 Quill 编辑器
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: zhCN.placeholder,
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [false, 1, 2, 3, 4, 5, 6] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'link', 'image', 'code-block', 'code'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['clean'],
                        [{
                            'image': function() {
                                // 使用默认图片处理程序
                                const input = document.createElement('input');
                                input.setAttribute('type', 'file');
                                input.setAttribute('accept', 'image/*');
                                input.click();

                                input.onchange = () => {
                                    const file = input.files[0];
                                    if (file) {
                                        const formData = new FormData();
                                        formData.append('image', file);

                                        fetch('admin.php', {
                                            method: 'POST',
                                            body: formData
                                        })
                                        .then(response => response.json())
                                        .then(result => {
                                            const range = quill.getSelection(true);
                                            quill.insertEmbed(range.index, 'image', result.location);
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('上传图片失败');
                                        });
                                    }
                                };
                            }
                        }]
                    ],
                    handlers: {
                        'image': function() {
                            // 使用默认图片处理程序
                            const input = document.createElement('input');
                            input.setAttribute('type', 'file');
                            input.setAttribute('accept', 'image/*');
                            input.click();

                            input.onchange = () => {
                                const file = input.files[0];
                                if (file) {
                                    const formData = new FormData();
                                    formData.append('image', file);

                                    fetch('admin.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(result => {
                                        const range = quill.getSelection(true);
                                        quill.insertEmbed(range.index, 'image', result.location);
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('上传图片失败');
                                    });
                                }
                            };
                        }
                    }
                },
                syntax: true,
                clipboard: {
                    matchVisual: false
                }
            }
        });

        // 应用中文本地化
        const toolbar = quill.getModule('toolbar');
        toolbar.container.querySelectorAll('button, select').forEach(element => {
            const className = Array.from(element.classList).find(cls => cls.startsWith('ql-'));
            if (!className) return;
            
            const format = className.replace('ql-', '');
            if (format === 'header') {
                // 处理标题下拉菜单
                const select = element;
                Array.from(select.options).forEach(option => {
                    const value = option.value || 'normal';
                    option.textContent = zhCN.toolbar.header[value];
                });
            } else if (format === 'list') {
                // 处理列表按钮
                const value = element.value;
                if (value) {
                    element.title = zhCN.toolbar.list[value];
                }
            } else if (format === 'blockquote') {
                // 添加引用块按钮的提示文本
                element.title = '引用块';
            } else {
                // 处理其他按钮
                const title = zhCN.toolbar[format];
                if (title) {
                    element.title = title;
                }
            }
        });

        const preview = document.getElementById('preview-container');
        const form = document.getElementById('contentForm');
        const hiddenContent = document.getElementById('hiddenContent');

        function updatePreview() {
            preview.innerHTML = quill.root.innerHTML;
            
            // 为预览中的代码块添加复制按钮
            preview.querySelectorAll('pre').forEach(pre => {
                pre.style.position = 'relative';
                
                if (!pre.querySelector('.copy-button')) {
                    const button = document.createElement('button');
                    button.className = 'copy-button';
                    button.textContent = '复制';
                    button.title = '复制代码';
                    
                    button.addEventListener('click', async () => {
                        try {
                            const code = pre.querySelector('code') ? 
                                (pre.querySelector('code').innerText || pre.querySelector('code').textContent) : 
                                (pre.innerText || pre.textContent);
                            
                            await navigator.clipboard.writeText(code);
                            
                            if (!navigator.clipboard) {
                                const textarea = document.createElement('textarea');
                                textarea.value = code;
                                textarea.style.position = 'fixed';
                                textarea.style.opacity = '0';
                                document.body.appendChild(textarea);
                                textarea.select();
                                document.execCommand('copy');
                                document.body.removeChild(textarea);
                            }
                            
                            button.textContent = '已复制';
                            button.classList.add('copied');
                            
                            setTimeout(() => {
                                button.textContent = '复制';
                                button.classList.remove('copied');
                            }, 2000);
                        } catch (err) {
                            console.error('复制失败:', err);
                            button.textContent = '复制失败';
                            setTimeout(() => {
                                button.textContent = '复制';
                            }, 2000);
                        }
                    });
                    
                    pre.appendChild(button);
                }
            });
            
            // 高亮代码块
            preview.querySelectorAll('pre code').forEach(block => {
                hljs.highlightElement(block);
            });
        }

        quill.on('text-change', updatePreview);
        updatePreview();

        // 处理编辑/预览切换
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // 移除所有活动状态
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                
                // 添加当前活动状态
                button.classList.add('active');
                document.getElementById(button.dataset.tab).classList.add('active');
                
                // 如果切换到预览，更新预览内容
                if (button.dataset.tab === 'preview-tab') {
                    updatePreview();
                }
            });
        });

        form.onsubmit = function() {
            hiddenContent.value = quill.root.innerHTML;
            return true;
        };

        // 处理粘贴事件
        quill.container.addEventListener('paste', function(e) {
            if (e.clipboardData && e.clipboardData.items) {
                for (let i = 0; i < e.clipboardData.items.length; i++) {
                    const item = e.clipboardData.items[i];
                    if (item.type.indexOf('image') !== -1) {
                        e.preventDefault();
                        const file = item.getAsFile();
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const formData = new FormData();
                            formData.append('image_data', e.target.result.split(',')[1]);
                            
                            fetch('admin.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(result => {
                                const range = quill.getSelection(true);
                                quill.insertEmbed(range.index, 'image', result.location);
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('上传图片失败');
                            });
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        });

        // 添加保存成功后的提示消息自动消失
        document.addEventListener('DOMContentLoaded', function() {
            const message = document.querySelector('.message');
            if (message) {
                setTimeout(function() {
                    message.style.display = 'none';
                }, 3000);
            }
        });

        // 删除确认对话框相关函数

        // 修复DOMNodeInserted警告
        // 使用 MutationObserver 替代已废弃的 DOMNodeInserted
        const fixDeprecationWarnings = () => {
            // 覆盖 Quill 的 scroll.constructor.observers 来避免使用废弃的 DOMNodeInserted 事件
            const originalObserve = Quill.prototype.constructor.imports.core.emitter.constructor.events.OBSERVER_CONFIG;
            if (originalObserve && originalObserve.childList === false) {
                // 将配置修改为使用 childList 而非已废弃的 DOM 事件
                Quill.prototype.constructor.imports.core.emitter.constructor.events.OBSERVER_CONFIG.childList = true;
            }
        };
        
        // 尝试修复警告
        try {
            fixDeprecationWarnings();
        } catch (e) {
            console.log('无法修复废弃警告，但这不影响编辑器功能');
        }

        // 添加图片点击事件，实现图片大小调整
        quill.root.addEventListener('click', function(event) {
            if (event.target && event.target.tagName === 'IMG') {
                const img = event.target;
                
                // 显示图片大小调整对话框
                const currentWidth = img.style.width || '100%';
                const newWidth = prompt('请输入图片宽度 (例如: 50%, 300px)', currentWidth);
                
                if (newWidth !== null) {
                    // 如果用户输入了有效值
                    if (newWidth === '') {
                        img.style.width = '';  // 重置为默认宽度
                    } else {
                        img.style.width = newWidth;
                    }
                }
            }
        });
    </script>
</body>
</html> 