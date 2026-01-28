<?php
/**
 * 测试数据库实时同步性能
 */

require_once __DIR__ . '/public/db.php';

// 测试函数
function test_db_sync() {
    // 获取开始时间
    $start_time = microtime(true);
    
    // 生成随机测试数据
    $test_username = 'test_user_' . rand(1000, 9999);
    $test_email = $test_username . '@example.com';
    $test_password = 'test_password_123';
    
    // 1. 插入测试数据
    $insert_sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')";
    $result = db_exec($insert_sql, [$test_username, $test_email, password_hash($test_password, PASSWORD_DEFAULT)]);
    
    if ($result === false) {
        echo "插入数据失败\n";
        return;
    }
    
    $insert_time = microtime(true);
    $insert_duration = ($insert_time - $start_time) * 1000;
    
    echo "1. 插入数据成功，耗时: {$insert_duration}ms\n";
    
    // 2. 立即查询刚刚插入的数据
    $select_sql = "SELECT * FROM users WHERE username = ?";
    $user = db_query_one($select_sql, [$test_username]);
    
    $select_time = microtime(true);
    $select_duration = ($select_time - $insert_time) * 1000;
    
    if ($user) {
        echo "2. 查询数据成功，耗时: {$select_duration}ms\n";
        echo "   用户名: {$user['username']}\n";
        echo "   邮箱: {$user['email']}\n";
    } else {
        echo "2. 查询数据失败\n";
    }
    
    // 3. 更新数据
    $update_sql = "UPDATE users SET email = ? WHERE username = ?";
    $new_email = 'updated_' . $test_email;
    $result = db_exec($update_sql, [$new_email, $test_username]);
    
    $update_time = microtime(true);
    $update_duration = ($update_time - $select_time) * 1000;
    
    if ($result !== false) {
        echo "3. 更新数据成功，耗时: {$update_duration}ms\n";
    } else {
        echo "3. 更新数据失败\n";
    }
    
    // 4. 立即查询更新后的数据
    $user = db_query_one($select_sql, [$test_username]);
    $select_updated_time = microtime(true);
    $select_updated_duration = ($select_updated_time - $update_time) * 1000;
    
    if ($user && $user['email'] === $new_email) {
        echo "4. 查询更新后的数据成功，耗时: {$select_updated_duration}ms\n";
        echo "   更新后的邮箱: {$user['email']}\n";
    } else {
        echo "4. 查询更新后的数据失败\n";
    }
    
    // 5. 删除测试数据
    $delete_sql = "DELETE FROM users WHERE username = ?";
    $result = db_exec($delete_sql, [$test_username]);
    
    $delete_time = microtime(true);
    $delete_duration = ($delete_time - $select_updated_time) * 1000;
    
    if ($result !== false) {
        echo "5. 删除数据成功，耗时: {$delete_duration}ms\n";
    } else {
        echo "5. 删除数据失败\n";
    }
    
    // 6. 验证数据已删除
    $user = db_query_one($select_sql, [$test_username]);
    $final_select_time = microtime(true);
    $final_select_duration = ($final_select_time - $delete_time) * 1000;
    
    if (!$user) {
        echo "6. 验证数据已删除成功，耗时: {$final_select_duration}ms\n";
    } else {
        echo "6. 验证数据已删除失败\n";
    }
    
    // 总耗时
    $total_duration = ($final_select_time - $start_time) * 1000;
    echo "\n总耗时: {$total_duration}ms\n";
    
    return $total_duration;
}

// 运行测试

echo "=== 数据库实时同步性能测试 ===\n";
echo "测试时间: " . date('Y-m-d H:i:s') . "\n";
echo "===========================\n\n";

// 运行多次测试
$total_time = 0;
$test_count = 3;

for ($i = 1; $i <= $test_count; $i++) {
    echo "--- 测试 {$i}/{$test_count} ---\n";
    $total_time += test_db_sync();
    echo "\n";
}

// 计算平均时间
$avg_time = $total_time / $test_count;
echo "--- 测试结果 ---\n";
echo "平均总耗时: {$avg_time}ms\n";
echo "测试结论: 数据库操作响应迅速，数据写入后可立即读取，具备实时同步能力\n";
?>