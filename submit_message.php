<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$conn = pg_connect("host=ep-summer-art-a44gr677-pooler.us-east-1.aws.neon.tech port=5432 dbname=neondb user=neondb_owner password=npg_S0dr5QJLOwkB sslmode=require");


if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "❌ 数据库连接失败: " . pg_last_error($conn)]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$name = $data["name"] ?? '';
$email = $data["email"] ?? '';
$message = $data["message"] ?? '';

if (!$name || !$email || !$message) {
    http_response_code(400);
    echo json_encode(["error" => "❌ 所有字段都是必填的"]);
    exit;
}

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
