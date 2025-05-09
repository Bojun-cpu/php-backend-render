<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn_str = "postgresql://neondb_owner:npg_6YkMQecdl0JN@ep-steep-recipe-a4516x20-pooler.us-east-1.aws.neon.tech/neondb?sslmode=require&options=endpoint%3Dep-steep-recipe-a4516x20";
$conn = pg_connect($conn_str);

if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "数据库连接失败"]);
    exit;
}

$result = pg_query($conn, "SELECT id, name, email, message, created_at FROM messages ORDER BY created_at DESC");

$messages = [];
while ($row = pg_fetch_assoc($result)) {
    $messages[] = $row;
}

echo json_encode($messages);
pg_close($conn);
?>
