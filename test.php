<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket Test</title>
</head>
<body>
    <h1>WebSocket Test</h1>
    <script>
        // Создание нового WebSocket соединения
        var socket = new WebSocket('ws://localhost:8080');

        // Обработчик события на открытие соединения
        socket.onopen = function(event) {
            console.log('WebSocket connection established');
        };

        // Обработчик события на получение сообщения
        socket.onmessage = function(event) {
            console.log('Received message: ' + event.data);
        };

        // Обработчик события на закрытие соединения
        socket.onclose = function(event) {
            console.log('WebSocket connection closed');
        };

        // Обработчик события на ошибку
        socket.onerror = function(error) {
            console.log('WebSocket error: ' + error);
        };
    </script>
</body>
</html>
