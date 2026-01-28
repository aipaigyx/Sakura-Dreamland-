<?php
/**
 * AniBlog 主题核心功能文件
 * 类似WordPress主题的functions.php
 */

// 定义常量
define('ANIBLOG_VERSION', '1.0.0');
define('ANIBLOG_TEMPLATE_DIR', __DIR__);

// 动态生成资产URL，确保在任何访问方式下都能正确加载资源
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base_url = $protocol . '://' . $host;
define('ANIBLOG_BASE_URL', $base_url);
define('ANIBLOG_ASSETS_URL', $base_url . '/assets');

// 会话常量
define('ANIBLOG_SESSION_DIR', __DIR__ . '/sessions');

// 设置会话保存路径
session_save_path(ANIBLOG_SESSION_DIR);

// 确保会话目录存在
if (!is_dir(ANIBLOG_SESSION_DIR)) {
    mkdir(ANIBLOG_SESSION_DIR, 0755, true);
}

// 启动会话（确保只启动一次）
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 缓存常量
define('ANIBLOG_CACHE_DIR', __DIR__ . '/cache');
define('ANIBLOG_CACHE_EXPIRY', 3600); // 缓存过期时间，单位秒

// 创建缓存目录（如果不存在）
if (!is_dir(ANIBLOG_CACHE_DIR)) {
    mkdir(ANIBLOG_CACHE_DIR, 0755, true);
}

// 加载数据库连接文件
require_once __DIR__ . '/db.php';



// 简单的路由函数
function aniblog_get_current_page() {
    $uri = $_SERVER['REQUEST_URI'];
    $uri = trim($uri, '/');
    
    // 解析URL，确定当前页面
    if (empty($uri) || $uri === 'index.html' || $uri === 'index.php') {
        return 'home';
    } elseif (strpos($uri, 'article') !== false) {
        return 'article';
    } elseif (strpos($uri, 'articles') !== false) {
        return 'articles';
    } elseif (strpos($uri, 'gallery') !== false) {
        return 'gallery';
    } elseif (strpos($uri, 'character-generator') !== false || strpos($uri, 'character.php') !== false) {
        return 'character';
    } elseif (strpos($uri, 'submit') !== false) {
        return 'submit';
    } elseif (strpos($uri, 'login') !== false) {
        return 'login';
    } elseif (strpos($uri, 'register') !== false) {
        return 'register';
    } else {
        return 'home';
    }
}

// 获取页面标题
function aniblog_get_page_title() {
    $page = aniblog_get_current_page();
    
    // 文章详情页特殊处理，使用文章标题作为页面标题
    if ($page === 'article' && isset($GLOBALS['article'])) {
        return $GLOBALS['article']['title'] . ' - 樱花梦境';
    }
    
    $titles = array(
        'home' => '樱花梦境 - 二次元风格博客',
        'article' => '文章详情 - 樱花梦境',
        'gallery' => '图片画廊 - 樱花梦境',
        'character' => '角色生成器 - 樱花梦境'
    );
    
    return isset($titles[$page]) ? $titles[$page] : $titles['home'];
}

// 加载CSS文件
function aniblog_enqueue_styles() {
    // 不再加载旧的style.css，使用统一的unified.css文件
    // echo '<link rel="stylesheet" href="' . ANIBLOG_ASSETS_URL . '/css/style.css">';
}

// 加载JavaScript文件
function aniblog_enqueue_scripts() {
    $page = aniblog_get_current_page();
    
    echo '<script src="' . ANIBLOG_ASSETS_URL . '/js/main.js"></script>';
    echo '<script src="' . ANIBLOG_ASSETS_URL . '/js/interactivity.js"></script>';
    
    // 根据页面加载特定脚本
    switch ($page) {
        case 'article':
            echo '<script src="' . ANIBLOG_ASSETS_URL . '/js/danmaku.js"></script>';
            break;
        case 'gallery':
            echo '<script src="' . ANIBLOG_ASSETS_URL . '/js/gallery.js"></script>';
            break;
        case 'character':
            echo '<script src="' . ANIBLOG_ASSETS_URL . '/js/character-generator.js"></script>';
            break;
    }
}

// 获取最新文章列表
function aniblog_get_latest_articles($limit = 3) {
    $sql = "SELECT * FROM articles ORDER BY created_at DESC LIMIT ?";
    return db_query($sql, [$limit]);
}

// 获取文章详情
function aniblog_get_article($id) {
    $sql = "SELECT a.*, u.avatar as author_avatar FROM articles a LEFT JOIN users u ON a.author_id = u.id WHERE a.id = ?";
    return db_query_one($sql, [$id]);
}

// 获取画廊图片列表
function aniblog_get_gallery_images($limit = 8) {
    $sql = "SELECT * FROM images ORDER BY created_at DESC LIMIT ?";
    return db_query($sql, [$limit]);
}

// 获取所有分类
function aniblog_get_categories() {
    $sql = "SELECT * FROM categories ORDER BY name";
    return db_query($sql);
}

// 获取所有文章（带分类信息）
function get_all_articles() {
    $sql = "SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.created_at DESC";
    return db_query($sql);
}

// 获取所有图片（带分类信息）
function get_all_images() {
    $sql = "SELECT i.*, c.name as category_name FROM images i LEFT JOIN categories c ON i.category_id = c.id ORDER BY i.created_at DESC";
    return db_query($sql);
}

// 获取所有评论（带文章信息）
function get_all_comments() {
    $sql = "SELECT c.*, a.title as article_title FROM comments c LEFT JOIN articles a ON c.article_id = a.id ORDER BY c.created_at DESC";
    return db_query($sql);
}

// 获取统计数据
function get_statistics() {
    $stats = [];
    
    // 获取文章数量
    $sql = "SELECT COUNT(*) as count FROM articles";
    $stats['articles'] = db_query_one($sql)['count'];
    
    // 获取图片数量
    $sql = "SELECT COUNT(*) as count FROM images";
    $stats['images'] = db_query_one($sql)['count'];
    
    // 获取评论数量
    $sql = "SELECT COUNT(*) as count FROM comments";
    $stats['comments'] = db_query_one($sql)['count'];
    
    // 获取用户数量
    $sql = "SELECT COUNT(*) as count FROM users";
    $stats['users'] = db_query_one($sql)['count'];
    
    return $stats;
}

// 获取最新文章
function get_latest_articles($limit = 5) {
    $sql = "SELECT * FROM articles ORDER BY created_at DESC LIMIT ?";
    return db_query($sql, [$limit]);
}

// 获取最新评论
function get_latest_comments($limit = 5) {
    $sql = "SELECT c.*, a.title as article_title FROM comments c LEFT JOIN articles a ON c.article_id = a.id ORDER BY c.created_at DESC LIMIT ?";
    return db_query($sql, [$limit]);
}

// 权限管理函数

/**
 * 检查用户是否具有特定权限
 * @param string $role 用户角色
 * @param string $permission 权限名称
 * @return bool 是否具有权限
 */
function check_permission($role, $permission) {
    // 定义角色权限映射
    $permissions = [
        'admin' => [
            'manage_articles' => true,
            'manage_images' => true,
            'manage_comments' => true,
            'manage_users' => true,
            'manage_settings' => true
        ],
        'editor' => [
            'manage_articles' => true,
            'manage_images' => true,
            'manage_comments' => true,
            'manage_users' => false,
            'manage_settings' => false
        ],
        'user' => [
            'manage_articles' => false,
            'manage_images' => false,
            'manage_comments' => false,
            'manage_users' => false,
            'manage_settings' => false
        ]
    ];
    
    return isset($permissions[$role][$permission]) && $permissions[$role][$permission] === true;
}

/**
 * 获取用户角色名称
 * @param string $role 角色代码
 * @return string 角色名称
 */
function get_role_name($role) {
    $role_names = [
        'admin' => '管理员',
        'editor' => '编辑',
        'user' => '普通用户'
    ];
    
    return isset($role_names[$role]) ? $role_names[$role] : $role_names['user'];
}

/**
 * 获取网站设置
 * @return array 网站设置数组
 */
/**
 * 获取单个设置值
 * @param string $option_name 设置名称
 * @param mixed $default 默认值
 * @return mixed 设置值
 */
function get_setting($option_name, $default = '') {
    $sql = "SELECT option_value FROM settings WHERE option_name = ?";
    $result = db_query($sql, [$option_name]);
    
    if (!empty($result)) {
        $value = $result[0]['option_value'];
        // 尝试将JSON字符串转换为数组或对象
        $json_decoded = json_decode($value, true);
        return $json_decoded !== null ? $json_decoded : $value;
    }
    
    return $default;
}

/**
 * 更新单个设置值
 * @param string $option_name 设置名称
 * @param mixed $option_value 设置值
 * @return bool 是否更新成功
 */
function update_setting($option_name, $option_value) {
    // 如果是数组或对象，转换为JSON字符串
    if (is_array($option_value) || is_object($option_value)) {
        $option_value = json_encode($option_value);
    }
    
    // 检查设置是否已存在
    $sql = "SELECT id FROM settings WHERE option_name = ?";
    $result = db_query($sql, [$option_name]);
    
    if (!empty($result)) {
        // 更新现有设置
        $sql = "UPDATE settings SET option_value = ? WHERE option_name = ?";
        return db_exec($sql, [$option_value, $option_name]);
    } else {
        // 插入新设置
        $sql = "INSERT INTO settings (option_name, option_value) VALUES (?, ?)";
        return db_exec($sql, [$option_name, $option_value]);
    }
}

/**
 * 获取所有设置
 * @return array 所有设置的关联数组
 */
function get_settings() {
    $sql = "SELECT option_name, option_value FROM settings WHERE autoload = 1";
    $result = db_query($sql);
    
    $settings = [];
    if (!empty($result)) {
        foreach ($result as $row) {
            $value = $row['option_value'];
            // 尝试将JSON字符串转换为数组或对象
            $json_decoded = json_decode($value, true);
            $settings[$row['option_name']] = $json_decoded !== null ? $json_decoded : $value;
        }
    }
    
    // 设置默认值
    $defaults = [
        'site_name' => '樱花梦境',
        'site_description' => '一个关于动漫的博客网站',
        'site_keywords' => '动漫,博客,二次元',
        'site_logo' => '',
        'theme_color' => 'pink',
        'show_sidebar' => 1,
        'show_comments' => 1,
        'site_url' => '',
        'admin_email' => '',
        'timezone' => 'Asia/Shanghai',
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i:s',
        'posts_per_page' => 10,
        'comments_per_page' => 50,
        'enable_danmaku' => 1,
        'danmaku_duration_min' => 10,
        'danmaku_duration_max' => 20,
        'default_danmaku_color' => '#ffffff',
        'default_danmaku_size' => 25,
        'default_danmaku_mode' => 'scroll',
        // 邮箱设置
        'enable_email_verification' => 1,
        'email_verification_expiry' => 24,
        'email_verification_from_name' => '樱花梦境',
        'email_verification_from_email' => 'noreply@sakuradream.com',
        'enable_password_reset' => 1,
        'password_reset_expiry' => 1,
        // SMTP设置
        'email_smtp_enable' => 0,
        'email_smtp_host' => 'smtp.example.com',
        'email_smtp_port' => 587,
        'email_smtp_security' => 'tls',
        'email_smtp_username' => 'your-email@example.com',
        'email_smtp_password' => '',
        'email_smtp_auth' => 1,
        // 卡片设置
        'show_user_card' => 1,
        'show_categories_card' => 1,
        'show_articles_card' => 1,
        'show_gallery_card' => 1,
        'show_character_card' => 1,
        'show_author_card' => 1,
        'articles_per_page_home' => 3,
        'gallery_images_per_page' => 4,
        'card_border_radius' => '2xl',
        'card_shadow' => 'lg',
        'card_hover_effect' => 1
    ];
    
    // 合并默认值和数据库设置
    return array_merge($defaults, $settings);
}

/**
 * 获取所有用户
 * @return array 用户列表
 */
function get_all_users() {
    $sql = "SELECT * FROM users ORDER BY created_at DESC";
    return db_query($sql);
}

/**
 * 更新用户角色
 * @param int $user_id 用户ID
 * @param string $role 新角色
 * @return bool 是否更新成功
 */// 更新用户角色
function update_user_role($user_id, $role) {
    $sql = "UPDATE users SET role = ? WHERE id = ?";
    return db_exec($sql, [$role, $user_id]) > 0;
}

// 创建密码重置请求
function create_password_reset($user_id, $email) {
    // 生成密码重置令牌
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // 检查是否已存在重置请求
    $sql = "SELECT id FROM password_resets WHERE user_id = ?";
    $existing = db_query_one($sql, [$user_id]);
    
    if ($existing) {
        // 更新现有请求
        $sql = "UPDATE password_resets SET token = ?, expiry = ? WHERE user_id = ?";
        return db_exec($sql, [$token, $expiry, $user_id]) > 0;
    } else {
        // 创建新请求
        $sql = "INSERT INTO password_resets (user_id, email, token, expiry) VALUES (?, ?, ?, ?)";
        return db_exec($sql, [$user_id, $email, $token, $expiry]) > 0;
    }
}

// 验证密码重置令牌
function validate_password_reset_token($token) {
    $sql = "SELECT * FROM password_resets WHERE token = ? AND expiry > NOW()";
    return db_query_one($sql, [$token]);
}

// 完成密码重置
function complete_password_reset($token, $new_password) {
    // 验证令牌
    $reset_request = validate_password_reset_token($token);
    
    if ($reset_request) {
        // 更新用户密码
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if (db_exec($sql, [$hashed_password, $reset_request['user_id']]) > 0) {
            // 删除重置请求
            $sql = "DELETE FROM password_resets WHERE token = ?";
            db_exec($sql, [$token]);
            return true;
        }
    }
    
    return false;
}

// 生成邮箱验证令牌
function generate_verification_token() {
    return bin2hex(random_bytes(32));
}

// 发送邮箱验证邮件
function send_verification_email($user_id, $email, $username) {
    require_once __DIR__ . '/email.php';
    
    // 生成验证令牌
    $token = generate_verification_token();
    
    // 设置令牌过期时间（24小时）
    $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // 保存令牌到数据库
    $sql = "UPDATE users SET verification_token = ?, verification_token_expiry = ? WHERE id = ?";
    db_exec($sql, [$token, $expiry, $user_id]);
    
    // 生成验证链接
    $verification_link = ANIBLOG_BASE_URL . "/verify-email.php?token=" . $token;
    
    // 发送验证邮件
    return EmailSender::sendVerificationEmail($email, $username, $verification_link);
}

// 验证邮箱令牌
function verify_email_token($token) {
    // 查询令牌信息
    $sql = "SELECT id, username FROM users WHERE verification_token = ? AND verification_token_expiry > NOW() AND email_verified = false";
    $user = db_query_one($sql, [$token]);
    
    return $user;
}

// 更新邮箱验证状态
function update_email_verification_status($user_id) {
    // 更新用户邮箱验证状态
    $sql = "UPDATE users SET email_verified = true, verification_token = NULL, verification_token_expiry = NULL WHERE id = ?";
    return db_exec($sql, [$user_id]) > 0;
}

// 重新发送验证邮件
function resend_verification_email($email) {
    // 查询用户信息
    $sql = "SELECT id, username FROM users WHERE email = ? AND email_verified = false";
    $user = db_query_one($sql, [$email]);
    
    if ($user) {
        // 发送验证邮件
        return send_verification_email($user['id'], $email, $user['username']);
    }
    
    return false;
}

/**
 * 获取所有标签
 * @return array 标签列表
 */
function get_all_tags() {
    $sql = "SELECT * FROM tags ORDER BY name";
    return db_query($sql);
}

/**
 * 获取文章的标签
 * @param int $article_id 文章ID
 * @return array 标签列表
 */
function get_article_tags($article_id) {
    $sql = "SELECT t.* FROM tags t JOIN article_tags at ON t.id = at.tag_id WHERE at.article_id = ?";
    return db_query($sql, [$article_id]);
}

// 获取上一篇文章
function get_previous_article($article_id) {
    $sql = "SELECT a.*, u.avatar as author_avatar FROM articles a LEFT JOIN users u ON a.author_id = u.id WHERE a.id < ? ORDER BY a.id DESC LIMIT 1";
    return db_query_one($sql, [$article_id]);
}

// 获取下一篇文章
function get_next_article($article_id) {
    $sql = "SELECT a.*, u.avatar as author_avatar FROM articles a LEFT JOIN users u ON a.author_id = u.id WHERE a.id > ? ORDER BY a.id ASC LIMIT 1";
    return db_query_one($sql, [$article_id]);
}

/**
 * 为文章添加标签
 * @param int $article_id 文章ID
 * @param array $tag_ids 标签ID数组
 * @return bool 是否添加成功
 */
function add_article_tags($article_id, $tag_ids) {
    // 先删除该文章的所有标签
    $delete_sql = "DELETE FROM article_tags WHERE article_id = ?";
    db_exec($delete_sql, [$article_id]);
    
    // 添加新标签
    $success = true;
    foreach ($tag_ids as $tag_id) {
        if (is_numeric($tag_id) && $tag_id > 0) {
            $sql = "INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)";
            if (!db_exec($sql, [$article_id, $tag_id])) {
                $success = false;
            }
        }
    }
    return $success;
}

/**
 * 添加标签
 * @param string $name 标签名称
 * @param string $description 标签描述
 * @return int|false 标签ID或false
 */
function add_tag($name, $description = '') {
    $sql = "INSERT INTO tags (name, description) VALUES (?, ?)";
    if (db_exec($sql, [$name, $description])) {
        return db_last_insert_id();
    }
    return false;
}

// 数据统计和分析函数

/**
 * 获取访问统计数据
 * @param string $period 时间周期：today, week, month, year, all
 * @return array 访问统计数据
 */
function get_visit_statistics($period = 'all') {
    $sql = "SELECT COUNT(*) as count FROM articles";
    $articles_count = db_query_one($sql)['count'];
    
    $sql = "SELECT COUNT(*) as count FROM images";
    $images_count = db_query_one($sql)['count'];
    
    $sql = "SELECT COUNT(*) as count FROM comments";
    $comments_count = db_query_one($sql)['count'];
    
    $sql = "SELECT COUNT(*) as count FROM users";
    $users_count = db_query_one($sql)['count'];
    
    // 模拟访问数据（实际项目中会从数据库获取）
    $visits = [
        'today' => rand(100, 500),
        'week' => rand(700, 3500),
        'month' => rand(3000, 15000),
        'year' => rand(36000, 180000),
        'all' => rand(100000, 500000)
    ];
    
    return [
        'articles' => $articles_count,
        'images' => $images_count,
        'comments' => $comments_count,
        'users' => $users_count,
        'visits' => $visits[$period] ?? $visits['all'],
        'visits_data' => [
            ['date' => '01/01', 'visits' => rand(100, 500)],
            ['date' => '01/02', 'visits' => rand(100, 500)],
            ['date' => '01/03', 'visits' => rand(100, 500)],
            ['date' => '01/04', 'visits' => rand(100, 500)],
            ['date' => '01/05', 'visits' => rand(100, 500)],
            ['date' => '01/06', 'visits' => rand(100, 500)],
            ['date' => '01/07', 'visits' => rand(100, 500)]
        ]
    ];
}

/**
 * 获取流量来源数据
 * @return array 流量来源数据
 */
function get_traffic_sources() {
    // 模拟流量来源数据（实际项目中会从数据库获取）
    return [
        ['source' => '直接访问', 'percentage' => 45, 'color' => '#FF6B8B'],
        ['source' => '搜索引擎', 'percentage' => 30, 'color' => '#8B5CF6'],
        ['source' => '社交媒体', 'percentage' => 15, 'color' => '#3B82F6'],
        ['source' => '外部链接', 'percentage' => 10, 'color' => '#10B981']
    ];
}

/**
 * 获取热门内容数据
 * @param string $type 内容类型：articles, images, comments
 * @param int $limit 结果数量
 * @return array 热门内容数据
 */
function get_popular_content($type = 'articles', $limit = 5) {
    switch ($type) {
        case 'articles':
            $sql = "SELECT * FROM articles ORDER BY view_count DESC LIMIT ?";
            return db_query($sql, [$limit]);
            break;
        case 'images':
            $sql = "SELECT * FROM images ORDER BY view_count DESC LIMIT ?";
            return db_query($sql, [$limit]);
            break;
        case 'comments':
            $sql = "SELECT c.*, a.title as article_title FROM comments c LEFT JOIN articles a ON c.article_id = a.id ORDER BY created_at DESC LIMIT ?";
            return db_query($sql, [$limit]);
            break;
        default:
            return [];
    }
}

/**
 * 获取用户行为数据
 * @return array 用户行为数据
 */
function get_user_behavior() {
    // 模拟用户行为数据（实际项目中会从数据库获取）
    return [
        ['action' => '浏览文章', 'count' => rand(1000, 5000)],
        ['action' => '查看图片', 'count' => rand(800, 4000)],
        ['action' => '发表评论', 'count' => rand(200, 1000)],
        ['action' => '生成角色', 'count' => rand(150, 750)],
        ['action' => '上传图片', 'count' => rand(100, 500)]
    ];
}

// 获取所有首页区域
function get_home_sections() {
    $sql = "SELECT * FROM home_sections WHERE enabled = 1 ORDER BY sort_order ASC, id ASC";
    return cached_query($sql, [], 3600); // 缓存1小时
}

// 获取指定区域的所有卡片
function get_section_cards($section_id) {
    $sql = "SELECT * FROM home_cards WHERE section_id = ? AND enabled = 1 ORDER BY sort_order ASC, id ASC";
    return cached_query($sql, [$section_id], 3600); // 缓存1小时
}

// 获取指定区域名称的卡片
function get_cards_by_section_name($section_name) {
    $sql = "SELECT hc.* FROM home_cards hc
            JOIN home_sections hs ON hc.section_id = hs.id
            WHERE hs.name = ? AND hs.enabled = 1 AND hc.enabled = 1
            ORDER BY hc.sort_order ASC, hc.id ASC";
    return cached_query($sql, [$section_name], 3600); // 缓存1小时
}

// 添加首页区域
function add_home_section($data) {
    $sql = "INSERT INTO home_sections (name, display_name, enabled, sort_order) VALUES (?, ?, ?, ?)";
    $result = db_exec($sql, [
        $data['name'],
        $data['display_name'],
        $data['enabled'] ?? 1,
        $data['sort_order'] ?? 0
    ]);
    if ($result) {
        // 清除相关缓存
        clear_cache();
    }
    return $result;
}

// 更新首页区域
function update_home_section($id, $data) {
    // 先获取当前区域信息
    $sql = "SELECT * FROM home_sections WHERE id = ?";
    $current_section = db_query_one($sql, [$id]);
    
    if (!$current_section) {
        return false;
    }
    
    // 使用当前值作为默认值，只更新传递了的字段
    $name = $data['name'] ?? $current_section['name'];
    $display_name = $data['display_name'] ?? $current_section['display_name'];
    $enabled = $data['enabled'] ?? $current_section['enabled'];
    $sort_order = $data['sort_order'] ?? $current_section['sort_order'];
    
    $sql = "UPDATE home_sections SET name = ?, display_name = ?, enabled = ?, sort_order = ? WHERE id = ?";
    $result = db_exec($sql, [
        $name,
        $display_name,
        $enabled,
        $sort_order,
        $id
    ]);
    if ($result) {
        // 清除相关缓存
        clear_cache();
    }
    return $result;
}

// 删除首页区域
function delete_home_section($id) {
    $sql = "DELETE FROM home_sections WHERE id = ?";
    $result = db_exec($sql, [$id]);
    if ($result) {
        // 清除相关缓存
        clear_cache();
    }
    return $result;
}

// 添加首页卡片
function add_home_card($data) {
    $sql = "INSERT INTO home_cards (section_id, card_type, title, content, settings, enabled, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $result = db_exec($sql, [
        $data['section_id'],
        $data['card_type'],
        $data['title'],
        $data['content'] ?? null,
        json_encode($data['settings'] ?? []),
        $data['enabled'] ?? 1,
        $data['sort_order'] ?? 0
    ]);
    if ($result) {
        // 清除相关缓存
        clear_cache();
    }
    return $result;
}

// 更新首页卡片
function update_home_card($id, $data) {
    $sql = "UPDATE home_cards SET section_id = ?, card_type = ?, title = ?, content = ?, settings = ?, enabled = ?, sort_order = ? WHERE id = ?";
    $result = db_exec($sql, [
        $data['section_id'],
        $data['card_type'],
        $data['title'],
        $data['content'] ?? null,
        json_encode($data['settings'] ?? []),
        $data['enabled'] ?? 1,
        $data['sort_order'] ?? 0,
        $id
    ]);
    if ($result) {
        // 清除相关缓存
        clear_cache();
    }
    return $result;
}

// 删除首页卡片
function delete_home_card($id) {
    $sql = "DELETE FROM home_cards WHERE id = ?";
    $result = db_exec($sql, [$id]);
    if ($result) {
        // 清除相关缓存
        clear_cache();
    }
    return $result;
}

// 重新排序卡片
function reorder_home_cards($section_id, $order) {
    // 开启事务
    $pdo = get_db_connection();
    if (!$pdo) {
        return false;
    }
    
    try {
        $pdo->beginTransaction();
        
        foreach ($order as $card) {
            $sql = "UPDATE home_cards SET sort_order = ? WHERE id = ? AND section_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$card['sort_order'], $card['id'], $section_id]);
        }
        
        $pdo->commit();
        // 清除相关缓存
        clear_cache();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

// 初始化数据库表
function aniblog_init_database() {
    // 创建系统设置表
    $sql = "CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        option_name VARCHAR(100) NOT NULL UNIQUE,
        option_value TEXT,
        autoload TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    db_exec($sql);
    
    // 创建分类表
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    db_exec($sql);
    
    // 创建文章表
    $sql = "CREATE TABLE IF NOT EXISTS articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        summary TEXT,
        cover_image VARCHAR(255),
        author_id INT DEFAULT 1,
        category_id INT,
        view_count INT DEFAULT 0,
        comment_count INT DEFAULT 0,
        like_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )";
    db_exec($sql);
    
    // 创建文章点赞表
    $sql = "CREATE TABLE IF NOT EXISTS article_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        article_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_article (user_id, article_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
    )";
    db_exec($sql);
    
    // 创建图片表
    $sql = "CREATE TABLE IF NOT EXISTS images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        file_path VARCHAR(255) NOT NULL,
        user_id INT DEFAULT 1,
        category_id INT,
        view_count INT DEFAULT 0,
        like_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )";
    db_exec($sql);
    
    // 创建评论表
    $sql = "CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content TEXT NOT NULL,
        user_id INT DEFAULT 1,
        name VARCHAR(100) DEFAULT '',
        email VARCHAR(100) DEFAULT '',
        article_id INT,
        parent_id INT DEFAULT NULL,
        likes INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
        FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
    )";
    db_exec($sql);
    
    // 创建评论点赞表
    $sql = "CREATE TABLE IF NOT EXISTS comment_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        comment_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_comment (user_id, comment_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE
    )";
    db_exec($sql);
    
    // 创建收藏表
    $sql = "CREATE TABLE IF NOT EXISTS favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        article_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_article (user_id, article_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
    )";
    db_exec($sql);
    
    // 创建角色表
    $sql = "CREATE TABLE IF NOT EXISTS characters (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        user_id INT DEFAULT 1,
        attributes JSON,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    db_exec($sql);
    
    // 创建投稿表
    $sql = "CREATE TABLE IF NOT EXISTS submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        article_id INT NOT NULL,
        submitter_name VARCHAR(100) NOT NULL,
        submitter_email VARCHAR(255) NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
    )";
    db_exec($sql);
    
    // 创建用户表
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        avatar VARCHAR(255),
        role ENUM('admin', 'editor', 'user') DEFAULT 'user',
        email_verified BOOLEAN DEFAULT false,
        verification_token VARCHAR(255),
        verification_token_expiry TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    db_exec($sql);
    
    // 创建标签表
    $sql = "CREATE TABLE IF NOT EXISTS tags (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    db_exec($sql);
    
    // 创建文章标签关联表
    $sql = "CREATE TABLE IF NOT EXISTS article_tags (
        id INT AUTO_INCREMENT PRIMARY KEY,
        article_id INT NOT NULL,
        tag_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_article_tag (article_id, tag_id),
        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
        FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
    )";
    db_exec($sql);
    
    // 创建弹幕表
    $sql = "CREATE TABLE IF NOT EXISTS danmaku (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content VARCHAR(255) NOT NULL,
        color VARCHAR(20) DEFAULT '#ffffff',
        size INT DEFAULT 24,
        mode VARCHAR(20) DEFAULT 'scroll',
        user_id INT DEFAULT 1,
        article_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
    )";
    db_exec($sql);
    
    // 创建首页区域表
    $sql = "CREATE TABLE IF NOT EXISTS home_sections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        display_name VARCHAR(100) NOT NULL,
        enabled TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    db_exec($sql);
    
    // 创建首页卡片表
    $sql = "CREATE TABLE IF NOT EXISTS home_cards (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section_id INT NOT NULL,
        card_type VARCHAR(50) NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        settings JSON DEFAULT '{}',
        enabled TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (section_id) REFERENCES home_sections(id) ON DELETE CASCADE
    )";
    db_exec($sql);
    
    // 添加默认分类
    $sql = "INSERT IGNORE INTO categories (id, name, description) VALUES (1, '动漫资讯', '最新的动漫新闻和资讯'), (2, '创作分享', '用户创作的内容分享'), (3, '漫评', '动漫评论和分析')";
    db_exec($sql);
    
    // 添加默认首页区域
    $sql = "INSERT IGNORE INTO home_sections (id, name, display_name, sort_order) VALUES 
        (1, 'hero', '英雄区', 10),
        (2, 'main', '主内容区', 20),
        (3, 'sidebar', '侧边栏', 30),
        (4, 'footer', '底部区', 40)";
    db_exec($sql);
    
    // 添加默认标签
    $sql = "INSERT IGNORE INTO tags (id, name) VALUES (1, '新番推荐'), (2, '动漫资讯'), (3, '角色分析'), (4, '剧情讨论'), (5, '图片分享')";
    db_exec($sql);
    
    // 添加默认用户
    $sql = "INSERT IGNORE INTO users (id, username, email, password, role) VALUES (1, 'admin', 'admin@example.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin')";
    db_exec($sql);
}

// 缓存相关函数

/**
 * 设置缓存
 * @param string $key 缓存键名
 * @param mixed $value 缓存值
 * @param int $expiry 过期时间（秒），默认使用全局设置
 * @return bool 是否设置成功
 */
function set_cache($key, $value, $expiry = ANIBLOG_CACHE_EXPIRY) {
    $cache_file = ANIBLOG_CACHE_DIR . '/' . md5($key) . '.cache';
    $cache_data = [
        'value' => $value,
        'expiry' => time() + $expiry
    ];
    
    return file_put_contents($cache_file, json_encode($cache_data)) !== false;
}

/**
 * 获取缓存
 * @param string $key 缓存键名
 * @return mixed 缓存值，失败或过期返回false
 */
function get_cache($key) {
    $cache_file = ANIBLOG_CACHE_DIR . '/' . md5($key) . '.cache';
    
    if (!file_exists($cache_file)) {
        return false;
    }
    
    $cache_data = json_decode(file_get_contents($cache_file), true);
    if (!$cache_data || !isset($cache_data['value']) || !isset($cache_data['expiry'])) {
        return false;
    }
    
    // 检查是否过期
    if (time() > $cache_data['expiry']) {
        // 删除过期缓存
        unlink($cache_file);
        return false;
    }
    
    return $cache_data['value'];
}

/**
 * 清除缓存
 * @param string $key 缓存键名，可选，如果不提供则清除所有缓存
 * @return bool 是否清除成功
 */
function clear_cache($key = null) {
    if ($key) {
        // 清除指定缓存
        $cache_file = ANIBLOG_CACHE_DIR . '/' . md5($key) . '.cache';
        if (file_exists($cache_file)) {
            return unlink($cache_file);
        }
        return true;
    } else {
        // 清除所有缓存
        $dir = opendir(ANIBLOG_CACHE_DIR);
        if (!$dir) {
            return false;
        }
        
        $success = true;
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $file_path = ANIBLOG_CACHE_DIR . '/' . $file;
                if (!unlink($file_path)) {
                    $success = false;
                }
            }
        }
        closedir($dir);
        return $success;
    }
}

/**
 * 带缓存的查询函数
 * @param string $sql SQL查询语句
 * @param array $params 参数数组
 * @param int $expiry 过期时间（秒）
 * @return array 查询结果
 */
function cached_query($sql, $params = [], $expiry = ANIBLOG_CACHE_EXPIRY) {
    $cache_key = 'query_' . md5($sql . serialize($params));
    
    // 尝试从缓存获取
    $result = get_cache($cache_key);
    if ($result !== false) {
        return $result;
    }
    
    // 缓存不存在，执行查询
    $result = db_query($sql, $params);
    
    // 将结果存入缓存
    set_cache($cache_key, $result, $expiry);
    
    return $result;
}

/**
 * 带缓存的单条查询函数
 * @param string $sql SQL查询语句
 * @param array $params 参数数组
 * @param int $expiry 过期时间（秒）
 * @return array|null 查询结果或null
 */
function cached_query_one($sql, $params = [], $expiry = ANIBLOG_CACHE_EXPIRY) {
    $cache_key = 'query_one_' . md5($sql . serialize($params));
    
    // 尝试从缓存获取
    $result = get_cache($cache_key);
    if ($result !== false) {
        return $result;
    }
    
    // 缓存不存在，执行查询
    $result = db_query_one($sql, $params);
    
    // 将结果存入缓存
    set_cache($cache_key, $result, $expiry);
    
    return $result;
}

/**
 * 创建新通知
 * @param int $user_id 接收通知的用户ID
 * @param string $type 通知类型
 * @param string $title 通知标题
 * @param string $content 通知内容
 * @param int $related_id 相关资源ID
 * @param string $related_type 相关资源类型
 * @return int|false 通知ID或false
 */
function create_notification($user_id, $type, $title, $content, $related_id = null, $related_type = null) {
    $sql = "INSERT INTO notifications (user_id, type, title, content, related_id, related_type) VALUES (?, ?, ?, ?, ?, ?)";
    $result = db_exec($sql, [$user_id, $type, $title, $content, $related_id, $related_type]);
    
    if ($result) {
        return db_last_insert_id();
    }
    return false;
}

/**
 * 获取用户通知列表
 * @param int $user_id 用户ID
 * @param int $limit 限制数量
 * @param int $offset 偏移量
 * @param bool $only_unread 是否只获取未读通知
 * @return array 通知列表
 */
function get_user_notifications($user_id, $limit = 20, $offset = 0, $only_unread = false) {
    // 验证用户ID
    if (!is_numeric($user_id) || $user_id <= 0) {
        return [];
    }
    
    $where = $only_unread ? "WHERE user_id = ? AND is_read = 0" : "WHERE user_id = ?";
    $sql = "SELECT * FROM notifications $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params = $only_unread ? [$user_id, $limit, $offset] : [$user_id, $limit, $offset];
    
    return db_query($sql, $params);
}

/**
 * 获取未读通知数量
 * @param int $user_id 用户ID
 * @return int 未读通知数量
 */
function get_unread_notification_count($user_id) {
    // 验证用户ID
    if (!is_numeric($user_id) || $user_id <= 0) {
        return 0;
    }
    
    $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
    $result = db_query_one($sql, [$user_id]);
    
    return $result['count'] ?? 0;
}

/**
 * 标记通知为已读
 * @param int $notification_id 通知ID
 * @param int $user_id 用户ID（确保只能操作自己的通知）
 * @return bool 是否成功
 */
function mark_notification_as_read($notification_id, $user_id) {
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    $result = db_exec($sql, [$notification_id, $user_id]);
    
    return $result !== false && $result > 0;
}

/**
 * 标记所有通知为已读
 * @param int $user_id 用户ID
 * @return bool 是否成功
 */
function mark_all_notifications_as_read($user_id) {
    $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
    $result = db_exec($sql, [$user_id]);
    
    return $result !== false;
}

/**
 * 删除通知
 * @param int $notification_id 通知ID
 * @param int $user_id 用户ID（确保只能操作自己的通知）
 * @return bool 是否成功
 */
function delete_notification($notification_id, $user_id) {
    $sql = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
    $result = db_exec($sql, [$notification_id, $user_id]);
    
    return $result !== false && $result > 0;
}

// 调用初始化函数
aniblog_init_database();

