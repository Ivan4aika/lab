<?php
session_start(); // Начинаем сессию

// Разрушаем все данные сессии
session_destroy();

// Отправляем ответ клиенту
echo json_encode(array('status' => 'success', 'message' => 'Выход выполнен успешно.'));
?>
