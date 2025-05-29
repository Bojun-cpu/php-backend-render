<?php

// ✅ 允许任意来源跨域请求（不限制 Vercel 域名）
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// ✅ 预检请求处理（OPTIONS）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ✅ 连接数据库（Neon PostgreSQL）
$conn = pg_connect("host=ep-summer-art-a44gr677-pooler.us-east-1.aws.neon.tech port=5432 dbname=neondb user=neondb_owner password=npg_S0dr5QJLOwkB sslmode=require");

if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "❌ Database is not connected: " . pg_last_error($conn)]);
    exit;
}

// ✅ 获取请求数据（JSON）
$data = json_decode(file_get_contents("php://input"), true);
$name = $data["name"] ?? '';
$email = $data["email"] ?? '';
$message = $data["message"] ?? '';

// ✅ 检查必填字段
if (!$name || !$email || !$message) {
    http_response_code(400);
    echo json_encode(["error" => "❌ All fields are required."]);
    exit;
}

// ✅ 插入留言数据
$sql = "INSERT INTO messages (name, email, message) VALUES ($1, $2, $3)";
$result = pg_query_params($conn, $sql, [$name, $email, $message]);

if ($result) {
    echo json_encode(["success" => "✅ Successful"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "❌ fail to leave message: " . pg_last_error($conn)]);
}

pg_close($conn);
?>
