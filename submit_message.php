<?php
// 动态允许本地或 Vercel 来源
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origins = [
    'http://localhost:3000',
    'https://nextjs-project-sandy-one.vercel.app'
];

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: https://nextjs-project-sandy-one.vercel.app");
}

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// 预检请求返回 204
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// 连接 Neon PostgreSQL 数据库
$conn = pg_connect("host=ep-summer-art-a44gr677-pooler.us-east-1.aws.neon.tech port=5432 dbname=neondb user=neondb_owner password=npg_S0dr5QJLOwkB sslmode=require");

if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "❌ Database connection failed: " . pg_last_error($conn)]);
    exit;
}

// 获取 JSON 数据
$data = json_decode(file_get_contents("php://input"), true);
$name = $data["name"] ?? '';
$email = $data["email"] ?? '';
$message = $data["message"] ?? '';

// 字段校验
if (!$name || !$email || !$message) {
    http_response_code(400);
    echo json_encode(["error" => "❌ All fields are required."]);
    exit;
}

// 插入数据库
$sql = "INSERT INTO messages (name, email, message) VALUES ($1, $2, $3)";
$result = pg_query_params($conn, $sql, [$name, $email, $message]);

if ($result) {
    echo json_encode(["success" => "✅ Message submitted successfully."]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "❌ Failed to insert message: " . pg_last_error($conn)]);
}

pg_close($conn);
?>
