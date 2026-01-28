<?php
/**
 * 数据库测试脚本
 */

require_once __DIR__ . '/db.php';

// 测试数据库连接
$pdo = get_db_connection();
echo "数据库连接测试: " . ($pdo ? "成功" : "失败") . "\n";

if ($pdo) {
    // 测试comments表结构
    $sql = "DESCRIBE comments";
    try {
        $stmt = $pdo->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\nComments表结构:\n";
        print_r($result);
        
        // 测试插入一条测试评论
        echo "\n测试插入评论...\n";
        $test_sql = "INSERT INTO comments (article_id, user_id, name, email, content, status, created_at) VALUES (1, 1, '测试用户', 'test@example.com', '测试评论内容', 'pending', NOW())";
        $pdo->exec($test_sql);
        $test_id = $pdo->lastInsertId();
        echo "插入测试评论成功，ID: " . $test_id . "\n";
        
        // 测试更新评论状态
        echo "\n测试更新评论状态...\n";
        $update_sql = "UPDATE comments SET status = 'approved' WHERE id = ?";
        $stmt = $pdo->prepare($update_sql);
        $result = $stmt->execute([$test_id]);
        $rows = $stmt->rowCount();
        echo "更新结果: " . ($result ? "成功" : "失败") . ", 影响行数: " . $rows . "\n";
        
        // 测试删除测试评论
        $delete_sql = "DELETE FROM comments WHERE id = ?";
        $stmt = $pdo->prepare($delete_sql);
        $stmt->execute([$test_id]);
        echo "删除测试评论成功\n";
        
    } catch (PDOException $e) {
        echo "数据库操作失败: " . $e->getMessage() . "\n";
    }
}
?>