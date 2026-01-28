<?php
// 调试评论功能
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

echo "<h2>调试评论功能</h2>";

// 测试数据库连接
function testDbConnection() {
    echo "<h3>测试数据库连接</h3>";
    
    $pdo = get_db_connection();
    if ($pdo) {
        echo "<p style='color: green;'>数据库连接成功</p>";
        
        // 测试comments表
        $sql = "SHOW TABLES LIKE 'comments'";
        $result = $pdo->query($sql);
        if ($result->rowCount() > 0) {
            echo "<p style='color: green;'>comments表存在</p>";
            
            // 显示表结构
            $sql = "DESCRIBE comments";
            $result = $pdo->query($sql);
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>字段名</th><th>类型</th><th>空值</th><th>键</th><th>默认值</th><th>额外信息</th></tr>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                foreach ($row as $col) {
                    echo "<td>$col</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            
            // 测试插入评论
            testInsertComment($pdo);
        } else {
            echo "<p style='color: red;'>comments表不存在</p>";
        }
    } else {
        echo "<p style='color: red;'>数据库连接失败</p>";
    }
}

// 测试插入评论
function testInsertComment($pdo) {
    echo "<h3>测试插入评论</h3>";
    
    $data = [
        'content' => '测试评论插入',
        'user_id' => 123456,
        'name' => '测试访客',
        'email' => 'test@example.com',
        'article_id' => 1,
        'parent_id' => null,
        'status' => 'approved'
    ];
    
    try {
        $sql = "INSERT INTO comments (content, user_id, name, email, article_id, parent_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$data['content'], $data['user_id'], $data['name'], $data['email'], $data['article_id'], $data['parent_id'], $data['status']]);
        
        if ($result) {
            $last_id = $pdo->lastInsertId();
            echo "<p style='color: green;'>评论插入成功，ID: $last_id</p>";
            
            // 查询刚插入的评论
            $sql = "SELECT * FROM comments WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$last_id]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<h4>刚插入的评论</h4>";
            echo "<pre>";
            print_r($comment);
            echo "</pre>";
            
            // 删除测试评论
            $sql = "DELETE FROM comments WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$last_id]);
        } else {
            echo "<p style='color: red;'>评论插入失败</p>";
            echo "<p>错误信息: " . implode(", ", $stmt->errorInfo()) . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>评论插入异常: " . $e->getMessage() . "</p>";
    }
}

// 测试db_exec函数
function testDbExec() {
    echo "<h3>测试db_exec函数</h3>";
    
    $data = [
        'content' => '测试db_exec函数',
        'user_id' => 123456,
        'name' => '测试访客',
        'email' => 'test@example.com',
        'article_id' => 1,
        'parent_id' => null,
        'status' => 'approved'
    ];
    
    $sql = "INSERT INTO comments (content, user_id, name, email, article_id, parent_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $result = db_exec($sql, [$data['content'], $data['user_id'], $data['name'], $data['email'], $data['article_id'], $data['parent_id'], $data['status']]);
    
    echo "<p>db_exec返回值: $result</p>";
    
    if ($result !== false) {
        echo "<p style='color: green;'>db_exec执行成功，影响行数: $result</p>";
        
        // 测试lastInsertId
        $last_id = db_885010189();
        echo "<p>db_885010189返回值: $last_id</p>";
        
        // 测试在同一个连接中获取lastInsertId
        $pdo = get_db_connection();
        $last_id2 = $pdo->lastInsertId();
        echo "<p>直接调用lastInsertId返回值: $last_id2</p>";
    } else {
        echo "<p style='color: red;'>db_exec执行失败</p>";
    }
}

// 测试addComment函数
function testAddComment() {
    echo "<h3>测试addComment函数</h3>";
    
    // 模拟POST数据
    $_POST['article_id'] = 1;
    $_POST['name'] = '测试访客';
    $_POST['email'] = 'test@example.com';
    $_POST['content'] = '测试addComment函数';
    $_POST['user_id'] = 123456;
    
    // 模拟响应
    $response = [
        'success' => false,
        'message' => '无效的请求',
        'data' => null
    ];
    
    // 调用addComment函数
    require_once __DIR__ . '/comments-api.php';
    
    // 重新定义addComment函数，因为它在comments-api.php中是函数内部调用的
    function addComment(&$response) {
        // 获取评论数据
        $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        
        echo "<p>获取到的数据：</p>";
        echo "<ul>";
        echo "<li>article_id: $article_id</li>";
        echo "<li>name: $name</li>";
        echo "<li>email: $email</li>";
        echo "<li>content: $content</li>";
        echo "<li>parent_id: " . ($parent_id ?? 'null') . "</li>";
        echo "</ul>";
        
        // 验证数据
        if (empty($article_id) || empty($content)) {
            $response['message'] = '文章ID和评论内容不能为空';
            return;
        }
        
        // 获取用户ID
        $user_id = 1; // 默认游客ID
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        } elseif (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
            $user_id = (int)$_POST['user_id'];
        }
        
        echo "<p>处理后的user_id: $user_id</p>";
        
        // 只有游客评论才需要验证name和email
        if ($user_id == 1) {
            if (empty($name) || empty($email)) {
                $response['message'] = '昵称和邮箱不能为空';
                return;
            }
            
            // 简单的邮箱验证
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = '无效的邮箱格式';
                return;
            }
        }
        
        echo "<p>数据验证通过</p>";
        
        // 插入评论到数据库
        $sql = "INSERT INTO comments (content, user_id, name, email, article_id, parent_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $status = $user_id == 1 ? 'pending' : 'approved'; // 游客评论默认待审核，登录用户评论默认通过
        echo "<p>执行的SQL: $sql</p>";
        echo "<p>参数: " . implode(", ", [$content, $user_id, $name, $email, $article_id, $parent_id, $status]) . "</p>";
        
        $result = db_exec($sql, [$content, $user_id, $name, $email, $article_id, $parent_id, $status]);
        
        echo "<p>db_exec返回值: $result</p>";
        
        if ($result !== false) {
            // 更新文章的评论计数
            $sql = "UPDATE articles SET comment_count = comment_count + 1 WHERE id = ?";
            db_exec($sql, [$article_id]);
            
            // 获取新评论的ID
            $comment_id = db_885010189();
            
            echo "<p>新评论ID: $comment_id</p>";
            
            // 构建评论数据
            $comment = [
                'id' => $comment_id,
                'content' => $content,
                'user_id' => $user_id,
                'name' => $name,
                'email' => $email,
                'article_id' => $article_id,
                'parent_id' => $parent_id,
                'status' => $status,
                'likes' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $response['success'] = true;
            $response['message'] = $user_id == 1 ? '评论提交成功，等待审核' : '评论提交成功';
            $response['data'] = $comment;
        } else {
            $response['message'] = '评论提交失败，请稍后重试';
        }
    }
    
    addComment($response);
    
    echo "<h4>addComment返回的响应</h4>";
    echo "<pre>";
    print_r($response);
    echo "</pre>";
}

// 运行所有测试
testDbConnection();
testDbExec();
testAddComment();
