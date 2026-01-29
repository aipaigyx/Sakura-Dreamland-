# 🌸 Sakura Dreamland Blog System
![GitHub](https://img.shields.io/github/license/sakuradream/aniblog)
![GitHub stars](https://img.shields.io/github/stars/sakuradream/aniblog?style=social)
![GitHub forks](https://img.shields.io/github/forks/sakuradream/aniblog?style=social)
![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-%2338B2AC)

<div align="center">
A modern blog system for otaku, focusing on ACG culture sharing.<br>
二次元愛好者向けのモダンブログシステム、ACGカルチャーシェアに特化。<br>
一款专为二次元爱好者打造的现代化博客系统，聚焦动漫、游戏及二次元文化内容分享。
</div>

---

## 中文版本 | Chinese Version
一款专为二次元爱好者打造的现代化博客系统，聚焦动漫、游戏及二次元文化内容分享，兼顾美观度与实用性，开箱即用且易于拓展。

### ✨ 核心特性
- **🎨 二次元响应式设计**：基于Tailwind CSS构建，适配全设备，支持主题自定义
- **📦 全量内容模块**：文章/图片画廊/角色生成器/资讯/视频卡片，满足多元展示需求
- **💬 沉浸式互动**：评论、点赞、收藏、实时弹幕，打造专属二次元社区
- **👤 完整用户体系**：注册/登录/个人中心，账号安全与个性化管理兼备
- **🛠️ 高效后台**：一站式内容/用户/系统管理，零基础也能轻松运维

### 🛠️ 技术栈
| 分类       | 技术清单                                  |
|------------|-------------------------------------------|
| 前端       | HTML5 / CSS3 / JS(ES6+) / Tailwind CSS / GSAP |
| 后端       | PHP 8.x / MySQL 5.7+ / Redis 6.0+（可选） |
| 部署环境   | Apache 2.4+ / Nginx 1.18+ / 宝塔面板      |
| 架构模式   | 模块化设计 + 模板驱动                     |

### 📂 核心目录结构
```bash
├── public/                # 主网站访问目录
│   ├── admin/            # 管理后台
│   ├── assets/           # 静态资源（css/images/js）
│   ├── uploads/          # 文件上传目录
│   └── user/             # 用户中心
├── app/                  # 核心业务逻辑
├── tests/                # 测试文件
└── vendor/               # Composer依赖
```

### 🚀 快速安装
#### 环境要求
- PHP 8.0+（开启fileinfo扩展）、MySQL 5.7+
- Apache/Nginx、Redis 6.0+（可选）

#### 普通服务器安装
```bash
# 1. 克隆项目
git clone https://github.com/aipaigyx/Sakura-Dreamland-.git
cd Sakura-Dreamland-

# 2. 创建数据库并导入初始数据
# 3. 配置数据库信息（修改public/db.php）

# 4. 设置目录权限
chmod -R 755 public/uploads/ public/cache/ public/sessions/

# 5. 安装依赖
composer install

# 6. 访问网站（浏览器打开域名/服务器IP）
```

#### 宝塔面板安装（新手推荐）
1. 宝塔面板 → 网站 → 添加站点（选择PHP 8.0+）
2. 安装扩展：fileinfo、redis（如需缓存）
3. 网站根目录 → 远程下载/上传解压项目
4. 配置数据库信息，设置目录权限为755
5. 访问域名即可使用

##### 初始管理员账号
```
账号：admin@example.com
密码：admin123
```

### 📌 核心功能
| 模块         | 核心能力                                  |
|--------------|-------------------------------------------|
| 内容管理     | 富文本文章/瀑布流画廊/角色生成器/资讯/视频卡片 |
| 用户管理     | 邮箱注册/验证登录/密码重置/个人中心       |
| 互动系统     | 评论回复/点赞收藏/实时弹幕                |
| 后台管理     | 内容管控/用户权限/系统配置/数据备份        |

### ❓ 常见问题
<details>
<summary>无法上传图片</summary>
1. 检查uploads目录权限是否为755；2. 调整PHP上传文件大小限制；3. 确认图片格式为jpg/png/gif。
</details>

<details>
<summary>网站加载缓慢</summary>
1. 启用Redis缓存；2. 压缩图片资源；3. 检查服务器带宽/CPU占用；4. 开启CDN加速。
</details>

<details>
<summary>后台登录失败</summary>
1. 核对账号密码；2. 检查数据库连接配置；3. 清除浏览器缓存/Cookie。
</details>

### 🤝 贡献指南
1. Fork本仓库 → 克隆到本地 → 创建功能分支（feature/xxx）
2. 开发功能/修复Bug → 提交代码（git commit -m "feat: 新增XXX功能"）
3. 推送分支 → 创建Pull Request → 等待审核合并

### 📞 联系我们
- 邮箱：2208850891@qq.com
- GitHub：https://github.com/aipaigyx/Sakura-Dreamland-
- B站：https://space.bilibili.com/12644772
- QQ群：1081909009

---

## 英文版本 | English Version
A modern blog system tailored for otaku, focusing on ACG (Anime, Comics, Games) and two-dimensional culture sharing. It balances aesthetics and practicality, ready to use out of the box and easy to extend.

### ✨ Core Features
- **🎨 Otaku Responsive Design**：Built with Tailwind CSS, adaptive to all devices, supports theme customization
- **📦 Full Content Modules**：Articles/Image Waterfall Gallery/Character Generator/News/Video Cards, meeting diverse display needs
- **💬 Immersive Interaction**：Comments, Likes, Collections, Real-time Bullet Comments, build an exclusive otaku community
- **👤 Complete User System**：Registration/Login/Personal Center, with account security and personalized management
- **🛠️ Efficient Backend**：One-stop content/user/system management, easy operation and maintenance for beginners

### 🛠️ Tech Stack
| Category     | Tech List                                  |
|--------------|--------------------------------------------|
| Frontend     | HTML5 / CSS3 / JS(ES6+) / Tailwind CSS / GSAP |
| Backend      | PHP 8.x / MySQL 5.7+ / Redis 6.0+ (Optional) |
| Deployment   | Apache 2.4+ / Nginx 1.18+ / BT Panel       |
| Architecture | Modular Design + Template-driven           |

### 📂 Core Directory Structure
```bash
├── public/                # Main website access directory
│   ├── admin/            # Admin backend
│   ├── assets/           # Static resources (css/images/js)
│   ├── uploads/          # File upload directory
│   └── user/             # User center
├── app/                  # Core business logic
├── tests/                # Test files
└── vendor/               # Composer dependencies
```

### 🚀 Quick Installation
#### Environment Requirements
- PHP 8.0+ (fileinfo extension enabled), MySQL 5.7+
- Apache/Nginx, Redis 6.0+ (Optional)

#### General Server Installation
```bash
# 1. Clone the project
git clone https://github.com/aipaigyx/Sakura-Dreamland-.git
cd Sakura-Dreamland-

# 2. Create a database and import initial data
# 3. Configure database information (modify public/db.php)

# 4. Set directory permissions
chmod -R 755 public/uploads/ public/cache/ public/sessions/

# 5. Install dependencies
composer install

# 6. Access the website (open domain/server IP in browser)
```

#### BT Panel Installation (Recommended for Beginners)
1. BT Panel → Website → Add Site (Select PHP 8.0+)
2. Install extensions: fileinfo, redis (if cache is needed)
3. Website root directory → Remote download/upload and unzip the project
4. Configure database information and set directory permissions to 755
5. Access the domain name to use

##### Initial Admin Account
```
Account: admin@example.com
Password: admin123
```

### 📌 Core Functions
| Module       | Core Capabilities                          |
|--------------|--------------------------------------------|
| Content Mgmt | Rich Text Articles/Waterfall Gallery/Character Generator/News/Video Cards |
| User Mgmt    | Email Registration/Verified Login/Password Reset/Personal Center |
| Interaction  | Comment & Reply/Like & Collect/Real-time Bullet Comments |
| Backend Mgmt | Content Control/User Permissions/System Configuration/Data Backup |

### ❓ Frequently Asked Questions
<details>
<summary>Unable to upload images</summary>
1. Check if the uploads directory permission is 755; 2. Adjust PHP upload file size limit; 3. Confirm the image format is jpg/png/gif.
</details>

<details>
<summary>Slow website loading</summary>
1. Enable Redis cache; 2. Compress image resources; 3. Check server bandwidth/CPU usage; 4. Enable CDN acceleration.
</details>

<details>
<summary>Backend login failure</summary>
1. Verify account and password; 2. Check database connection configuration; 3. Clear browser cache/Cookie.
</details>

### 🤝 Contribution Guide
1. Fork this repository → Clone to local → Create feature branch (feature/xxx)
2. Develop features/fix bugs → Commit code (git commit -m "feat: Add XXX function")
3. Push branch → Create Pull Request → Wait for review and merge

### 📞 Contact Us
- Email: 2208850891@qq.com
- GitHub: https://github.com/aipaigyx/Sakura-Dreamland-
- Bilibili: https://space.bilibili.com/12644772
- QQ Group: 1081909009

---

## 日本語版本 | Japanese Version
二次元愛好者向けに開発されたモダンなブログシステムで、アニメ・ゲーム・二次元カルチャーのコンテンツシェアに特化。美観と実用性を両立し、即時使用可能で拡張が簡単な特徴を持つ。

### ✨ コア機能
- **🎨 二次元レスポンシブデザイン**：Tailwind CSSに基づき開発、全デバイス対応、テーマカスタマイズ対応
- **📦 フルコンテンツモジュール**：記事/イメージギャラリー/キャラクタージェネレーター/ニュース/ビデオカード、多様な表示ニーズに対応
- **💬 没入型インタラクション**：コメント・いいね・コレクション・リアルタイム弾幕、専用の二次元コミュニティ構築
- **👤 完全なユーザーシステム**：登録/ログイン/マイページ、アカウントセキュリティとパーソナライズ管理を両立
- **🛠️ 高効率バックエンド**：ワンストップのコンテンツ/ユーザー/システム管理、初心者でも簡単に運用・保守可能

### 🛠️ テックスタック
| カテゴリ     | 技術リスト                                |
|--------------|-------------------------------------------|
| フロントエンド | HTML5 / CSS3 / JS(ES6+) / Tailwind CSS / GSAP |
| バックエンド  | PHP 8.x / MySQL 5.7+ / Redis 6.0+（オプション） |
| デプロイ環境  | Apache 2.4+ / Nginx 1.18+ / 宝塔パネル    |
| アーキテクチャ | モジュール式デザイン + テンプレート駆動   |

### 📂 コアディレクトリ構造
```bash
├── public/                # メインサイトアクセスディレクトリ
│   ├── admin/            # 管理バックエンド
│   ├── assets/           # 静的リソース（css/images/js）
│   ├── uploads/          # ファイルアップロードディレクトリ
│   └── user/             # ユーザーセンター
├── app/                  # コアビジネスロジック
├── tests/                # テストファイル
└── vendor/               # Composer依存ファイル
```

### 🚀 クイックインストール
#### 環境要件
- PHP 8.0+（fileinfo拡張機能を有効にする）、MySQL 5.7+
- Apache/Nginx、Redis 6.0+（オプション）

#### 汎用サーバーインストール
```bash
# 1. プロジェクトをクローン
git clone https://github.com/aipaigyx/Sakura-Dreamland-.git
cd Sakura-Dreamland-

# 2. データベース作成し、初期データをインポート
# 3. データベース情報を設定（public/db.phpを修正）

# 4. ディレクトリ権限を設定
chmod -R 755 public/uploads/ public/cache/ public/sessions/

# 5. 依存ファイルをインストール
composer install

# 6. サイトにアクセス（ブラウザでドメイン/サーバーIPを開く）
```

#### 宝塔パネルインストール（初心者推奨）
1. 宝塔パネル → ウェブサイト → サイトを追加（PHP 8.0+を選択）
2. 拡張機能をインストール：fileinfo、redis（キャッシュが必要な場合）
3. サイトルートディレクトリ → リモートダウンロード/アップロードしてプロジェクトを解凍
4. データベース情報を設定し、ディレクトリ権限を755に設定
5. ドメイン名にアクセスすれば使用可能

##### 初期管理者アカウント
```
アカウント：admin@example.com
パスワード：admin123
```

### 📌 主要機能
| モジュール   | コア機能                                  |
|--------------|-------------------------------------------|
| コンテンツ管理 | リッチテキスト記事/滝流しギャラリー/キャラクタージェネレーター/ニュース/ビデオカード |
| ユーザー管理  | メール登録/認証ログイン/パスワードリセット/マイページ |
| インタラクション | コメント返信/いいねコレクション/リアルタイム弾幕 |
| バックエンド管理 | コンテンツ制御/ユーザー権限/システム設定/データバックアップ |

### ❓ よくある質問
<details>
<summary>画像アップロードができない</summary>
1. uploadsディレクトリの権限が755か確認；2. PHPのファイルアップロードサイズ制限を調整；3. 画像形式がjpg/png/gifか確認。
</details>

<details>
<summary>サイトの読み込みが遅い</summary>
1. Redisキャッシュを有効にする；2. 画像リソースを圧縮する；3. サーバーの帯域幅/CPU使用率を確認；4. CDN加速を有効にする。
</details>

<details>
<summary>バックエンドのログインに失敗する</summary>
1. アカウントとパスワードを確認；2. データベース接続設定を確認；3. ブラウザのキャッシュ/Cookieをクリア。
</details>

### 🤝 コントリビューションガイド
1. 本レポジトリをFork → ローカルにクローン → 機能ブランチを作成（feature/xxx）
2. 機能開発/Bug修正 → コードをコミット（git commit -m "feat: XXX機能を追加"）
3. ブランチをプッシュ → Pull Requestを作成 → レビューとマージを待つ

### 📞 お問い合わせ
- メール：2208850891@qq.com
- GitHub：https://github.com/aipaigyx/Sakura-Dreamland-
- Bilibili：https://space.bilibili.com/12644772
- QQグループ：1081909009

---
 
