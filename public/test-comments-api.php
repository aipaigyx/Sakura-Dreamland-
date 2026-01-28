<?php
// 测试评论API
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

echo "<h2>测试评论API</h2>";

// 测试添加评论
function testAddComment() {
    echo "<h3>测试添加评论</h3>";
    
    $data = [
        'article_id' => 1,
        'name' => '测试访客',
        'email' => 'test@example.com',
        'content' => '测试评论API添加评论',
        'user_id' => rand(100000, 2147483647)
    ];
    
    $response = sendRequest('/comments-api.php?action=add', $data, 'POST');
    
    echo "<pre>";
    print_r($response);
    echo "</pre>";
    
    return $response;
}

// 测试获取评论列表
function testGetComments() {
    echo "<h3>测试获取评论列表</h3>";
    
    $response = sendRequest('/comments-api.php?action=list&article_id=1', [], 'GET');
    
    echo "<pre>";
    print_r($response);
    echo "</pre>";
    
    return $response;
}

// 发送HTTP请求
function sendRequest($url, $data, $method = 'GET') {
    $curl = curl_init();
    
    if ($method === 'POST') {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    } else {
        $url .= '&' . http_build_query($data);
    }
    
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    
    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    curl_close($curl);
    
    if ($http_code === 200) {
        return json_decode($response, true);
    } else {
        return [
            'success' => false,
            'message' => "HTTP请求失败，状态码: $http_code",
            'response' => $response
        ];
    }
}

// 运行测试
testAddComment();
testGetComments();
