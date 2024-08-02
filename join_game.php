<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Присоединиться к игре</title>
</head>
<body>

<?php include 'header.php'; ?>

<h1>Присоединиться к игре</h1>

<!-- Кнопка для отмены поиска игры -->
<button onclick="cancelSearch()">Отменить поиск</button>

<script>
var ws = new WebSocket('ws://localhost:8080'); // Замените адрес и порт WebSocket сервера

ws.onopen = function() {
    console.log('Соединение установлено.');
    // При открытии страницы можно отправить сообщение на сервер о том, что пользователь присоединился к игре
    // Например, отправить информацию о текущем пользователе на сервер
};

ws.onmessage = function(event) {
    console.log('Получено сообщение от сервера:', event.data);
    if (event.data === 'game_ready') {
        // Когда игра готова, перенаправляем пользователя на страницу игры
        window.location.href = 'game_match.php';
    }
};

ws.onerror = function(event) {
    console.error('Произошла ошибка:', event);
};

ws.onclose = function() {
    console.log('Соединение закрыто.');
};

function cancelSearch() {
    // Логика для отмены поиска игры
    // Здесь можно отправить сообщение на сервер о том, что пользователь отменил поиск
}
</script>

</body>
</html>
