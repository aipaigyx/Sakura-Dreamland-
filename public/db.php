<?php
/**
 * 数据库连接配置文件
 */

// 数据库连接信息
define('DB_HOST', 'localhost');
define('DB_NAME', 'aaa123456');
define('DB_USER', 'aaa123456');
define('DB_PASS', 'aaa123456789');

/**
 * 获取数据库连接
 * @return PDO|null 数据库连接对象或null
 */
function get_db_connection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // 记录错误日志（注释掉，避免输出到标准输出）
        // error_log("数据库连接失败: " . $e->getMessage());
        return false;
    }
}/**
 * 执行查询语句
 * @param string $sql SQL查询语句
 * @param array $params 参数数组
 * @return array 查询结果
 */
function db_query($sql, $params = []) {
    $pdo = get_db_connection();
    if (!$pdo) {
        return [];
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        // 记录错误日志（注释掉，避免输出到标准输出）
        // error_log("查询失败: " . $e->getMessage() . ", SQL: " . $sql);
        return [];
    }
}

/**
 * 执行单条数据查询
 * @param string $sql SQL查询语句
 * @param array $params 参数数组
 * @return array|null 查询结果或null
 */
function db_query_one($sql, $params = []) {
    $results = db_query($sql, $params);
    return $results ? $results[0] : null;
}

/**
 * 执行增删改操作
 * @param string $sql SQL语句
 * @param array $params 参数数组
 * @param PDO|null $pdo 可选的数据库连接对象，如果提供则使用该连接，否则创建新连接
 * @param bool $return_pdo 是否返回PDO连接对象
 * @return int|false|array 影响的行数、false或包含行数和PDO连接的数组
 */
function db_exec($sql, $params = [], $pdo = null, $return_pdo = false) {
    if ($pdo === null) {
        $pdo = get_db_connection();
    }
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rowCount = $stmt->rowCount();
        
        if ($return_pdo) {
            return [
                'rowCount' => $rowCount,
                'pdo' => $pdo
            ];
        }
        
        return $rowCount;
    } catch (PDOException $e) {
        // 记录错误日志（注释掉，避免输出到标准输出）
        // error_log("执行失败: " . $e->getMessage() . ", SQL: " . $sql);
        return false;
    }
}

/**
 * 获取最后插入的ID
 * @param PDO $pdo 可选的数据库连接对象，如果不提供则创建新连接
 * @return int|false 最后插入的ID或false
 */
function db_last_insert_id($pdo = null) {
    if ($pdo === null) {
        $pdo = get_db_connection();
    }
    if (!$pdo) {
        return false;
    }
    
    return $pdo->lastInsertId();
}
