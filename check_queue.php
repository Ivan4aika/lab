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

// Возвращаем результат проверки
echo json_encode(['inQueue' => $userInQueue]);

?>
