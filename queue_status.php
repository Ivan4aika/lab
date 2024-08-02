<?php
// Подключение к Redis
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

// Получение всех элементов из очереди
$key = 'game_queue';
$queueLength = $redis->lLen($key);

$players = [];

// Получаем все элементы из Redis очереди
for ($i = 0; $i < $queueLength; $i++) {
    $item = json_decode($redis->lIndex($key, $i), true);
    $players[] = $item['login'];
}

// Отправка ответа клиенту в формате JSON
header('Content-Type: application/json');
echo json_encode([
    'playersCount' => $queueLength,
    'players' => $players
]);
?>
