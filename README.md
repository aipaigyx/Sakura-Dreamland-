# 樱花梦境博客系统

![GitHub](https://img.shields.io/github/license/sakuradream/aniblog)
![GitHub stars](https://img.shields.io/github/stars/sakuradream/aniblog?style=social)

## 项目简介

樱花梦境博客系统是一个现代化的二次元风格博客网站，专注于提供高质量的动漫、游戏和二次元文化内容。

- **现代化设计**：采用Tailwind CSS构建的响应式界面，支持各种设备尺寸
- **丰富的功能**：文章系统、图片画廊、角色生成器、资讯卡片、视频卡片等
- **互动体验**：评论、点赞、收藏、弹幕等多种互动功能
- **用户系统**：完整的用户注册、登录、个人中心功能
- **后台管理**：功能强大的管理后台，支持内容管理、用户管理、系统设置等

## 技术栈

- **前端**：HTML5 + CSS3 + JavaScript (ES6+) + Tailwind CSS + GSAP
- **后端**：PHP 8.x + MySQL + Redis
- **架构模式**：模块化设计，模板驱动

## 目录结构

```
├── public/                # 主网站目录
│   ├── admin/            # 管理后台
│   ├── api/              # API接口
│   ├── assets/           # 静态资源
│   │   ├── css/          # CSS样式文件
│   │   ├── images/       # 图片资源
│   │   ├── js/           # JavaScript文件
│   │   └── vendor/       # 第三方库
│   ├── components/       # UI组件
│   ├── sessions/         # 会话存储
│   ├── cache/            # 缓存存储
│   ├── uploads/          # 文件上传目录
│   └── user/             # 用户中心
├── app/                  # 应用核心目录
├── tests/                # 测试文件
├── vendor/               # Composer依赖
└── .trae/                # Trae AI配置
```

## 主要功能模块

### 内容管理
- **文章系统**：支持富文本编辑、分类管理、标签系统
- **图片画廊**：支持图片上传、分类、标签、瀑布流布局
- **角色生成器**：支持创建和管理角色卡片
- **资讯卡片**：展示最新资讯和新闻
- **视频卡片**：展示视频内容
- **内容搜索**：支持中文关键词搜索

### 用户管理
- **注册系统**：支持邮箱注册和验证
- **登录系统**：支持账号密码登录和记住密码
- **密码重置**：支持通过邮箱重置密码
- **用户中心**：个人资料管理、文章管理、收藏管理等

### 互动系统
- **评论系统**：支持文章评论和回复
- **点赞功能**：支持对文章、评论等内容点赞
- **收藏功能**：支持收藏文章、图片等内容
- **弹幕功能**：支持在文章页面发送弹幕

### 系统管理
- **数据备份**：支持数据库备份
- **网络监控**：监控网站访问情况
- **Redis缓存**：管理缓存配置
- **通知配置**：管理邮件模板和推送配置

## 管理后台

管理后台位于 `public/admin/` 目录，提供了以下功能：

- **文章管理**：创建、编辑、删除文章
- **图片管理**：上传、管理图片
- **角色管理**：创建、编辑角色卡片
- **资讯卡片管理**：管理资讯内容
- **视频卡片管理**：管理视频内容
- **用户管理**：管理用户账号和权限
- **系统设置**：配置网站基本信息、主题颜色、显示设置等
- **首页排版**：管理首页模块和布局

## 安装与配置

### 环境要求
- **Web服务器**：Apache或Nginx
- **PHP版本**：8.0+
- **MySQL版本**：5.7+
- **Redis版本**：6.0+（可选，用于缓存）

### 安装步骤
1. **克隆项目**：将项目文件上传到服务器
   ```bash
   git clone https://github.com/sakuradream/aniblog.git
   cd aniblog
   ```

2. **创建数据库**：创建MySQL数据库并导入初始数据

3. **配置数据库**：修改 `public/db.php` 文件中的数据库连接信息

4. **设置目录权限**：确保 `uploads/`、`cache/`、`sessions/` 目录有读写权限
   ```bash
   chmod -R 755 public/uploads/
   chmod -R 755 public/cache/
   chmod -R 755 public/sessions/
   ```

5. **安装依赖**：如果使用Composer管理依赖
   ```bash
   composer install
   ```

6. **访问网站**：在浏览器中访问网站地址

### 初始账号
- **管理员账号**：admin@example.com
- **密码**：admin123

## 基本使用方法

### 创建文章
1. 登录管理后台
2. 点击左侧菜单中的「文章管理」
3. 点击「添加文章」按钮
4. 填写文章标题、内容、分类等信息
5. 点击「保存」按钮

### 管理图片
1. 登录管理后台
2. 点击左侧菜单中的「图片管理」
3. 点击「上传图片」按钮
4. 选择图片文件并上传
5. 为图片添加标题、描述等信息

### 配置网站设置
1. 登录管理后台
2. 点击左侧菜单中的「系统设置」
3. 根据需要修改网站基本信息、主题颜色、显示设置等
4. 点击「保存」按钮

## 贡献指南

### 如何贡献
1. **Fork** 本仓库
2. **Clone** 你fork的仓库到本地
   ```bash
   git clone https://github.com/YOUR_USERNAME/aniblog.git
   cd aniblog
   ```
3. **创建分支**：创建一个新的分支来开发你的功能或修复
   ```bash
   git checkout -b feature/your-feature-name
   ```
4. **开发**：实现你的功能或修复
5. **提交**：提交你的更改
   ```bash
   git add .
   git commit -m "Add your commit message here"
   ```
6. **推送**：推送你的分支到GitHub
   ```bash
   git push origin feature/your-feature-name
   ```
7. **创建Pull Request**：在GitHub上创建一个Pull Request

### 代码规范
- 遵循PSR-4自动加载规范
- 使用命名空间组织代码
- 代码缩进使用4个空格
- 变量和函数命名使用小写字母和下划线
- 类名使用驼峰命名法
- 方法名使用驼峰命名法
- 提交消息应该清晰明了，描述你的更改

## 许可证信息

本项目采用 **MIT License** 许可证。

### MIT License

```
MIT License

Copyright (c) 2026 樱花梦境

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## 安全注意事项

1. **定期备份**：定期备份数据库和重要文件
2. **更新密码**：定期更新管理员密码
3. **权限管理**：合理设置用户权限
4. **安全配置**：关闭不必要的服务和端口
5. **防止SQL注入**：使用预处理语句和参数化查询
6. **防止XSS攻击**：对用户输入进行过滤和转义

## 常见问题

### 1. 无法上传图片
- 检查 `uploads/` 目录权限是否正确
- 检查PHP配置中的上传限制
- 检查文件大小是否超过限制

### 2. 网站加载缓慢
- 启用Redis缓存
- 优化图片大小和格式
- 检查服务器资源使用情况

### 3. 后台登录失败
- 检查用户名和密码是否正确
- 检查数据库连接是否正常
- 检查PHP会话设置

## 联系我们

- **邮箱**：contact@sakuradream.com
- **官方网站**：https://www.sakuradream.com
- **GitHub**：https://github.com/sakuradream/aniblog
- **QQ群**：1081909009

---

© 2026 樱花梦境. 保留所有权利。
