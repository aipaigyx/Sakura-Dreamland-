<?php
/**
 * 首页区域和卡片数据库迁移脚本
 */

// 加载数据库连接文件
require_once __DIR__ . '/db.php';

function migrate_home_database() {
    echo "开始执行首页数据库迁移...\n";
    
    // 创建区域表
    $create_sections_table = "
    CREATE TABLE IF NOT EXISTS home_sections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        display_name VARCHAR(100) NOT NULL,
        enabled TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_name (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    $result = db_exec($create_sections_table);
    if ($result === false) {
        echo "创建home_sections表失败\n";
        return false;
    }
    echo "创建home_sections表成功\n";
    
    // 创建卡片表
    $create_cards_table = "
    CREATE TABLE IF NOT EXISTS home_cards (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section_id INT NOT NULL,
        card_type VARCHAR(50) NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        settings JSON,
        enabled TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (section_id) REFERENCES home_sections(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    $result = db_exec($create_cards_table);
    if ($result === false) {
        echo "创建home_cards表失败\n";
        return false;
    }
    echo "创建home_cards表成功\n";
    
    // 插入初始区域数据
    $sections = [
        ['name' => 'hero', 'display_name' => '英雄区', 'sort_order' => 1, 'enabled' => 1],
        ['name' => 'main', 'display_name' => '主内容区', 'sort_order' => 2, 'enabled' => 1],
        ['name' => 'sidebar', 'display_name' => '侧边栏', 'sort_order' => 3, 'enabled' => 1],
        ['name' => 'footer', 'display_name' => '底部区', 'sort_order' => 4, 'enabled' => 1]
    ];
    
    foreach ($sections as $section) {
        $sql = "INSERT IGNORE INTO home_sections (name, display_name, sort_order, enabled) VALUES (?, ?, ?, ?)";
        $result = db_exec($sql, [$section['name'], $section['display_name'], $section['sort_order'], $section['enabled']]);
        if ($result === false) {
            echo "插入区域 {$section['display_name']} 失败\n";
        } else {
            echo "插入区域 {$section['display_name']} 成功\n";
        }
    }
    
    echo "数据库迁移完成！\n";
    return true;
}

// 执行迁移
migrate_home_database();
