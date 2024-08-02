<?php
// Включение файла для подключения к Redis
include 'redis.php';

$queueKey = 'game_queue';

// Удаление ключа (очереди)
$redis->del($queueKey);

echo "Очередь успешно очищена.";
?>
