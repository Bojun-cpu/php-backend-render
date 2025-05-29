<?php

header("Access-Control-Allow-Origin: https://nextjs-project-sandy-one.vercel.app");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}


$conn = pg_connect("host=ep-summer-art-a44gr677-pooler.us-east-1.aws.neon.tech port=5432 dbname=neondb user=neondb_owner password=npg_S0dr5QJLOwkB sslmode=require");

if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "wrong"]);
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
