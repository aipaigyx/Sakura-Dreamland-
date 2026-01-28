<?php
/**
 * 统一的评论API处理器
 * 处理所有评论相关的请求，包括发表、回复、删除、审核等
 */



// 设置JSON响应头
header('Content-Type: application/json');

// 加载数据库连接和函数文件
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 获取请求方法和操作类型
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// 初始化响应数组
$response = [
    'success' => false,
    'message' => '无效的请求',
    'data' => null
];

// 根据请求方法和操作类型处理不同的评论功能
try {
    switch ($method) {
        case 'POST':
            handlePostRequest($action, $response);
            break;
        case 'GET':
            handleGetRequest($action, $response);
            break;
        default:
            $response['message'] = '不支持的请求方法';
            break;
    }
} catch (Exception $e) {
    $response['message'] = '服务器内部错误: ' . $e->getMessage();
}

// 输出JSON响应
echo json_encode($response);
exit;

/**
 * 处理POST请求
 */
function handlePostRequest($action, &$response) {
    switch ($action) {
        case 'add':
            addComment($response);
            break;
        case 'reply':
            replyComment($response);
            break;
        case 'delete':
            deleteComment($response);
            break;
        case 'moderate':
            moderateComment($response);
            break;
        case 'like':
            likeComment($response);
            break;
        default:
            $response['message'] = '无效的操作';
            break;
    }
}

/**
 * 处理GET请求
 */
function handleGetRequest($action, &$response) {
    switch ($action) {
        case 'list':
            getComments($response);
            break;
        case 'replies':
            getReplies($response);
            break;
        default:
            $response['message'] = '无效的操作';
            break;
    }
}

/**
 * 添加评论
 */
function addComment(&$response) {
    // 获取评论数据
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    // 修复parent_id处理：当parent_id为空或0时，将其设置为null
    $parent_id = isset($_POST['parent_id']) && (int)$_POST['parent_id'] > 0 ? (int)$_POST['parent_id'] : null;
    
    // 验证数据
    if (empty($article_id) || empty($content)) {
        $response['message'] = '文章ID和评论内容不能为空';
        return;
    }
    
    // 获取用户ID
    // 已登录用户使用会话中的ID，否则使用临时ID（如果有），否则使用默认值1
    $user_id = 1; // 默认游客ID
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } elseif (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
        $user_id = (int)$_POST['user_id'];
    }
    
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
    
    // 插入评论到数据库
    $sql = "INSERT INTO comments (content, user_id, name, email, article_id, parent_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $status = $user_id == 1 ? 'pending' : 'approved'; // 游客评论默认待审核，登录用户评论默认通过
    $pdo = get_db_connection();
    
    if ($pdo !== false) {
        $result = db_exec($sql, [$content, $user_id, $name, $email, $article_id, $parent_id, $status], $pdo);
        
        if ($result !== false) {
            // 立即获取新评论的ID，使用同一PDO连接
            $comment_id = $pdo->lastInsertId();
            
            // 更新文章的评论计数
            $sql = "UPDATE articles SET comment_count = comment_count + 1 WHERE id = ?";
            db_exec($sql, [$article_id], $pdo);
            
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
    } else {
        $response['message'] = '数据库连接失败，请稍后重试';
    }
}

/**
 * 回复评论
 */
function replyComment(&$response) {
    // 获取评论ID和回复内容
    $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    
    if (empty($comment_id) || $comment_id <= 0 || empty($content)) {
        $response['message'] = '评论ID和回复内容不能为空，且评论ID必须是有效的正数';
        return;
    }
    
    // 获取被回复评论的信息
    $sql = "SELECT article_id, user_id FROM comments WHERE id = ?";
    $parent_comment = db_query_one($sql, [$comment_id]);
    
    if (!$parent_comment) {
        $response['message'] = '被回复的评论不存在';
        return;
    }
    
    $article_id = $parent_comment['article_id'];
    
    // 获取回复用户ID
    $user_id = 1; // 默认游客ID
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } elseif (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
        $user_id = (int)$_POST['user_id'];
    }
    
    // 获取回复用户的名称和邮箱
    $name = '访客';
    $email = '';
    if ($user_id != 1) {
        $sql = "SELECT username, email FROM users WHERE id = ?";
        $user = db_query_one($sql, [$user_id]);
        if ($user) {
            $name = $user['username'];
            $email = $user['email'];
        }
    } else {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '访客';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    }
    
    // 插入回复
    $sql = "INSERT INTO comments (content, user_id, name, email, article_id, parent_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $status = $user_id == 1 ? 'pending' : 'approved'; // 游客回复默认待审核，登录用户回复默认通过
    $pdo = get_db_connection();
    
    if ($pdo !== false) {
        $result = db_exec($sql, [$content, $user_id, $name, $email, $article_id, $comment_id, $status], $pdo);
        
        if ($result !== false) {
            // 立即获取新回复的ID，使用同一PDO连接
            $reply_id = $pdo->lastInsertId();
            
            // 更新文章的评论计数
            $sql = "UPDATE articles SET comment_count = comment_count + 1 WHERE id = ?";
            db_exec($sql, [$article_id], $pdo);
            
            // 构建回复数据
            $reply = [
                'id' => $reply_id,
                'content' => $content,
                'user_id' => $user_id,
                'name' => $name,
                'email' => $email,
                'article_id' => $article_id,
                'parent_id' => $comment_id,
                'status' => $status,
                'likes' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $response['success'] = true;
            $response['message'] = $user_id == 1 ? '回复提交成功，等待审核' : '回复提交成功';
            $response['data'] = $reply;
        } else {
            $response['message'] = '回复提交失败，请稍后重试';
        }
    } else {
        $response['message'] = '数据库连接失败，请稍后重试';
    }
}

/**
 * 删除评论
 */
function deleteComment(&$response) {
    // 获取评论ID
    $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    
    if (empty($comment_id)) {
        $response['message'] = '无效的评论ID';
        return;
    }
    
    // 获取评论关联的文章ID
    $sql = "SELECT article_id FROM comments WHERE id = ?";
    $comment = db_query_one($sql, [$comment_id]);
    
    if (!$comment) {
        $response['message'] = '评论不存在';
        return;
    }
    
    $article_id = $comment['article_id'];
    
    // 先删除子评论
    $sql = "DELETE FROM comments WHERE parent_id = ?";
    $child_result = db_exec($sql, [$comment_id]);
    
    // 然后删除主评论
    $sql = "DELETE FROM comments WHERE id = ?";
    $main_result = db_exec($sql, [$comment_id]);
    
    if ($child_result !== false && $main_result !== false) {
        // 计算总删除数量
        $total_deleted = $child_result + $main_result;
        
        // 更新文章的评论计数
        if ($total_deleted > 0) {
            $sql = "UPDATE articles SET comment_count = comment_count - ? WHERE id = ?";
            db_exec($sql, [$total_deleted, $article_id]);
        }
        
        $response['success'] = true;
        $response['message'] = '评论已删除';
        $response['data'] = [
            'comment_id' => $comment_id,
            'total_deleted' => $total_deleted
        ];
    } else {
        $response['message'] = '删除评论失败';
    }
}

/**
 * 审核评论
 */
function moderateComment(&$response) {
    // 获取评论ID和审核状态
    $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    // 验证参数
    if (empty($comment_id) || !in_array($status, ['pending', 'approved', 'rejected'])) {
        $response['message'] = '无效的参数';
        return;
    }
    
    // 检查评论是否存在
    $sql = "SELECT id FROM comments WHERE id = ?";
    $comment = db_query_one($sql, [$comment_id]);
    
    if (!$comment) {
        $response['message'] = '评论不存在';
        return;
    }
    
    // 更新评论状态
    $sql = "UPDATE comments SET status = ? WHERE id = ?";
    $result = db_exec($sql, [$status, $comment_id]);
    
    if ($result !== false) {
        $response['success'] = true;
        $response['message'] = '评论状态已更新';
        $response['data'] = [
            'comment_id' => $comment_id,
            'status' => $status
        ];
    } else {
        $response['message'] = '更新评论状态失败';
    }
}

/**
 * 点赞评论
 */
function likeComment(&$response) {
    // 获取评论ID
    $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    
    if (empty($comment_id)) {
        $response['message'] = '无效的评论ID';
        return;
    }
    
    // 这里可以添加点赞逻辑，例如：
    // 1. 检查用户是否已经点赞
    // 2. 如果没有，增加点赞计数
    // 3. 如果已经点赞，取消点赞
    
    // 暂时简化实现，直接增加点赞计数
    $sql = "UPDATE comments SET likes = likes + 1 WHERE id = ?";
    $result = db_exec($sql, [$comment_id]);
    
    if ($result !== false) {
        // 获取更新后的点赞数
        $sql = "SELECT likes FROM comments WHERE id = ?";
        $comment = db_query_one($sql, [$comment_id]);
        
        $response['success'] = true;
        $response['message'] = '点赞成功';
        $response['data'] = [
            'comment_id' => $comment_id,
            'likes' => $comment['likes']
        ];
    } else {
        $response['message'] = '点赞失败';
    }
}

/**
 * 获取评论列表
 */
function getComments(&$response) {
    // 获取文章ID和分页参数
    $article_id = isset($_GET['article_id']) ? (int)$_GET['article_id'] : 0;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
    
    if (empty($article_id)) {
        $response['message'] = '无效的文章ID';
        return;
    }
    
    // 计算偏移量
    $offset = ($page - 1) * $per_page;
    
    // 获取主评论列表
    $sql = "SELECT c.*, u.username, u.avatar FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.article_id = ? AND c.parent_id IS NULL AND c.status = 'approved' ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
    $comments = db_query($sql, [$article_id, $per_page, $offset]);
    
    // 获取每个评论的回复
    foreach ($comments as &$comment) {
        $sql = "SELECT c.*, u.username, u.avatar FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.parent_id = ? AND c.status = 'approved' ORDER BY c.created_at ASC";
        $comment['replies'] = db_query($sql, [$comment['id']]);
    }
    
    // 获取总评论数
    $sql = "SELECT COUNT(*) as total FROM comments WHERE article_id = ? AND parent_id IS NULL AND status = 'approved'";
    $total = db_query_one($sql, [$article_id])['total'];
    
    $response['success'] = true;
    $response['message'] = '获取评论列表成功';
    $response['data'] = [
        'comments' => $comments,
        'pagination' => [
            'page' => $page,
            'per_page' => $per_page,
            'total' => $total,
            'total_pages' => ceil($total / $per_page)
        ]
    ];
}

/**
 * 获取评论回复
 */
function getReplies(&$response) {
    // 获取评论ID
    $comment_id = isset($_GET['comment_id']) ? (int)$_GET['comment_id'] : 0;
    
    if (empty($comment_id)) {
        $response['message'] = '无效的评论ID';
        return;
    }
    
    // 获取评论的回复
    $sql = "SELECT c.*, u.username, u.avatar FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.parent_id = ? AND c.status = 'approved' ORDER BY c.created_at ASC";
    $replies = db_query($sql, [$comment_id]);
    
    $response['success'] = true;
    $response['message'] = '获取回复列表成功';
    $response['data'] = [
        'replies' => $replies,
        'total' => count($replies)
    ];
}
