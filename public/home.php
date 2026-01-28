<?php
/**
 * 首页模板
 */

// 加载数据库连接和函数
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 加载卡片模板
require_once __DIR__ . '/card-templates.php';

// 获取当前登录用户信息
$current_user = null;
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $current_user = db_query_one($sql, [$_SESSION['user_id']]);
}

// 获取网站设置
$settings = get_settings();

// 获取卡片样式设置
$card_border_radius = $settings['card_border_radius'] ?? '2xl';
$card_shadow = $settings['card_shadow'] ?? 'lg';
$card_hover_effect = $settings['card_hover_effect'] ?? 1;

// 获取所有首页区域
$home_sections = get_home_sections();

// 获取每个区域的卡片
$section_cards = [];
foreach ($home_sections as $section) {
    $section_cards[$section['id']] = get_section_cards($section['id']);
}

// debug信息（临时添加，用于排查问题）
// echo '<pre>';
// echo '区块数量: ' . count($home_sections) . '<br>';
// foreach ($home_sections as $section) {
//     echo '区块: ' . $section['name'] . ' - ' . $section['display_name'] . '<br>';
//     echo '卡片数量: ' . count($section_cards[$section['id']]) . '<br>';
// }
// echo '</pre>';
?>
    <!-- 动态渲染首页区域 -->
    <?php
    // 分离不同类型的区域，确保布局正确
    $hero_section = null;
    $main_cards = [];
    $sidebar_cards = [];
    $other_sections = [];
    
    // 遍历所有区域，分类整理
    foreach ($home_sections as $section) {
        if (isset($section_cards[$section['id']]) && !empty($section_cards[$section['id']])) {
            if ($section['name'] === 'hero') {
                $hero_section = $section;
            } elseif ($section['name'] === 'main') {
                $main_cards = $section_cards[$section['id']];
            } elseif ($section['name'] === 'sidebar') {
                $sidebar_cards = $section_cards[$section['id']];
            } else {
                $other_sections[] = [
                    'section' => $section,
                    'cards' => $section_cards[$section['id']]
                ];
            }
        }
    }
    
    // 1. 渲染英雄区
    if ($hero_section) {
        foreach ($section_cards[$hero_section['id']] as $card) {
            echo render_card($card);
        }
    }
    
    // 2. 渲染主内容区和侧边栏（使用正确的嵌套结构）
    if (!empty($main_cards) || !empty($sidebar_cards)) {
        echo '<div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-20">';
        
        // 主内容区
        if (!empty($main_cards)) {
            echo '<div class="lg:col-span-3">';
            foreach ($main_cards as $card) {
                echo render_card($card);
            }
            echo '</div>';
        }
        
        // 侧边栏
        if (!empty($sidebar_cards)) {
            echo '<div class="lg:col-span-1 space-y-8">';
            foreach ($sidebar_cards as $card) {
                echo render_card($card);
            }
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    // 3. 渲染其他区域
    foreach ($other_sections as $other_section) {
        echo '<section class="mb-16">';
        foreach ($other_section['cards'] as $card) {
            echo render_card($card);
        }
        echo '</section>';
    }
    ?>