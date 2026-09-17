<?php

header('Content-Type: application/json');

if (($_SERVER['HTTP_AUTHORIZATION'] ?? '') !== 'Bearer test-key') {
    http_response_code(401);
    echo json_encode(['status' => 'unauthorized']);
    return;
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/v3/error') {
    http_response_code(422);
    echo json_encode(['message' => 'bad input']);
    return;
}

echo json_encode([
    'method' => $_SERVER['REQUEST_METHOD'],
    'path' => $path,
    'query' => $_GET,
    'body' => json_decode(file_get_contents('php://input'), true),
]);
