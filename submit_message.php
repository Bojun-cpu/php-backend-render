<?php
// 跨域配置（允许来自你的 Vercel 域名）
header("Access-Control-Allow-Origin: https://nextjs-project-sandy-one.vercel.app");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// 处理预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// 数据库连接
$conn = pg_connect("host=ep-summer-art-a44gr677-pooler.us-east-1.aws.neon.tech port=5432 dbname=neondb user=neondb_owner password=npg_S0dr5QJLOwkB sslmode=require");

if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "❌ 数据库连接失败: " . pg_last_error($conn)]);
    exit;
}

// 获取请求体 JSON 数据
$data = json_decode(file_get_contents("php://input"), true);
$name = $data["name"] ?? '';
$email = $data["email"] ?? '';
$message = $data["message"] ?? '';

// 表单字段验证
if (!$name || !$email || !$message) {
    http_response_code(400);
    echo json_encode(["error" => "❌ 所有字段都是必填的"]);
    exit;
}

// 插入留言
$sql = "INSERT INTO messages (name, email, message) VALUES ($1, $2, $3)";
$result = pg_query_params($conn, $sql, [$name, $email, $message]);

if ($result) {
    echo json_encode(["success" => "✅ 留言提交成功"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "❌ 数据插入失败: " . pg_last_error($conn)]);
}

pg_close($conn);
?>
