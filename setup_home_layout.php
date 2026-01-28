<?php
/**
 * 首页排版设置脚本
 * 用于配置二次元个人博客的首页布局
 */

// 包含必要的文件
require_once 'public/functions.php';

// 1. 调整区域顺序
$sections = [
    ['id' => 1, 'sort_order' => 1],  // 英雄区在最前面
    ['id' => 2, 'sort_order' => 2],  // 主内容区
    ['id' => 3, 'sort_order' => 3],  // 侧边栏
    ['id' => 4, 'sort_order' => 4]   // 底部区
];

foreach ($sections as $section) {
    update_home_section($section['id'], [
        'sort_order' => $section['sort_order']
    ]);
    echo "Updated section {$section['id']} to sort_order {$section['sort_order']}\n";
}

// 2. 添加英雄区卡片
$hero_card = [
    'section_id' => 1,
    'card_type' => 'announcements',
    'title' => '欢迎来到樱花梦境',
    'content' => '✨ 一个充满二次元魅力的个人博客 ✨',
    'settings' => [
        'style' => 'style3',
        'layout' => 'auto',
        'responsive' => 'auto',
        'width' => 100,
        'margin' => 8,
        'padding' => 20
    ],
    'enabled' => 1,
    'sort_order' => 1
];

if (add_home_card($hero_card)) {
    echo "Added hero card successfully\n";
}

// 3. 添加主内容区文章卡片
$article_card = [
    'section_id' => 2,
    'card_type' => 'articles',
    'title' => '最新文章',
    'settings' => [
        'count' => 6,
        'style' => 'style3',
        'layout' => 'auto',
        'responsive' => 'auto',
        'image_height' => 200,
        'show_meta' => 1,
        'show_summary' => 1,
        'hover_effect' => 'scale',
        'width' => 100,
        'margin' => 8,
        'padding' => 12
    ],
    'enabled' => 1,
    'sort_order' => 1
];

if (add_home_card($article_card)) {
    echo "Added article card successfully\n";
}

// 4. 添加二次元风格画廊卡片
$gallery_card = [
    'section_id' => 2,
    'card_type' => 'gallery',
    'title' => '作品展示',
    'settings' => [
        'count' => 8,
        'style' => 'anime',
        'layout' => 'horizontal',
        'responsive' => 'auto',
        'width' => 100,
        'margin' => 8,
        'padding' => 12
    ],
    'enabled' => 1,
    'sort_order' => 2
];

if (add_home_card($gallery_card)) {
    echo "Added anime gallery card successfully\n";
}

// 5. 添加侧边栏分类卡片
$category_card = [
    'section_id' => 3,
    'card_type' => 'categories',
    'title' => '文章分类',
    'settings' => [
        'style' => 'style3',
        'width' => 100,
        'margin' => 8,
        'padding' => 12
    ],
    'enabled' => 1,
    'sort_order' => 1
];

if (add_home_card($category_card)) {
    echo "Added category card successfully\n";
}

// 6. 添加侧边栏统计卡片
$stats_card = [
    'section_id' => 3,
    'card_type' => 'stats',
    'title' => '站点统计',
    'settings' => [
        'style' => 'style3',
        'width' => 100,
        'margin' => 8,
        'padding' => 12
    ],
    'enabled' => 1,
    'sort_order' => 2
];

if (add_home_card($stats_card)) {
    echo "Added stats card successfully\n";
}

// 7. 添加侧边栏公告卡片
$announcement_card = [
    'section_id' => 3,
    'card_type' => 'announcements',
    'title' => '最新公告',
    'settings' => [
        'style' => 'style3',
        'width' => 100,
        'margin' => 8,
        'padding' => 12
    ],
    'enabled' => 1,
    'sort_order' => 3
];

if (add_home_card($announcement_card)) {
    echo "Added announcement card successfully\n";
}

// 8. 添加底部区链接卡片
$link_card = [
    'section_id' => 4,
    'card_type' => 'links',
    'title' => '快速链接',
    'settings' => [
        'style' => 'style3',
        'width' => 100,
        'margin' => 8,
        'padding' => 12
    ],
    'enabled' => 1,
    'sort_order' => 1
];

if (add_home_card($link_card)) {
    echo "Added link card successfully\n";
}

echo "\n首页排版设置完成！\n";
echo "你可以通过 http://localhost:8850 访问网站查看效果。\n";