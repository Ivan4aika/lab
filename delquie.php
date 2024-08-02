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

// Получение данных из запроса (если нужно)
$requestData = json_decode(file_get_contents('php://input'), true);

// Удаление всех записей из очереди, принадлежащих пользователю
$key = 'game_queue';
$queueLength = $redis->lLen($key); // Получаем количество элементов в очереди

for ($i = 0; $i < $queueLength; $i++) {
    $jsonData = $redis->lIndex($key, $i);
    $data = json_decode($jsonData, true);

    if ($data && isset($data['login']) && $data['login'] === $login) {
        // Нашли элемент, который принадлежит текущему пользователю, удаляем его
        $redis->lSet($key, $i, ""); // Устанавливаем пустую строку на место элемента
    }
}

// Удаляем все пустые строки из списка
$redis->lRem($key, "", 0);

// Отправка ответа клиенту
http_response_code(200);
echo json_encode(['message' => 'Записи пользователя успешно удалены из очереди']);
