<?php
session_start(); // Убедитесь, что сессия уже запущена или включена

// Проверяем, что пользователь авторизован
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    exit;
}

include 'redis.php';

// Получаем количество пользователей в очереди
$queueLength = $redis->lLen('game_queue');

// Задаем минимальное количество игроков, необходимое для начала игры
$minPlayers = 2;

if ($queueLength >= $minPlayers) {
    // Если в очереди есть достаточно игроков, возвращаем успешный статус
    http_response_code(200); // OK
} else {
    // Если в очереди недостаточно игроков, возвращаем ошибку или другой статус
    http_response_code(400); // Bad request или другой подходящий статус
}
?>
