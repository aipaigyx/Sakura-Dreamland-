<?php
// 测试修复后的评论提交
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 测试新的临时ID生成逻辑
echo "测试新的临时ID生成逻辑...<br>";

// 生成唯一的整数ID，确保在MySQL int(11)范围内（-2147483648到2147483647）
// 使用当前时间戳的后8位 + 3位随机数，确保在有效范围内
$visitor_id = (int)(substr(time(), -8) . rand(100, 999));
// 确保生成的ID大于1（1是系统默认的游客ID）
if ($visitor_id <= 1) {
    $visitor_id = 2;
}

// 输出调试信息
echo "生成的临时ID: $visitor_id<br>";
echo "ID类型: " . gettype($visitor_id) . "<br>";
echo "ID大小: " . PHP_INT_SIZE . "字节<br>";
echo "PHP_INT_MAX: " . PHP_INT_MAX . "<br>";
echo "ID是否大于PHP_INT_MAX: " . ($visitor_id > PHP_INT_MAX ? '是' : '否') . "<br>";
echo "ID是否在MySQL int(11)范围内: " . ($visitor_id >= -2147483648 && $visitor_id <= 2147483647 ? '是' : '否') . "<br>";

// 模拟评论提交
$article_id = 1;
$name = '测试访客';
$email = 'test@example.com';
$content = '测试修复后的评论提交';
$parent_id = null;
$user_id = $visitor_id;

// 测试SQL插入
echo "<h3>测试评论提交</h3>";
$sql = "INSERT INTO comments (content, user_id, name, email, article_id, parent_id) VALUES (?, ?, ?, ?, ?, ?)";
$params = [$content, $user_id, $name, $email, $article_id, $parent_id];

// 直接执行SQL并显示错误信息
$pdo = get_db_connection();
if (!$pdo) {
    echo "无法连接到数据库<br>";
    exit;
}

try {
    $stmt = $pdo->prepare($sql);
    echo "SQL准备成功<br>";
    $stmt->execute($params);
    echo "SQL执行成功<br>";
    $rowCount = $stmt->rowCount();
    echo "影响的行数: $rowCount<br>";
    $lastInsertId = $pdo->lastInsertId();
    echo "最后插入的ID: $lastInsertId<br>";
    echo "<span style='color: green;'>评论提交成功！</span><br>";
} catch (PDOException $e) {
    echo "SQL执行失败: " . $e->getMessage() . "<br>";
    echo "错误代码: " . $e->getCode() . "<br>";
    echo "<span style='color: red;'>评论提交失败！</span><br>";
}
