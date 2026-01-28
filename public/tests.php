<?php
/**
 * 单元测试框架
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 测试结果数组
$test_results = [
    'passed' => 0,
    'failed' => 0,
    'tests' => []
];

/**
 * 运行测试
 */
function run_tests() {
    global $test_results;
    
    echo "开始运行测试...\n\n";
    
    // 测试数据库连接
    test_db_connection();
    
    // 测试用户相关功能
    test_user_functions();
    
    // 测试设置相关功能
    test_settings_functions();
    
    // 测试首页排版功能
    test_home_layout_functions();
    
    // 测试密码重置功能
    test_password_reset_functions();
    
    // 显示测试结果
    echo "\n";
    echo "测试结果:\n";
    echo "通过: {$test_results['passed']}\n";
    echo "失败: {$test_results['failed']}\n";
    echo "总计: " . ($test_results['passed'] + $test_results['failed']) . "\n";
    
    if ($test_results['failed'] === 0) {
        echo "\n✅ 所有测试通过！\n";
    } else {
        echo "\n❌ 有 {$test_results['failed']} 个测试失败！\n";
    }
}

/**
 * 测试数据库连接
 */
function test_db_connection() {
    global $test_results;
    
    echo "测试数据库连接...";
    
    $pdo = get_db_connection();
    if ($pdo) {
        $test_results['passed']++;
        $test_results['tests'][] = [
            'name' => '数据库连接',
            'status' => 'passed'
        ];
        echo " ✅\n";
    } else {
        $test_results['failed']++;
        $test_results['tests'][] = [
            'name' => '数据库连接',
            'status' => 'failed'
        ];
        echo " ❌\n";
    }
}

/**
 * 测试用户相关功能
 */
function test_user_functions() {
    global $test_results;
    
    echo "测试用户相关功能...\n";
    
    // 测试获取所有用户
    echo "  测试获取所有用户...";
    $users = get_all_users();
    if (is_array($users)) {
        $test_results['passed']++;
        $test_results['tests'][] = [
            'name' => '获取所有用户',
            'status' => 'passed'
        ];
        echo " ✅\n";
    } else {
        $test_results['failed']++;
        $test_results['tests'][] = [
            'name' => '获取所有用户',
            'status' => 'failed'
        ];
        echo " ❌\n";
    }
}

/**
 * 测试设置相关功能
 */
function test_settings_functions() {
    global $test_results;
    
    echo "测试设置相关功能...\n";
    
    // 测试获取单个设置
    echo "  测试获取单个设置...";
    $site_name = get_setting('site_name');
    if ($site_name) {
        $test_results['passed']++;
        $test_results['tests'][] = [
            'name' => '获取单个设置',
            'status' => 'passed'
        ];
        echo " ✅\n";
    } else {
        $test_results['failed']++;
        $test_results['tests'][] = [
            'name' => '获取单个设置',
            'status' => 'failed'
        ];
        echo " ❌\n";
    }
    
    // 测试获取所有设置
    echo "  测试获取所有设置...";
    $settings = get_settings();
    if (is_array($settings) && count($settings) > 0) {
        $test_results['passed']++;
        $test_results['tests'][] = [
            'name' => '获取所有设置',
            'status' => 'passed'
        ];
        echo " ✅\n";
    } else {
        $test_results['failed']++;
        $test_results['tests'][] = [
            'name' => '获取所有设置',
            'status' => 'failed'
        ];
        echo " ❌\n";
    }
}

/**
 * 测试首页排版功能
 */
function test_home_layout_functions() {
    global $test_results;
    
    echo "测试首页排版功能...\n";
    
    // 测试获取首页区域
    echo "  测试获取首页区域...";
    $sections = get_home_sections();
    if (is_array($sections)) {
        $test_results['passed']++;
        $test_results['tests'][] = [
            'name' => '获取首页区域',
            'status' => 'passed'
        ];
        echo " ✅\n";
    } else {
        $test_results['failed']++;
        $test_results['tests'][] = [
            'name' => '获取首页区域',
            'status' => 'failed'
        ];
        echo " ❌\n";
    }
}

/**
 * 测试密码重置功能
 */
function test_password_reset_functions() {
    global $test_results;
    
    echo "测试密码重置功能...\n";
    
    // 测试创建密码重置请求
    echo "  测试创建密码重置请求...";
    // 使用测试用户ID 1
    $result = create_password_reset(1, 'test@example.com');
    if ($result) {
        $test_results['passed']++;
        $test_results['tests'][] = [
            'name' => '创建密码重置请求',
            'status' => 'passed'
        ];
        echo " ✅\n";
    } else {
        $test_results['failed']++;
        $test_results['tests'][] = [
            'name' => '创建密码重置请求',
            'status' => 'failed'
        ];
        echo " ❌\n";
    }
}

// 运行测试
run_tests();
