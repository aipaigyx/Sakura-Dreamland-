<?php
// 测试评论提交错误
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 模拟评论提交
$article_id = 1;
$name = '测试访客';
$email = 'test@example.com';
$content = '测试评论提交失败问题';
$parent_id = null;
$user_id = 176820930980; // 我们生成的临时ID

// 输出调试信息
echo "开始测试评论提交...<br>";
echo "article_id: $article_id<br>";
echo "name: $name<br>";
echo "email: $email<br>";
echo "content: $content<br>";
echo "parent_id: " . ($parent_id ?? 'null') . "<br>";
echo "user_id: $user_id<br>";
echo "user_id类型: " . gettype($user_id) . "<br>";
echo "user_id大小: " . PHP_INT_SIZE . "字节<br>";
echo "PHP_INT_MAX: " . PHP_INT_MAX . "<br>";
echo "user_id是否大于PHP_INT_MAX: " . ($user_id > PHP_INT_MAX ? '是' : '否') . "<br>";

// 验证数据
echo "<h3>数据验证</h3>";
if (empty($article_id) || empty($content)) {
    echo "错误：文章ID或内容为空<br>";
} else {
    echo "文章ID和内容验证通过<br>";
}

if ($user_id == 1) {
    echo "这是游客评论<br>";
    if (empty($name) || empty($email)) {
        echo "错误：游客评论需要提供姓名和邮箱<br>";
    } else {
        echo "姓名和邮箱验证通过<br>";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "错误：邮箱格式无效<br>";
        } else {
            echo "邮箱格式验证通过<br>";
        }
    }
} else {
    echo "这是用户评论，不需要验证姓名和邮箱<br>";
}

// 测试SQL插入
echo "<h3>测试SQL插入</h3>";
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
} catch (PDOException $e) {
    echo "SQL执行失败: " . $e->getMessage() . "<br>";
    echo "错误代码: " . $e->getCode() . "<br>";
    echo "错误文件: " . $e->getFile() . "<br>";
    echo "错误行号: " . $e->getLine() . "<br>";
}
