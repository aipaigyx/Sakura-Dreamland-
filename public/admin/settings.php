<?php
/**
 * 系统设置页面
 */

// 包含认证中间件
require_once 'auth.php';

// 检查权限
require_permission('manage_settings');

// 设置页面标题和描述
$page_title = '系统设置';
$page_description = '管理网站的基本设置和首页排版';

// 包含公共头部
require_once 'header.php';

// 包含功能函数
require_once '../functions.php';
require_once '../db.php';

// 处理拖拽排序
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reorder_cards') {
    $section_id = $_POST['section_id'];
    $order = json_decode($_POST['order'], true);
    if (reorder_home_cards($section_id, $order)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 首页排版设置表单处理
    if (isset($_POST['form_type'])) {
        $action = $_POST['action'] ?? 'list';
        
        switch ($_POST['form_type']) {
            case 'section':
                // 区域管理
                switch ($action) {
                    case 'add_section':
                        // 添加新区域
                        $data = [
                            'name' => $_POST['name'],
                            'display_name' => $_POST['display_name'],
                            'enabled' => isset($_POST['enabled']) ? 1 : 0,
                            'sort_order' => $_POST['sort_order'] ?? 0
                        ];
                        if (add_home_section($data)) {
                            $message = '区域添加成功';
                        } else {
                            $message = '区域添加失败';
                        }
                        break;
                    
                    case 'edit_section':
                        // 编辑区域
                        $id = $_POST['id'];
                        $data = [
                            'name' => $_POST['name'],
                            'display_name' => $_POST['display_name'],
                            'enabled' => isset($_POST['enabled']) ? 1 : 0,
                            'sort_order' => $_POST['sort_order'] ?? 0
                        ];
                        $result = update_home_section($id, $data);
                        if ($result !== false) {
                            $message = '区域更新成功';
                        } else {
                            $message = '区域更新失败';
                        }
                        break;
                    
                    case 'delete_section':
                        // 删除区域
                        $id = $_POST['id'];
                        if (delete_home_section($id)) {
                            $message = '区域删除成功';
                        } else {
                            $message = '区域删除失败';
                        }
                        break;
                }
                break;
            
            case 'card':
                // 卡片管理
                switch ($action) {
                    case 'add_card':
                        // 添加新卡片
                        $data = [
                            'section_id' => $_POST['section_id'],
                            'card_type' => $_POST['card_type'],
                            'title' => $_POST['title'],
                            'content' => $_POST['content'] ?? null,
                            'settings' => [
                                'count' => $_POST['count'] ?? 3,
                                'category_id' => $_POST['category_id'] ?? null,
                                'layout' => $_POST['layout'] ?? 'auto',
                                'responsive' => $_POST['responsive'] ?? 'auto',
                                'image_height' => $_POST['image_height'] ?? 200,
                                'style' => $_POST['style'] ?? 'style3',
                                'show_meta' => $_POST['show_meta'] ?? 1,
                                'show_summary' => $_POST['show_summary'] ?? 0,
                                'hover_effect' => $_POST['hover_effect'] ?? 'scale',
                                'width' => $_POST['width'] ?? 100,
                                'margin' => $_POST['margin'] ?? 8,
                                'padding' => $_POST['padding'] ?? 12,
                                'autoplay' => $_POST['autoplay'] ?? 1,
                                'interval' => $_POST['interval'] ?? 5000
                            ],
                            'enabled' => isset($_POST['enabled']) ? 1 : 0,
                            'sort_order' => $_POST['sort_order'] ?? 0
                        ];
                        if (add_home_card($data)) {
                            $message = '卡片添加成功';
                        } else {
                            $message = '卡片添加失败';
                        }
                        break;
                    
                    case 'edit_card':
                        // 编辑卡片
                        $id = $_POST['id'];
                        $data = [
                            'section_id' => $_POST['section_id'],
                            'card_type' => $_POST['card_type'],
                            'title' => $_POST['title'],
                            'content' => $_POST['content'] ?? null,
                            'settings' => [
                                'count' => $_POST['count'] ?? 3,
                                'category_id' => $_POST['category_id'] ?? null,
                                'layout' => $_POST['layout'] ?? 'auto',
                                'responsive' => $_POST['responsive'] ?? 'auto',
                                'image_height' => $_POST['image_height'] ?? 200,
                                'style' => $_POST['style'] ?? 'style3',
                                'show_meta' => $_POST['show_meta'] ?? 1,
                                'show_summary' => $_POST['show_summary'] ?? 0,
                                'hover_effect' => $_POST['hover_effect'] ?? 'scale',
                                'width' => $_POST['width'] ?? 100,
                                'margin' => $_POST['margin'] ?? 8,
                                'padding' => $_POST['padding'] ?? 12,
                                'autoplay' => $_POST['autoplay'] ?? 1,
                                'interval' => $_POST['interval'] ?? 5000
                            ],
                            'enabled' => isset($_POST['enabled']) ? 1 : 0,
                            'sort_order' => $_POST['sort_order'] ?? 0
                        ];
                        if (update_home_card($id, $data)) {
                            $message = '卡片更新成功';
                        } else {
                            $message = '卡片更新失败';
                        }
                        break;
                    
                    case 'delete_card':
                        // 删除卡片
                        $id = $_POST['id'];
                        if (delete_home_card($id)) {
                            $message = '卡片删除成功';
                        } else {
                            $message = '卡片删除失败';
                        }
                        break;
                }
                break;
        }
    } else {
        // 处理背景图片上传
        if (isset($_FILES['custom_bg_image']) && $_FILES['custom_bg_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['custom_bg_image'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                // 生成唯一文件名
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'bg_' . time() . '.' . $ext;
                $upload_path = __DIR__ . '/../uploads/' . $filename;
                
                // 创建上传目录（如果不存在）
                if (!is_dir(__DIR__ . '/../uploads')) {
                    mkdir(__DIR__ . '/../uploads', 0755, true);
                }
                
                // 移动文件
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // 保存文件URL
                    $settings['custom_bg_image'] = '/uploads/' . $filename;
                    update_setting('custom_bg_image', $settings['custom_bg_image']);
                }
            }
        }
        
        // 处理移除背景图片
        if (isset($_POST['remove_bg_image']) && $_POST['remove_bg_image'] === '1') {
            // 删除实际文件
            if (!empty($settings['custom_bg_image'])) {
                $file_path = __DIR__ . '/..' . $settings['custom_bg_image'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            // 清除设置
            update_setting('custom_bg_image', '');
            $settings['custom_bg_image'] = '';
        }
        
        // 处理系统设置保存逻辑
        $settings_to_save = [
            'site_name',
            'site_description',
            'site_keywords',
            'site_logo',
            'theme_color',
            'show_sidebar',
            'show_comments',
            'site_url',
            'admin_email',
            'timezone',
            'date_format',
            'time_format',
            'posts_per_page',
            'comments_per_page',
            'enable_danmaku',
            'danmaku_duration_min',
            'danmaku_duration_max',
            'default_danmaku_color',
            'default_danmaku_size',
            'default_danmaku_mode',
            // 编辑器设置
            'tinymce_height',
            'tinymce_toolbar',
            'tinymce_plugins',
            'tinymce_language',
            // 图片管理设置
            'image_upload_max_size',
            'image_allowed_types',
            'image_thumbnail_size',
            'enable_image_watermark',
            'watermark_text',
            // 邮箱设置
            'enable_email_verification',
            'email_verification_expiry',
            'email_verification_from_name',
            'email_verification_from_email',
            'enable_password_reset',
            'password_reset_expiry',
            // SMTP 设置
            'email_smtp_enable',
            'email_smtp_host',
            'email_smtp_port',
            'email_smtp_security',
            'email_smtp_username',
            'email_smtp_password',
            'email_smtp_auth',
            // 背景设置
            'bg_blend_mode',
            'bg_opacity',
            // 卡片设置
            'show_user_card',
            'show_categories_card',
            'show_articles_card',
            'show_gallery_card',
            'show_character_card',
            'show_author_card',
            'articles_per_page_home',
            'gallery_images_per_page',
            'card_border_radius',
            'card_shadow',
            'card_hover_effect',
            // 页脚设置
            'footer_about_title',
            'footer_about_description',
            'footer_twitter_url',
            'footer_instagram_url',
            'footer_github_url',
            'footer_bilibili_url',
            'footer_email',
            'footer_address',
            'footer_business_hours',
            'footer_copyright_text',
            'footer_usage_guide_url',
            'footer_privacy_policy_url',
            'footer_terms_of_service_url',
            'footer_sitemap_url',
            // 伪静态设置
            'server_type',
            'custom_rewrite_rules',
            'custom_css'
        ];
        
        foreach ($settings_to_save as $setting_name) {
            if (isset($_POST[$setting_name])) {
                update_setting($setting_name, $_POST[$setting_name]);
            }
        }
    
    $message = '设置已保存';
    // 重新获取设置，确保页面显示最新值
    $settings = get_settings();
    }
}

// 获取当前设置
$settings = get_settings();

// 获取首页排版相关数据
$sections = get_home_sections();

// 获取所有卡片
$sql = "SELECT hc.*, hs.name as section_name FROM home_cards hc JOIN home_sections hs ON hc.section_id = hs.id ORDER BY hs.sort_order ASC, hc.sort_order ASC, hc.id ASC";
$cards = db_query($sql);

// 获取要编辑的区域
$section_to_edit = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit_section' && isset($_GET['section_id'])) {
    $id = $_GET['section_id'];
    $sql = "SELECT * FROM home_sections WHERE id = ?";
    $section_to_edit = db_query_one($sql, [$id]);
}

// 获取要编辑的卡片
$card_to_edit = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit_card' && isset($_GET['card_id'])) {
    $id = $_GET['card_id'];
    $sql = "SELECT * FROM home_cards WHERE id = ?";
    $card_to_edit = db_query_one($sql, [$id]);
    if ($card_to_edit) {
        $card_to_edit['settings'] = json_decode($card_to_edit['settings'], true);
    }
}

// 获取所有分类
$categories = aniblog_get_categories();
?>
        <!-- 页面内容 -->
        <div class="container mx-auto px-4 py-8">
            <!-- 页面标题 -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800"><?php echo $page_title; ?></h1>
                <p class="text-gray-600 mt-2"><?php echo $page_description; ?></p>
            </div>

            <!-- 成功消息 -->
            <?php if (isset($message)): ?>
                <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- 主要内容区域 -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- 侧边导航 -->
                <div class="lg:col-span-1">
                    <div class="card p-4 sticky top-8">
                        <nav class="space-y-1">
                            <button class="settings-nav-button w-full text-left py-2 px-4 rounded-lg transition-colors duration-200 settings-nav-active" data-settings-tab="system">
                                <i class="fas fa-cog mr-2"></i> 系统设置
                            </button>
                            <button class="settings-nav-button w-full text-left py-2 px-4 rounded-lg transition-colors duration-200" data-settings-tab="home-layout">
                                <i class="fas fa-th-large mr-2"></i> 首页排版
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- 设置内容区域 -->
                <div class="lg:col-span-3">
                    <!-- 系统设置内容 -->
                    <div id="system-settings-tab" class="settings-tab-content">
                        <!-- 系统设置标签页导航 -->
                        <div class="card p-4 mb-6">
                            <div class="flex flex-wrap border-b border-gray-200">
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200 system-sub-tab-active" data-system-sub-tab="general">
                                    基本设置
                                </button>
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200" data-system-sub-tab="theme">
                                    主题样式
                                </button>
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200" data-system-sub-tab="display">
                                    显示设置
                                </button>
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200" data-system-sub-tab="reading">
                                    阅读设置
                                </button>
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200" data-system-sub-tab="comments">
                                    讨论设置
                                </button>
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200" data-system-sub-tab="danmaku">
                                    弹幕设置
                                </button>
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200" data-system-sub-tab="footer">
                                    页脚设置
                                </button>
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200" data-system-sub-tab="editor">
                                    编辑器设置
                                </button>
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200" data-system-sub-tab="images">
                                    图片设置
                                </button>
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200" data-system-sub-tab="email">
                                    邮箱设置
                                </button>
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200" data-system-sub-tab="custom-css">
                                    CSS自定义
                                </button>
                                <button class="system-sub-tab-button py-2 px-4 font-medium text-md focus:outline-none transition-colors duration-200" data-system-sub-tab="rewrite">
                                    伪静态设置
                                </button>
                            </div>
                        </div>

                        <!-- 基本设置 -->
                        <div id="general-system-sub-tab" class="system-sub-tab-content mb-8">
                            <div class="card p-6">
                                <h3 class="text-lg font-bold mb-6">基本设置</h3>
                                
                                <form class="settings-form space-y-8" method="POST">
                                    <!-- 基本设置 -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div>
                                            <h4 class="text-md font-medium mb-4">网站基本信息</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">网站名称</label>
                                                    <input type="text" id="site_name" name="site_name" value="<?php echo $settings['site_name'] ?? '樱花梦境'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                </div>
                                                
                                                <div>
                                                    <label for="site_url" class="block text-sm font-medium text-gray-700 mb-1">网站URL</label>
                                                    <input type="text" id="site_url" name="site_url" value="<?php echo $settings['site_url'] ?? ''; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="https://example.com">
                                                </div>
                                                
                                                <div>
                                                    <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-1">管理员邮箱</label>
                                                    <input type="email" id="admin_email" name="admin_email" value="<?php echo $settings['admin_email'] ?? ''; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="admin@example.com">
                                                </div>
                                                
                                                <div>
                                                    <label for="site_description" class="block text-sm font-medium text-gray-700 mb-1">网站描述</label>
                                                    <textarea id="site_description" name="site_description" rows="3" 
                                                              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"><?php echo $settings['site_description'] ?? '一个关于动漫的博客网站'; ?></textarea>
                                                </div>
                                                
                                                <div>
                                                    <label for="site_keywords" class="block text-sm font-medium text-gray-700 mb-1">网站关键词</label>
                                                    <input type="text" id="site_keywords" name="site_keywords" value="<?php echo $settings['site_keywords'] ?? '动漫,博客,二次元'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="关键词1,关键词2,关键词3">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <!-- 保存按钮 -->
                                    <div class="mt-8 flex justify-end">
                                        <button type="submit" class="btn-primary px-6 py-3 rounded-lg text-white font-medium hover:bg-pink-600 transition-colors">
                                            <i class="fas fa-save mr-2"></i>保存设置
                                        </button>
                                    </div>
                                    
                                </form>
                            </div>
                        </div>

                        <!-- 主题样式设置 -->
                        <div id="theme-system-sub-tab" class="system-sub-tab-content mb-8 hidden">
                            <div class="card p-6">
                                <h3 class="text-lg font-bold mb-6">主题样式</h3>
                                
                                <form class="settings-form space-y-8" method="POST" enctype="multipart/form-data">
                                    <!-- 主题颜色设置 -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div>
                                            <h4 class="text-md font-medium mb-4">主题设置</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="theme_color" class="block text-sm font-medium text-gray-700 mb-1">主题颜色</label>
                                                    <select id="theme_color" name="theme_color" 
                                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                        <option value="pink" <?php echo ($settings['theme_color'] ?? 'pink') === 'pink' ? 'selected' : ''; ?>>粉色系</option>
                                                        <option value="purple" <?php echo ($settings['theme_color'] ?? 'pink') === 'purple' ? 'selected' : ''; ?>>紫色系</option>
                                                        <option value="blue" <?php echo ($settings['theme_color'] ?? 'pink') === 'blue' ? 'selected' : ''; ?>>蓝色系</option>
                                                        <option value="green" <?php echo ($settings['theme_color'] ?? 'pink') === 'green' ? 'selected' : ''; ?>>绿色系</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- 背景设置 -->
                                        <div>
                                            <h4 class="text-md font-medium mb-4">背景设置</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="custom_bg_image" class="block text-sm font-medium text-gray-700 mb-1">自定义背景图片</label>
                                                    <input type="file" id="custom_bg_image" name="custom_bg_image" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" 
                                                           accept="image/*">
                                                    <p class="text-xs text-gray-500 mt-1">支持 JPG、PNG、WEBP 格式，建议尺寸：1920x1080</p>
                                                </div>
                                                
                                                <?php if (!empty($settings['custom_bg_image'])): ?>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">当前背景图片</label>
                                                    <div class="relative w-full h-32 rounded-lg overflow-hidden border border-gray-200">
                                                        <img src="<?php echo htmlspecialchars($settings['custom_bg_image']); ?>" alt="当前背景图片" 
                                                             class="w-full h-full object-cover">
                                                        <button type="submit" name="remove_bg_image" value="1" 
                                                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <div>
                                                    <label for="bg_blend_mode" class="block text-sm font-medium text-gray-700 mb-1">背景混合模式</label>
                                                    <select id="bg_blend_mode" name="bg_blend_mode" 
                                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                        <option value="normal" <?php echo ($settings['bg_blend_mode'] ?? 'normal') === 'normal' ? 'selected' : ''; ?>>正常</option>
                                                        <option value="overlay" <?php echo ($settings['bg_blend_mode'] ?? 'normal') === 'overlay' ? 'selected' : ''; ?>>叠加</option>
                                                        <option value="multiply" <?php echo ($settings['bg_blend_mode'] ?? 'normal') === 'multiply' ? 'selected' : ''; ?>>正片叠底</option>
                                                        <option value="screen" <?php echo ($settings['bg_blend_mode'] ?? 'normal') === 'screen' ? 'selected' : ''; ?>>滤色</option>
                                                        <option value="darken" <?php echo ($settings['bg_blend_mode'] ?? 'normal') === 'darken' ? 'selected' : ''; ?>>变暗</option>
                                                        <option value="lighten" <?php echo ($settings['bg_blend_mode'] ?? 'normal') === 'lighten' ? 'selected' : ''; ?>>变亮</option>
                                                    </select>
                                                </div>
                                                
                                                <div>
                                                    <label for="bg_opacity" class="block text-sm font-medium text-gray-700 mb-1">背景透明度</label>
                                                    <input type="range" id="bg_opacity" name="bg_opacity" min="0" max="100" step="5" 
                                                           value="<?php echo $settings['bg_opacity'] ?? 80; ?>" 
                                                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                                        <span>0%</span>
                                                        <span><?php echo $settings['bg_opacity'] ?? 80; ?>%</span>
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 保存按钮 -->
                                    <div class="mt-8 flex justify-end">
                                        <button type="submit" class="btn-primary px-6 py-3 rounded-lg text-white font-medium hover:bg-pink-600 transition-colors">
                                            <i class="fas fa-save mr-2"></i>保存设置
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- 显示设置 -->
                        <div id="display-system-sub-tab" class="system-sub-tab-content mb-8 hidden">
                            <div class="card p-6">
                                <h3 class="text-lg font-bold mb-6">显示设置</h3>
                                
                                <form class="settings-form space-y-8" method="POST">
                                    <!-- 导航栏设置 -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div>
                                            <h4 class="text-md font-medium mb-4">导航栏设置</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-1">网站Logo</label>
                                                    <input type="text" id="site_logo" name="site_logo" value="<?php echo $settings['site_logo'] ?? ''; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="/path/to/logo.png">
                                                    <p class="text-xs text-gray-500 mt-1">输入网站Logo的URL路径，建议使用128x128像素的PNG或ICO格式。</p>
                                                </div>
                                                
                                                <div>
                                                    <h5 class="text-sm font-medium text-gray-700 mb-2">导航链接说明</h5>
                                                    <p class="text-sm text-gray-600">导航链接可以通过修改 <code>header.php</code> 文件进行自定义。当前导航链接：</p>
                                                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-2 mt-2">
                                                        <li>首页 - <code>/index.php</code></li>
                                                        <li>文章 - <code>/articles.php</code></li>
                                                        <li>画廊 - <code>/gallery.php</code></li>
                                                        <li>角色生成器 - <code>/character.php</code></li>
                                                        <li>投稿 - <code>/submit.php</code></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- 侧边栏设置 -->
                                        <div>
                                            <h4 class="text-md font-medium mb-4">侧边栏设置</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="show_sidebar" class="block text-sm font-medium text-gray-700 mb-1">显示侧边栏</label>
                                                    <div class="flex items-center gap-3">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_sidebar" value="1" <?php echo ($settings['show_sidebar'] ?? 1) === 1 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">显示</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_sidebar" value="0" <?php echo ($settings['show_sidebar'] ?? 1) === 0 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">隐藏</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 卡片显示控制 -->
                                    <div class="grid grid-cols-1 gap-8">
                                        <div>
                                            <h4 class="text-md font-medium mb-4">卡片显示控制</h4>
                                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                                <div>
                                                    <label for="show_user_card" class="block text-sm font-medium text-gray-700 mb-1">显示用户中心卡片</label>
                                                    <div class="flex items-center gap-3">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_user_card" value="1" <?php echo ($settings['show_user_card'] ?? 1) === 1 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">显示</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_user_card" value="0" <?php echo ($settings['show_user_card'] ?? 1) === 0 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">隐藏</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label for="show_categories_card" class="block text-sm font-medium text-gray-700 mb-1">显示分类卡片</label>
                                                    <div class="flex items-center gap-3">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_categories_card" value="1" <?php echo ($settings['show_categories_card'] ?? 1) === 1 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">显示</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_categories_card" value="0" <?php echo ($settings['show_categories_card'] ?? 1) === 0 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">隐藏</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label for="show_articles_card" class="block text-sm font-medium text-gray-700 mb-1">显示文章卡片</label>
                                                    <div class="flex items-center gap-3">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_articles_card" value="1" <?php echo ($settings['show_articles_card'] ?? 1) === 1 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">显示</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_articles_card" value="0" <?php echo ($settings['show_articles_card'] ?? 1) === 0 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">隐藏</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label for="show_gallery_card" class="block text-sm font-medium text-gray-700 mb-1">显示画廊卡片</label>
                                                    <div class="flex items-center gap-3">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_gallery_card" value="1" <?php echo ($settings['show_gallery_card'] ?? 1) === 1 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">显示</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_gallery_card" value="0" <?php echo ($settings['show_gallery_card'] ?? 1) === 0 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">隐藏</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label for="show_character_card" class="block text-sm font-medium text-gray-700 mb-1">显示角色生成器卡片</label>
                                                    <div class="flex items-center gap-3">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_character_card" value="1" <?php echo ($settings['show_character_card'] ?? 1) === 1 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">显示</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_character_card" value="0" <?php echo ($settings['show_character_card'] ?? 1) === 0 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">隐藏</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label for="show_author_card" class="block text-sm font-medium text-gray-700 mb-1">显示作者卡片</label>
                                                    <div class="flex items-center gap-3">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_author_card" value="1" <?php echo ($settings['show_author_card'] ?? 1) === 1 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">显示</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_author_card" value="0" <?php echo ($settings['show_author_card'] ?? 1) === 0 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">隐藏</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 卡片样式和内容设置 -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div>
                                            <h4 class="text-md font-medium mb-4">卡片内容设置</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="articles_per_page_home" class="block text-sm font-medium text-gray-700 mb-1">首页文章卡片数量</label>
                                                    <input type="number" id="articles_per_page_home" name="articles_per_page_home" value="<?php echo $settings['articles_per_page_home'] ?? 3; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" min="1" max="12">
                                                </div>
                                                
                                                <div>
                                                    <label for="gallery_images_per_page" class="block text-sm font-medium text-gray-700 mb-1">首页画廊图片数量</label>
                                                    <input type="number" id="gallery_images_per_page" name="gallery_images_per_page" value="<?php echo $settings['gallery_images_per_page'] ?? 4; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" min="1" max="8">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-md font-medium mb-4">卡片样式设置</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="card_border_radius" class="block text-sm font-medium text-gray-700 mb-1">卡片圆角大小</label>
                                                    <select id="card_border_radius" name="card_border_radius" 
                                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                        <option value="sm" <?php echo ($settings['card_border_radius'] ?? '2xl') === 'sm' ? 'selected' : ''; ?>>小 (sm)</option>
                                                        <option value="md" <?php echo ($settings['card_border_radius'] ?? '2xl') === 'md' ? 'selected' : ''; ?>>中 (md)</option>
                                                        <option value="lg" <?php echo ($settings['card_border_radius'] ?? '2xl') === 'lg' ? 'selected' : ''; ?>>大 (lg)</option>
                                                        <option value="xl" <?php echo ($settings['card_border_radius'] ?? '2xl') === 'xl' ? 'selected' : ''; ?>>超大 (xl)</option>
                                                        <option value="2xl" <?php echo ($settings['card_border_radius'] ?? '2xl') === '2xl' ? 'selected' : ''; ?>>特大 (2xl)</option>
                                                        <option value="3xl" <?php echo ($settings['card_border_radius'] ?? '2xl') === '3xl' ? 'selected' : ''; ?>>极大 (3xl)</option>
                                                    </select>
                                                </div>
                                                
                                                <div>
                                                    <label for="card_shadow" class="block text-sm font-medium text-gray-700 mb-1">卡片阴影大小</label>
                                                    <select id="card_shadow" name="card_shadow" 
                                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                        <option value="none" <?php echo ($settings['card_shadow'] ?? 'lg') === 'none' ? 'selected' : ''; ?>>无阴影</option>
                                                        <option value="sm" <?php echo ($settings['card_shadow'] ?? 'lg') === 'sm' ? 'selected' : ''; ?>>小阴影 (sm)</option>
                                                        <option value="md" <?php echo ($settings['card_shadow'] ?? 'lg') === 'md' ? 'selected' : ''; ?>>中阴影 (md)</option>
                                                        <option value="lg" <?php echo ($settings['card_shadow'] ?? 'lg') === 'lg' ? 'selected' : ''; ?>>大阴影 (lg)</option>
                                                        <option value="xl" <?php echo ($settings['card_shadow'] ?? 'lg') === 'xl' ? 'selected' : ''; ?>>超大阴影 (xl)</option>
                                                        <option value="2xl" <?php echo ($settings['card_shadow'] ?? 'lg') === '2xl' ? 'selected' : ''; ?>>特大阴影 (2xl)</option>
                                                    </select>
                                                </div>
                                                
                                                <div>
                                                    <label for="card_hover_effect" class="block text-sm font-medium text-gray-700 mb-1">启用卡片悬停效果</label>
                                                    <div class="flex items-center gap-3">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="card_hover_effect" value="1" <?php echo ($settings['card_hover_effect'] ?? 1) === 1 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">启用</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="card_hover_effect" value="0" <?php echo ($settings['card_hover_effect'] ?? 1) === 0 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">禁用</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 保存按钮 -->
                                    <div class="mt-8 flex justify-end">
                                        <button type="submit" class="btn-primary px-6 py-3 rounded-lg text-white font-medium hover:bg-pink-600 transition-colors">
                                            <i class="fas fa-save mr-2"></i>保存设置
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- 阅读设置 -->
                        <div id="reading-system-sub-tab" class="system-sub-tab-content mb-8 hidden">
                            <div class="card p-6">
                                <h3 class="text-lg font-bold mb-6">阅读设置</h3>
                                
                                <form class="settings-form space-y-8" method="POST">
                                    <!-- 阅读设置 -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div>
                                            <h4 class="text-md font-medium mb-4">阅读设置</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="posts_per_page" class="block text-sm font-medium text-gray-700 mb-1">每页文章数量</label>
                                                    <input type="number" id="posts_per_page" name="posts_per_page" value="<?php echo $settings['posts_per_page'] ?? 10; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" min="1" max="100">
                                                </div>
                                                
                                                <div>
                                                    <label for="date_format" class="block text-sm font-medium text-gray-700 mb-1">日期格式</label>
                                                    <input type="text" id="date_format" name="date_format" value="<?php echo $settings['date_format'] ?? 'Y-m-d'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="Y-m-d">
                                                    <p class="text-xs text-gray-500 mt-1">示例: Y-m-d (2023-12-31), m/d/Y (12/31/2023)</p>
                                                </div>
                                                
                                                <div>
                                                    <label for="time_format" class="block text-sm font-medium text-gray-700 mb-1">时间格式</label>
                                                    <input type="text" id="time_format" name="time_format" value="<?php echo $settings['time_format'] ?? 'H:i:s'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="H:i:s">
                                                    <p class="text-xs text-gray-500 mt-1">示例: H:i:s (23:59:59), h:i A (11:59 PM)</p>
                                                </div>
                                                
                                                <div>
                                                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">时区</label>
                                                    <input type="text" id="timezone" name="timezone" value="<?php echo $settings['timezone'] ?? 'Asia/Shanghai'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="Asia/Shanghai">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 保存按钮 -->
                                    <div class="mt-8 flex justify-end">
                                        <button type="submit" class="btn-primary px-6 py-3 rounded-lg text-white font-medium hover:bg-pink-600 transition-colors">
                                            <i class="fas fa-save mr-2"></i>保存设置
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- 讨论设置 -->
                        <div id="comments-system-sub-tab" class="system-sub-tab-content mb-8 hidden">
                            <div class="card p-6">
                                <h3 class="text-lg font-bold mb-6">讨论设置</h3>
                                
                                <form class="settings-form space-y-8" method="POST">
                                    <!-- 讨论设置 -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div>
                                            <h4 class="text-md font-medium mb-4">评论设置</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="show_comments" class="block text-sm font-medium text-gray-700 mb-1">允许评论</label>
                                                    <div class="flex items-center gap-3">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_comments" value="1" <?php echo ($settings['show_comments'] ?? 1) === 1 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">允许</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="show_comments" value="0" <?php echo ($settings['show_comments'] ?? 1) === 0 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">禁止</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label for="comments_per_page" class="block text-sm font-medium text-gray-700 mb-1">每页评论数量</label>
                                                    <input type="number" id="comments_per_page" name="comments_per_page" value="<?php echo $settings['comments_per_page'] ?? 50; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" min="1" max="200">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 保存按钮 -->
                                    <div class="mt-8 flex justify-end">
                                        <button type="submit" class="btn-primary px-6 py-3 rounded-lg text-white font-medium hover:bg-pink-600 transition-colors">
                                            <i class="fas fa-save mr-2"></i>保存设置
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- 弹幕设置 -->
                        <div id="danmaku-system-sub-tab" class="system-sub-tab-content mb-8 hidden">
                            <div class="card p-6">
                                <h3 class="text-lg font-bold mb-6">弹幕设置</h3>
                                
                                <form class="settings-form space-y-8" method="POST">
                                    <!-- 弹幕设置 -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div>
                                            <h4 class="text-md font-medium mb-4">弹幕设置</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="enable_danmaku" class="block text-sm font-medium text-gray-700 mb-1">启用弹幕功能</label>
                                                    <div class="flex items-center gap-3">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="enable_danmaku" value="1" <?php echo ($settings['enable_danmaku'] ?? 1) === 1 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">启用</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="enable_danmaku" value="0" <?php echo ($settings['enable_danmaku'] ?? 1) === 0 ? 'checked' : ''; ?> 
                                                                   class="form-radio text-pink-500">
                                                            <span class="ml-2">禁用</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label for="danmaku_duration_min" class="block text-sm font-medium text-gray-700 mb-1">弹幕持续时间（最小值，秒）</label>
                                                    <input type="number" id="danmaku_duration_min" name="danmaku_duration_min" value="<?php echo $settings['danmaku_duration_min'] ?? 10; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" min="1" max="60">
                                                </div>
                                                
                                                <div>
                                                    <label for="danmaku_duration_max" class="block text-sm font-medium text-gray-700 mb-1">弹幕持续时间（最大值，秒）</label>
                                                    <input type="number" id="danmaku_duration_max" name="danmaku_duration_max" value="<?php echo $settings['danmaku_duration_max'] ?? 20; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" min="1" max="60">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-md font-medium mb-4">默认弹幕设置</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="default_danmaku_color" class="block text-sm font-medium text-gray-700 mb-1">默认弹幕颜色</label>
                                                    <select id="default_danmaku_color" name="default_danmaku_color" 
                                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                        <option value="#ffffff" <?php echo ($settings['default_danmaku_color'] ?? '#ffffff') === '#ffffff' ? 'selected' : ''; ?>>白色</option>
                                                        <option value="#ff6b81" <?php echo ($settings['default_danmaku_color'] ?? '#ffffff') === '#ff6b81' ? 'selected' : ''; ?>>粉色</option>
                                                        <option value="#ff6348" <?php echo ($settings['default_danmaku_color'] ?? '#ffffff') === '#ff6348' ? 'selected' : ''; ?>>红色</option>
                                                        <option value="#32cd32" <?php echo ($settings['default_danmaku_color'] ?? '#ffffff') === '#32cd32' ? 'selected' : ''; ?>>绿色</option>
                                                        <option value="#1e90ff" <?php echo ($settings['default_danmaku_color'] ?? '#ffffff') === '#1e90ff' ? 'selected' : ''; ?>>蓝色</option>
                                                        <option value="#ffd700" <?php echo ($settings['default_danmaku_color'] ?? '#ffffff') === '#ffd700' ? 'selected' : ''; ?>>金色</option>
                                                        <option value="#9370db" <?php echo ($settings['default_danmaku_color'] ?? '#ffffff') === '#9370db' ? 'selected' : ''; ?>>紫色</option>
                                                        <option value="#00ffff" <?php echo ($settings['default_danmaku_color'] ?? '#ffffff') === '#00ffff' ? 'selected' : ''; ?>>青色</option>
                                                    </select>
                                                </div>
                                                
                                                <div>
                                                    <label for="default_danmaku_size" class="block text-sm font-medium text-gray-700 mb-1">默认弹幕大小</label>
                                                    <select id="default_danmaku_size" name="default_danmaku_size" 
                                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                        <option value="20" <?php echo ($settings['default_danmaku_size'] ?? 25) === 20 ? 'selected' : ''; ?>>小 (20px)</option>
                                                        <option value="25" <?php echo ($settings['default_danmaku_size'] ?? 25) === 25 ? 'selected' : ''; ?>>中 (25px)</option>
                                                        <option value="30" <?php echo ($settings['default_danmaku_size'] ?? 25) === 30 ? 'selected' : ''; ?>>大 (30px)</option>
                                                        <option value="35" <?php echo ($settings['default_danmaku_size'] ?? 25) === 35 ? 'selected' : ''; ?>>超大 (35px)</option>
                                                    </select>
                                                </div>
                                                
                                                <div>
                                                    <label for="default_danmaku_mode" class="block text-sm font-medium text-gray-700 mb-1">默认弹幕模式</label>
                                                    <select id="default_danmaku_mode" name="default_danmaku_mode" 
                                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                        <option value="scroll" <?php echo ($settings['default_danmaku_mode'] ?? 'scroll') === 'scroll' ? 'selected' : ''; ?>>滚动</option>
                                                        <option value="top" <?php echo ($settings['default_danmaku_mode'] ?? 'scroll') === 'top' ? 'selected' : ''; ?>>顶部</option>
                                                        <option value="bottom" <?php echo ($settings['default_danmaku_mode'] ?? 'scroll') === 'bottom' ? 'selected' : ''; ?>>底部</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 保存按钮 -->
                                    <div class="mt-8 flex justify-end">
                                        <button type="submit" class="btn-primary px-6 py-3 rounded-lg text-white font-medium hover:bg-pink-600 transition-colors">
                                            <i class="fas fa-save mr-2"></i>保存设置
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- 页脚设置 -->
                        <div id="footer-system-sub-tab" class="system-sub-tab-content mb-8 hidden">
                            <div class="card p-6">
                                <h3 class="text-lg font-bold mb-6">页脚设置</h3>
                                
                                <form class="settings-form space-y-8" method="POST">
                                    <!-- 关于我们设置 -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div>
                                            <h4 class="text-md font-medium mb-4">关于我们</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="footer_about_title" class="block text-sm font-medium text-gray-700 mb-1">标题</label>
                                                    <input type="text" id="footer_about_title" name="footer_about_title" 
                                                           value="<?php echo $settings['footer_about_title'] ?? '关于樱花梦境'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                </div>
                                                
                                                <div>
                                                    <label for="footer_about_description" class="block text-sm font-medium text-gray-700 mb-1">描述</label>
                                                    <textarea id="footer_about_description" name="footer_about_description" rows="4" 
                                                              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"><?php echo $settings['footer_about_description'] ?? '樱花梦境是一个专注于二次元文化的博客网站，提供文章分享、图片画廊和角色生成器等功能。在这里，你可以发现更多精彩的二次元内容，分享你的创作与感悟。'; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-md font-medium mb-4">社交媒体链接</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="footer_twitter_url" class="block text-sm font-medium text-gray-700 mb-1">Twitter</label>
                                                    <input type="url" id="footer_twitter_url" name="footer_twitter_url" 
                                                           value="<?php echo $settings['footer_twitter_url'] ?? ''; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="https://twitter.com/">
                                                </div>
                                                
                                                <div>
                                                    <label for="footer_instagram_url" class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                                                    <input type="url" id="footer_instagram_url" name="footer_instagram_url" 
                                                           value="<?php echo $settings['footer_instagram_url'] ?? ''; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="https://instagram.com/">
                                                </div>
                                                
                                                <div>
                                                    <label for="footer_github_url" class="block text-sm font-medium text-gray-700 mb-1">GitHub</label>
                                                    <input type="url" id="footer_github_url" name="footer_github_url" 
                                                           value="<?php echo $settings['footer_github_url'] ?? ''; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="https://github.com/">
                                                </div>
                                                
                                                <div>
                                                    <label for="footer_bilibili_url" class="block text-sm font-medium text-gray-700 mb-1">Bilibili</label>
                                                    <input type="url" id="footer_bilibili_url" name="footer_bilibili_url" 
                                                           value="<?php echo $settings['footer_bilibili_url'] ?? ''; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="https://bilibili.com/">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 联系信息设置 -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div>
                                            <h4 class="text-md font-medium mb-4">联系信息</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="footer_email" class="block text-sm font-medium text-gray-700 mb-1">邮箱</label>
                                                    <input type="email" id="footer_email" name="footer_email" 
                                                           value="<?php echo $settings['footer_email'] ?? 'contact@sakuradream.com'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                </div>
                                                
                                                <div>
                                                    <label for="footer_address" class="block text-sm font-medium text-gray-700 mb-1">地址</label>
                                                    <input type="text" id="footer_address" name="footer_address" 
                                                           value="<?php echo $settings['footer_address'] ?? '二次元世界，樱花街道123号'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                </div>
                                                
                                                <div>
                                                    <label for="footer_business_hours" class="block text-sm font-medium text-gray-700 mb-1">营业时间</label>
                                                    <input type="text" id="footer_business_hours" name="footer_business_hours" 
                                                           value="<?php echo $settings['footer_business_hours'] ?? '周一至周日 9:00 - 22:00'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-md font-medium mb-4">版权信息</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="footer_copyright_text" class="block text-sm font-medium text-gray-700 mb-1">版权文本</label>
                                                    <input type="text" id="footer_copyright_text" name="footer_copyright_text" 
                                                           value="<?php echo $settings['footer_copyright_text'] ?? '樱花梦境. 保留所有权利.'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                </div>
                                            </div>
                                            
                                            <h4 class="text-md font-medium mb-4 mt-6">页脚链接</h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="footer_usage_guide_url" class="block text-sm font-medium text-gray-700 mb-1">使用说明链接</label>
                                                    <input type="url" id="footer_usage_guide_url" name="footer_usage_guide_url" 
                                                           value="<?php echo $settings['footer_usage_guide_url'] ?? '/使用说明.md'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                </div>
                                                
                                                <div>
                                                    <label for="footer_privacy_policy_url" class="block text-sm font-medium text-gray-700 mb-1">隐私政策链接</label>
                                                    <input type="url" id="footer_privacy_policy_url" name="footer_privacy_policy_url" 
                                                           value="<?php echo $settings['footer_privacy_policy_url'] ?? '/privacy'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                </div>
                                                
                                                <div>
                                                    <label for="footer_terms_of_service_url" class="block text-sm font-medium text-gray-700 mb-1">使用条款链接</label>
                                                    <input type="url" id="footer_terms_of_service_url" name="footer_terms_of_service_url" 
                                                           value="<?php echo $settings['footer_terms_of_service_url'] ?? '/terms'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                </div>
                                                
                                                <div>
                                                    <label for="footer_sitemap_url" class="block text-sm font-medium text-gray-700 mb-1">网站地图链接</label>
                                                    <input type="url" id="footer_sitemap_url" name="footer_sitemap_url" 
                                                           value="<?php echo $settings['footer_sitemap_url'] ?? '/sitemap'; ?>" 
                                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 保存按钮 -->
                                    <div class="mt-8 flex justify-end">
                                        <button type="submit" class="btn-primary px-6 py-3 rounded-lg text-white font-medium hover:bg-pink-600 transition-colors">
                                            <i class="fas fa-save mr-2"></i>保存设置
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>