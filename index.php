<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    $contentFile = 'content/content.json';
    if (file_exists($contentFile)) {
        $content = json_decode(file_get_contents($contentFile), true);
        echo htmlspecialchars($content['title'] ?? '内容展示');
    } else {
        echo '内容展示';
    }
    ?></title>
    <link rel="icon" href="assets/icons/favicon.svg" type="image/svg+xml">
    <link rel="alternate icon" href="assets/icons/favicon.png" type="image/png">
    <link rel="shortcut icon" href="assets/icons/favicon.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/monokai-sublime.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: none;
        }

        .content-wrapper {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .content {
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .content h1, .content h2, .content h3, .content h4, .content h5, .content h6 {
            color: var(--primary-color);
            margin: 1.5rem 0 1rem;
            line-height: 1.4;
        }

        .content h1 { font-size: 2.2rem; }
        .content h2 { font-size: 1.8rem; }
        .content h3 { font-size: 1.5rem; }

        .content p {
            margin-bottom: 1.2rem;
        }

        .content img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            margin: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .content a {
            color: #3498db;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color 0.3s;
            word-break: break-all;
        }

        .content a:hover {
            border-bottom-color: #3498db;
        }

        .content ul, .content ol {
            margin: 1rem 0;
            padding-left: 2rem;
        }

        .content li {
            margin-bottom: 0.5rem;
        }

        .content blockquote {
            border-left: 4px solid var(--primary-color);
            padding-left: 1rem;
            margin: 1rem 0;
            color: #666;
            font-style: italic;
        }

        pre {
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

        code {
            font-family: 'Consolas', 'Monaco', 'Courier New', Courier, monospace;
            font-size: 0.9rem;
        }

        /* 添加行内代码样式 */
        .content code:not(pre code) {
            background-color: #f0f0f0;
            padding: 0.2em 0.4em;
            border-radius: 3px;
            color: #e83e8c;
            font-family: 'Consolas', 'Monaco', 'Courier New', Courier, monospace;
            font-size: 0.9em;
            white-space: pre-wrap;
        }

        /* 确保代码块和行内代码样式不冲突 */
        .content pre code {
            background-color: #23241f;
            color: #f8f8f2;
            padding: inherit;
            border-radius: inherit;
            display: block;
            white-space: pre;
            overflow-x: auto;
            font-family: 'Consolas', 'Monaco', 'Courier New', Courier, monospace;
            font-size: 0.9em;
            line-height: 1.5;
        }

        .error {
            background-color: #fff3f3;
            color: #dc3545;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
            border: 1px solid #ffcdd2;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .content-wrapper {
                padding: 1rem;
            }

            .content {
                font-size: 1rem;
            }

            .content h1 { font-size: 1.8rem; }
            .content h2 { font-size: 1.5rem; }
            .content h3 { font-size: 1.3rem; }
        }

        .page-title {
            text-align: center;
            color: var(--primary-color);
            font-size: 2.5rem;
            margin: 2rem 0;
            font-weight: 300;
            letter-spacing: -0.5px;
        }

        .page-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 3px;
            background: var(--primary-color);
            margin: 1rem auto;
            transition: width 0.3s ease;
        }

        .page-title:hover::after {
            width: 150px;
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
            opacity: 0.8; /* 默认可见 */
            z-index: 10; /* 确保按钮在最上层 */
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
    <?php
    $contentFile = 'content/content.json';
    
    if (!file_exists($contentFile)) {
        $defaultContent = [
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
        
        // 检查目录是否存在
        if (!file_exists('content')) {
            echo '<div class="error">
                <strong>系统提示：</strong> content 目录不存在。<br>
                请在网站根目录下手动创建以下目录：<br>
                1. content 目录（用于存储内容）<br>
                2. images 目录（用于存储图片）<br>
                并确保这些目录的权限设置为 755，所有者为 www 用户。
            </div>';
            exit;
        }
        
        // 尝试创建文件
        if (!@file_put_contents($contentFile, json_encode($defaultContent, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
            echo '<div class="error">
                <strong>系统提示：</strong> 无法创建内容文件。<br>
                请检查 content 目录的写入权限。
            </div>';
            exit;
        }
        
        $content = $defaultContent;
    } else {
        $content = json_decode(file_get_contents($contentFile), true);
        if ($content === null) {
            echo '<div class="error">内容格式错误</div>';
            exit;
        }
    }
    ?>

    <main class="container">
        <?php if (!empty($content['title'])): ?>
        <h1 class="page-title"><?php echo htmlspecialchars($content['title']); ?></h1>
        <?php endif; ?>

        <div class="content-wrapper">
            <div class="content">
                <?php echo $content['content']; ?>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        // 代码高亮和添加复制按钮
        document.addEventListener('DOMContentLoaded', (event) => {
            // 代码高亮
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightElement(block);
                
                // 为每个代码块添加复制按钮
                const pre = block.parentElement;
                pre.style.position = 'relative'; // 确保pre元素是相对定位
                
                const button = document.createElement('button');
                button.className = 'copy-button';
                button.textContent = '复制';
                button.title = '复制代码';
                
                button.addEventListener('click', async () => {
                    try {
                        // 使用更可靠的方式获取代码内容
                        const code = block.innerText || block.textContent;
                        
                        // 使用Clipboard API
                        await navigator.clipboard.writeText(code);
                        
                        // 如果不支持Clipboard API，使用传统方法
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
            });

            // 自动识别URL并转换为链接
            document.querySelectorAll('.content p').forEach(paragraph => {
                if (!paragraph.querySelector('a, img, code, pre')) { // 避免处理已有链接、图片或代码的段落
                    const text = paragraph.innerHTML;
                    const urlRegex = /(https?:\/\/[^\s<]+)/g;
                    
                    if (urlRegex.test(text)) {
                        const newHtml = text.replace(urlRegex, url => {
                            return `<a href="${url}" target="_blank">${url}</a>`;
                        });
                        paragraph.innerHTML = newHtml;
                    }
                }
            });
        });
    </script>
</body>
</html> 