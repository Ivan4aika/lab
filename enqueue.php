<?php

session_start();

// Проверка авторизации пользователя
if (!isset($_SESSION['login'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Пользователь не авторизован']));
}

$login = $_SESSION['login'];

// Подключение к Redis
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

// Проверка наличия пользователя в очереди
$key = 'game_queue';
$queueLength = $redis->lLen($key);
$userInQueue = false;

for ($i = 0; $i < $queueLength; $i++) {
    $item = json_decode($redis->lIndex($key, $i), true);
    if ($item && isset($item['login']) && $item['login'] === $login) {
        $userInQueue = true;
        break;
    }
}

if ($userInQueue) {
    http_response_code(400);
    echo json_encode(['error' => 'Пользователь уже находится в очереди']);
    exit;
}

// Если пользователь не в очереди, добавляем его
$requestData = json_decode(file_get_contents('php://input'), true);

if (!$requestData || empty($requestData['action'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Неверные данные запроса']));
}

$action = $requestData['action'];

// Добавление данных в очередь Redis
$data = [
    'action' => $action,
    'login' => $login,
    'timestamp' => time() // Можно добавить время создания записи
];
$redis->rPush($key, json_encode($data));

// Отправка ответа клиенту
http_response_code(200);
echo json_encode(['message' => 'Запрос успешно добавлен в очередь']);
?>
