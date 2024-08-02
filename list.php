<?php
// Подключаемся к Redis
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

// Проверка подключения к Redis
if (!$redis->ping()) {
    exit;
}

// Ключ очереди в Redis
$queueKey = 'game_queue';




// Получаем количество элементов в очереди
$queueLength = $redis->lLen($queueKey);
$queueItems = $redis->lRange($queueKey, 0, $queueLength - 1);

if ($queueLength > 1) {
    echo "<ul>";
    // Перебираем элементы очереди по парам
    for ($i = 0; $i < $queueLength; $i += 2) {
        // Убеждаемся, что у нас есть пара для отображения
        if ($i + 1 < $queueLength) {
            $user_json1 = $queueItems[$i];
            $user_json2 = $queueItems[$i + 1];

            $user_data1 = json_decode($user_json1, true);
            $user_data2 = json_decode($user_json2, true);

            $login1 = $user_data1['login'];
            $login2 = $user_data2['login'];

            $recipientLogins = [$login1, $login2];
            $message = json_encode(array(
                'message' => 'Привет, Алиса и Боб!'
            ));




            
        } else {
           
        }
    }
    echo "</ul>";
} else {
    
}
?>


<script>
const login = "admin";
const wsUri = `wss://ivan4aika.ru/websocket?login=${login}`;
const websocket = new WebSocket(wsUri);

websocket.onopen = function(evt) {
    console.log("WebSocket открыт");
    // Перемещаем вызов sendMessage сюда, чтобы убедиться, что соединение открыто
    sendMessage(["4aika", "Олег"], "куку");
};

websocket.onmessage = function(evt) {
    const data = JSON.parse(evt.data);
    if (data.type === "sendMessageToUsersResponse") {
        if (Array.isArray(data.recipients) && data.recipients.length > 0) {
            console.log("Сообщение успешно отправлено пользователям:", data.recipients);
        } else {
            console.log("Сообщение не было отправлено ни одному пользователю");
        }
    } else {
        console.log("Получено неизвестное сообщение от сервера:", data);
    }
};

function sendMessage(recipientLogins, message) {
    const payload = {
        type: "sendMessageToUsers",
        recipients: recipientLogins,
        message: message
    };
    websocket.send(JSON.stringify(payload));
}

</script>