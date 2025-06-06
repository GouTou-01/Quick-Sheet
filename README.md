# ​​Quick Sheet​​ - 轻量级内容管理系统
![首页](https://github.com/user-attachments/assets/0494660f-f2c9-4cc1-8231-c4d1e931c0b5)
![登录](https://github.com/user-attachments/assets/7ac74aa1-7a30-48fa-b70e-67ea49c53f3d)
![后台](https://github.com/user-attachments/assets/5563245e-187d-43b9-8bd5-c4435cf0e344)
---

​​Quick Sheet​​ 是一个简单、高效的轻量级内容管理系统，专为展示单页面内容而设计。它不依赖数据库，使用文件系统进行存储，具有简洁的管理界面和现代化的展示效果。

## 特性

- **零数据库依赖**: 所有内容都存储在 JSON 文件中，无需配置数据库
- **响应式设计**: 前后台均采用响应式设计，完美适配各种设备
- **富文本编辑**: 集成 Quill.js 编辑器，支持丰富的文本格式化选项
- **代码高亮**: 支持多种编程语言的代码高亮显示
- **行内代码**: 支持行内代码格式化和代码块格式化
- **图片管理**: 支持图片上传和粘贴功能
- **一键复制代码**: 代码块右上角提供复制按钮
- **Markdown 风格**: 支持类似 Markdown 的代码块和格式化
- **简洁登录**: 美观的登录页面，采用 JSON 文件管理用户
- **实时预览**: 编辑时可实时预览最终展示效果

## 技术栈

- **后端**: PHP (无框架)
- **前端**:
  - HTML5 / CSS3
  - JavaScript (原生)
  - Quill.js (富文本编辑器)
  - highlight.js (代码高亮)
  - Font Awesome (图标)

## 安装方法

1. 将所有文件上传到您的网站根目录
2. 确保服务器支持 PHP 7.2 或更高版本
3. 创建 `content` 和 `images` 到目录下也就是 `content` 和 `images` 和 `admin` 和 `assets` 是同一个目录，不懂的看项目目录结构
4. 确保 `content` 和 `images` 目录可写
5. 通过访问 `/admin` 路径登录到管理后台
6. 默认管理员账号可在 `admin/config/admins.json` 中配置

## 目录结构

```
/
├── admin/                  # 管理后台
│   ├── config/             # 配置文件
│   │   └── admins.json     # 管理员账号配置
│   ├── admin.php           # 后台主界面
│   ├── login.php           # 登录页面
│   └── index.php           # 登录和注销处理
├── assets/                # 静态资源
│   └── icons/            # 网站图标
│       ├── favicon.svg    # SVG格式图标
│       └── favicon.png    # PNG格式图标
├── content/                # 内容存储
│   └── content.json        # 网站内容
├── images/                 # 上传的图片
└── index.php               # 前台展示页面
```

## 使用指南

### 登录管理后台

1. 访问 `您的域名/admin`
2. 使用配置的管理员账号登录

### 编辑内容

1. 在编辑器中输入或粘贴内容
2. 使用工具栏格式化文本、插入图片或代码
3. 可以在「编辑内容」和「预览效果」选项卡之间切换查看
4. 点击「保存更改」按钮保存内容

### 代码块和行内代码

- 使用工具栏中的「代码块」按钮插入代码块
- 使用工具栏中的「行内代码」按钮为选中文本添加行内代码格式
- 代码块支持自动语法高亮

### 图片管理

- 点击工具栏中的图片按钮上传图片
- 支持直接粘贴剪贴板中的图片
- 图片会自动上传到服务器并插入到内容中
- 点击已插入的图片可调整其显示尺寸（可设置为百分比或像素值）

## 管理员账户配置

管理员账户存储在 `admin/config/admins.json` 文件中，格式如下:

```json
{
    "admins": [
        {
            "username": "admin",
            "password": "your_password"
        },
        {
            "username": "another_admin",
            "password": "another_password"
        }
    ]
}
```

## 安全建议

1. 修改默认管理员账号和密码
2. 考虑为 `admin` 目录添加 IP 限制或额外的 HTTP 认证
3. 定期备份 `content` 目录内的 JSON 文件
4. 在生产环境中，建议启用 HTTPS

## 自定义主题

如需修改前台展示样式:
1. 编辑 `index.php` 文件中的 CSS 部分
2. 自定义颜色可以通过修改 CSS 变量实现:
   ```css
   :root {
       --primary-color: #2c3e50;
       --text-color: #333;
       --bg-color: #f8f9fa;
       --border-color: #e9ecef;
   }
   ```

## 许可证

此项目采用 MIT 许可证。

---

**​​Quick Sheet​​** - 简单、高效的内容管理解决方案 
