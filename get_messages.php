<?php
// 启用 CORS，允许前端跨域访问此 API（建议只允许你的域名）
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// 自动读取 Neon 数据库连接字符串（来自 Vercel 设置）
$connection_string = getenv("DATABASE_URL");

// 尝试连接数据库
$conn = pg_connect($connection_string);
if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "❌ Database connection failed: " . pg_last_error()]);
    exit;
}

// 查询 messages 表
$sql = "SELECT * FROM messages ORDER BY created_at DESC";
$result = pg_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "❌ Query failed: " . pg_last_error($conn)]);
    exit;
}

// 整理数据
$messages = [];
while ($row = pg_fetch_assoc($result)) {
    $messages[] = $row;
}

// 返回 JSON 数据
echo json_encode($messages);
pg_close($conn);
?>
