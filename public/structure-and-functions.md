# 樱花梦境网站结构与函数说明

## 一、网站结构

### 1. 目录结构
```
/www/wwwroot/gre/
├── public/
│   ├── admin/           # 后台管理页面
│   │   ├── articles.php     # 文章管理
│   │   ├── auth.php         # 权限管理
│   │   ├── comments.php     # 评论管理
│   │   ├── dashboard.php    # 后台仪表盘
│   │   ├── edit-article.php # 编辑文章
│   │   ├── footer.php       # 后台页脚
│   │   ├── gallery.php      # 图片管理
│   │   ├── header.php       # 后台头部
│   │   ├── login.php        # 后台登录
│   │   └── logout.php       # 后台登出
│   ├── user/            # 用户中心页面
│   │   ├── comments.php     # 用户评论
│   │   ├── favorites.php    # 用户收藏
│   │   ├── notifications.php # 通知设置
│   │   ├── password.php     # 密码修改
│   │   ├── privacy.php      # 隐私设置
│   │   ├── profile.php      # 用户资料
│   │   └── settings.php     # 通用设置
│   ├── add-sample-article.php        # 添加示例文章
│   ├── add-sample-gutenberg-article.php # 添加示例古腾堡文章
│   ├── add-sample-images.php         # 添加示例图片
│   ├── api.php              # API接口
│   ├── article.php          # 文章详情页
│   ├── articles.php         # 文章列表页
│   ├── character.php        # 角色生成器
│   ├── comment-like.php     # 评论点赞
│   ├── comment-moderate.php # 评论审核
│   ├── comment.php          # 评论提交
│   ├── create-admin.php     # 创建管理员
│   ├── db.php               # 数据库连接
│   ├── favorite.php         # 收藏功能
│   ├── footer.php           # 前台页脚
│   ├── functions.php        # 核心功能函数
│   ├── gallery.php          # 图片画廊
│   ├── header.php           # 前台头部
│   ├── home.php             # 首页内容
│   ├── index.php            # 网站入口
│   ├── init-favorite-table.php # 初始化收藏表
│   ├── like.php             # 文章点赞
│   ├── login.php            # 前台登录
│   ├── logout.php           # 前台登出
│   ├── register.php         # 用户注册
│   ├── reset-admin.php      # 重置管理员密码
│   ├── test-db.php          # 数据库测试
│   ├── test.php             # 测试文件
│   ├── update_comment_table.php # 更新评论表
│   └── upload-image.php     # 图片上传
├── test_db_sync.php         # 数据库同步测试
└── test.php                 # 根目录测试文件
```

### 2. 核心文件说明

| 文件名 | 功能描述 |
|--------|----------|
| `index.php` | 网站入口文件，处理路由 |
| `functions.php` | 核心功能函数库 |
| `db.php` | 数据库连接与操作函数 |
| `header.php` | 前台页面头部导航 |
| `footer.php` | 前台页面页脚 |
| `home.php` | 首页内容 |
| `articles.php` | 文章列表页 |
| `article.php` | 文章详情页 |
| `gallery.php` | 图片画廊 |
| `character.php` | 角色生成器 |

## 二、函数说明

### 1. 路由与页面处理函数

#### `aniblog_get_current_page()`
- **功能**：获取当前页面类型
- **参数**：无
- **返回值**：字符串，表示当前页面类型（home, article, gallery, character）
- **说明**：根据URL解析当前页面类型，用于加载不同的页面模板

#### `aniblog_get_page_title()`
- **功能**：获取当前页面标题
- **参数**：无
- **返回值**：字符串，页面完整标题
- **说明**：根据当前页面类型返回对应的页面标题

### 2. 资源加载函数

#### `aniblog_enqueue_styles()`
- **功能**：加载CSS样式文件
- **参数**：无
- **返回值**：无
- **说明**：输出CSS文件链接

#### `aniblog_enqueue_scripts()`
- **功能**：加载JavaScript脚本文件
- **参数**：无
- **返回值**：无
- **说明**：根据当前页面类型加载对应的JavaScript文件

### 3. 内容获取函数

#### `aniblog_get_latest_articles($limit = 3)`
- **功能**：获取最新文章列表
- **参数**：
  - `$limit`：整数，返回文章数量，默认3篇
- **返回值**：数组，包含最新文章信息
- **说明**：从数据库获取最新发布的文章

#### `aniblog_get_article($id)`
- **功能**：获取单篇文章详情
- **参数**：
  - `$id`：整数，文章ID
- **返回值**：数组，文章详细信息
- **说明**：根据文章ID获取完整文章信息

#### `aniblog_get_gallery_images($limit = 8)`
- **功能**：获取画廊图片列表
- **参数**：
  - `$limit`：整数，返回图片数量，默认8张
- **返回值**：数组，包含图片信息
- **说明**：从数据库获取最新上传的图片

#### `aniblog_get_categories()`
- **功能**：获取所有分类
- **参数**：无
- **返回值**：数组，包含所有分类信息
- **说明**：从数据库获取所有分类数据

#### `get_all_articles()`
- **功能**：获取所有文章（带分类信息）
- **参数**：无
- **返回值**：数组，包含所有文章信息及分类名称
- **说明**：从数据库获取所有文章，包含分类关联信息

#### `get_all_images()`
- **功能**：获取所有图片（带分类信息）
- **参数**：无
- **返回值**：数组，包含所有图片信息及分类名称
- **说明**：从数据库获取所有图片，包含分类关联信息

#### `get_all_comments()`
- **功能**：获取所有评论（带文章信息）
- **参数**：无
- **返回值**：数组，包含所有评论信息及文章标题
- **说明**：从数据库获取所有评论，包含文章关联信息

### 4. 统计与分析函数

#### `get_statistics()`
- **功能**：获取网站统计数据
- **参数**：无
- **返回值**：数组，包含文章、图片、评论、用户数量
- **说明**：从数据库获取网站核心统计数据

#### `get_latest_articles($limit = 5)`
- **功能**：获取最新文章列表
- **参数**：
  - `$limit`：整数，返回文章数量，默认5篇
- **返回值**：数组，包含最新文章信息
- **说明**：从数据库获取最新发布的文章

#### `get_latest_comments($limit = 5)`
- **功能**：获取最新评论列表
- **参数**：
  - `$limit`：整数，返回评论数量，默认5条
- **返回值**：数组，包含最新评论信息及关联文章
- **说明**：从数据库获取最新发布的评论

#### `get_visit_statistics($period = 'all')`
- **功能**：获取访问统计数据
- **参数**：
  - `$period`：字符串，时间周期（today, week, month, year, all）
- **返回值**：数组，包含访问统计数据
- **说明**：获取指定时间周期的访问统计数据

#### `get_traffic_sources()`
- **功能**：获取流量来源数据
- **参数**：无
- **返回值**：数组，包含流量来源分布
- **说明**：获取网站流量来源的分布情况

#### `get_popular_content($type = 'articles', $limit = 5)`
- **功能**：获取热门内容数据
- **参数**：
  - `$type`：字符串，内容类型（articles, images, comments）
  - `$limit`：整数，返回数量，默认5条
- **返回值**：数组，包含热门内容信息
- **说明**：根据内容类型获取热门内容

#### `get_user_behavior()`
- **功能**：获取用户行为数据
- **参数**：无
- **返回值**：数组，包含用户行为统计
- **说明**：获取用户在网站上的行为统计数据

### 5. 权限管理函数

#### `check_permission($role, $permission)`
- **功能**：检查用户是否具有特定权限
- **参数**：
  - `$role`：字符串，用户角色
  - `$permission`：字符串，权限名称
- **返回值**：布尔值，是否具有权限
- **说明**：根据角色权限映射检查用户是否具有指定权限

#### `get_role_name($role)`
- **功能**：获取用户角色名称
- **参数**：
  - `$role`：字符串，角色代码
- **返回值**：字符串，角色中文名称
- **说明**：将角色代码转换为可读性强的中文名称

#### `get_all_users()`
- **功能**：获取所有用户
- **参数**：无
- **返回值**：数组，包含所有用户信息
- **说明**：从数据库获取所有用户数据

#### `update_user_role($user_id, $role)`
- **功能**：更新用户角色
- **参数**：
  - `$user_id`：整数，用户ID
  - `$role`：字符串，新角色
- **返回值**：布尔值，是否更新成功
- **说明**：更新指定用户的角色

### 6. 数据库初始化函数

#### `aniblog_init_database()`
- **功能**：初始化数据库表结构
- **参数**：无
- **返回值**：无
- **说明**：创建所有必要的数据库表，包括分类、文章、图片、评论、角色、用户、弹幕等

## 三、数据库表结构

### 1. categories（分类表）
- `id`：INT AUTO_INCREMENT PRIMARY KEY
- `name`：VARCHAR(100) NOT NULL
- `description`：TEXT
- `created_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

### 2. articles（文章表）
- `id`：INT AUTO_INCREMENT PRIMARY KEY
- `title`：VARCHAR(255) NOT NULL
- `content`：TEXT NOT NULL
- `summary`：TEXT
- `cover_image`：VARCHAR(255)
- `author_id`：INT DEFAULT 1
- `category_id`：INT
- `view_count`：INT DEFAULT 0
- `comment_count`：INT DEFAULT 0
- `like_count`：INT DEFAULT 0
- `created_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

### 3. images（图片表）
- `id`：INT AUTO_INCREMENT PRIMARY KEY
- `title`：VARCHAR(255) NOT NULL
- `description`：TEXT
- `file_path`：VARCHAR(255) NOT NULL
- `user_id`：INT DEFAULT 1
- `category_id`：INT
- `view_count`：INT DEFAULT 0
- `like_count`：INT DEFAULT 0
- `created_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

### 4. comments（评论表）
- `id`：INT AUTO_INCREMENT PRIMARY KEY
- `content`：TEXT NOT NULL
- `user_id`：INT DEFAULT 1
- `article_id`：INT
- `parent_id`：INT DEFAULT NULL
- `created_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

### 5. characters（角色表）
- `id`：INT AUTO_INCREMENT PRIMARY KEY
- `name`：VARCHAR(255) NOT NULL
- `user_id`：INT DEFAULT 1
- `attributes`：JSON
- `image_url`：VARCHAR(255)
- `created_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

### 6. users（用户表）
- `id`：INT AUTO_INCREMENT PRIMARY KEY
- `username`：VARCHAR(100) NOT NULL UNIQUE
- `email`：VARCHAR(255) NOT NULL UNIQUE
- `password`：VARCHAR(255) NOT NULL
- `avatar`：VARCHAR(255)
- `role`：ENUM('admin', 'editor', 'user') DEFAULT 'user'
- `created_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

### 7. danmaku（弹幕表）
- `id`：INT AUTO_INCREMENT PRIMARY KEY
- `content`：VARCHAR(255) NOT NULL
- `color`：VARCHAR(20) DEFAULT '#ffffff'
- `size`：INT DEFAULT 24
- `mode`：VARCHAR(20) DEFAULT 'scroll'
- `user_id`：INT DEFAULT 1
- `article_id`：INT
- `created_at`：TIMESTAMP DEFAULT CURRENT_TIMESTAMP

## 四、API接口

### 1. 获取筛选图片
- **URL**：`/api.php?action=get_filtered_images&category=all`
- **方法**：GET
- **参数**：
  - `action`：字符串，固定为`get_filtered_images`
  - `category`：字符串/整数，分类ID或'all'
- **返回**：JSON格式，包含筛选后的图片列表

## 五、权限管理

### 1. 角色权限映射

| 权限名称 | admin | editor | user |
|----------|-------|--------|------|
| manage_articles | ✅ | ✅ | ❌ |
| manage_images | ✅ | ✅ | ❌ |
| manage_comments | ✅ | ✅ | ❌ |
| manage_users | ✅ | ❌ | ❌ |
| manage_settings | ✅ | ❌ | ❌ |

## 六、使用说明

1. **网站初始化**：首次访问时会自动创建数据库表结构
2. **默认管理员**：
   - 用户名：admin
   - 密码：admin123
   - 邮箱：admin@example.com
3. **添加内容**：
   - 可以通过后台管理页面添加文章和图片
   - 也可以使用`add-sample-article.php`和`add-sample-images.php`添加示例内容
4. **访问网站**：
   - 前台：`http://localhost/index.php`
   - 后台：`http://localhost/admin/login.php`

## 七、技术栈

- **后端**：PHP 8.2 + PDO
- **前端**：HTML5, CSS3, JavaScript (ES6+)
- **CSS框架**：Tailwind CSS
- **图标库**：Font Awesome
- **动画库**：GSAP
- **数据库**：MySQL

## 八、开发说明

1. **代码规范**：
   - 函数命名采用小驼峰命名法
   - 变量命名采用小驼峰命名法
   - 文件命名采用小写字母+下划线命名法
   - 类名采用大驼峰命名法

2. **扩展建议**：
   - 可以添加文章标签功能
   - 可以扩展角色生成器的属性选项
   - 可以添加文章分类筛选功能
   - 可以添加图片上传预览功能

3. **安全建议**：
   - 所有用户输入必须进行验证和过滤
   - 敏感数据必须进行加密存储
   - 所有数据库查询必须使用预处理语句
   - 定期备份数据库

## 九、更新日志

### 2026-01-12
- 修复了导航链接中的路径问题
- 修改了文章ID获取逻辑
- 修复了推荐文章链接
- 修复了页脚中的快速链接
- 更新了角色生成器页面链接

### 2026-01-11
- 实现了用户系统功能
- 实现了管理员后台功能
- 添加了权限管理系统
- 实现了文章、图片、评论功能
- 添加了角色生成器功能

### 2026-01-10
- 初始化项目结构
- 创建了数据库表结构
- 实现了基本的页面模板
- 添加了样式和动画效果

---

**樱花梦境网站** - 二次元文化交流平台

© 2026 樱花梦境. 保留所有权利.