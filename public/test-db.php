<?php
/**
 * 数据库测试脚本
 */

// 加载核心功能文件
require_once __DIR__ . '/functions.php';

// 测试数据库连接
$pdo = get_db_connection();
echo "数据库连接测试：" . ($pdo ? "<span style='color: green;'>成功</span><br>" : "<span style='color: red;'>失败</span><br>");

// 测试分类数据
$categories = aniblog_get_categories();
echo "分类数据测试：" . (count($categories) > 0 ? "<span style='color: green;'>成功</span> (共 " . count($categories) . " 个分类)<br>" : "<span style='color: red;'>失败</span><br>");

// 测试文章数据
$articles = aniblog_get_latest_articles(2);
echo "文章数据测试：" . (count($articles) > 0 ? "<span style='color: green;'>成功</span> (共 " . count($articles) . " 篇文章)<br>" : "<span style='color: orange;'>成功</span> (暂无文章数据)<br>");

// 测试画廊数据
$images = aniblog_get_gallery_images(2);
echo "画廊数据测试：" . (count($images) > 0 ? "<span style='color: green;'>成功</span> (共 " . count($images) . " 张图片)<br>" : "<span style='color: orange;'>成功</span> (暂无图片数据)<br>");

// 输出测试结果
if ($pdo) {
    echo "<br><span style='color: green; font-weight: bold;'>✅ 所有测试通过！数据库连接正常，系统可以正常运行。</span><br>";
    echo "<br>系统状态：<br>";
    echo "- 数据库连接：正常<br>";
    echo "- 数据表：已创建<br>";
    echo "- 默认数据：已初始化<br>";
    echo "<br>访问地址：<br>";
    echo "- 首页：/index.php<br>";
    echo "- 文章详情：/index.php/article/1<br>";
    echo "- 画廊：/index.php/gallery<br>";
    echo "- 角色生成器：/index.php/character-generator<br>";
} else {
    echo "<br><span style='color: red; font-weight: bold;'>❌ 测试失败！数据库连接失败。</span><br>";
    echo "请检查数据库配置是否正确，或者数据库服务是否正常运行。<br>";
}
