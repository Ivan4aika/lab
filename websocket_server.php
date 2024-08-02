<?php

require __DIR__ . '/vendor/autoload.php'; // Путь к автозагрузчику Composer

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;

// Создаем класс-обработчик для веб-сокет сервера
class MyWebSocketServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $db) {
        // Получаем логин из параметров URL
        $queryParams = $db->httpRequest->getUri()->getQuery();
        parse_str($queryParams, $params);
        $login = isset($params['login']) ? $params['login'] : 'Guest';
    
        // Сохраняем данные соединения в базу данных
        $connectionResourceId = $db->resourceId;
        $this->saveConnection($login, $connectionResourceId);
    
        // Присваиваем никнейм
        $db->nickname = $login;
    
        $this->clients->attach($db);
        echo "New connection! ({$db->resourceId})\n";
        echo "User {$login} connected\n";
    }

    public function sendMessageToUsers(array $recipientLogins, $message) {
        foreach ($this->clients as $client) {
            if (in_array($client->nickname, $recipientLogins)) {
                $client->send($message);
            }
        }
    }
    
    private function formatMessage($nickname, $message) {
        // Формируем сообщение для отправки
        return json_encode([
            'message' => "{$nickname}: {$message}"
        ]);
    }
    
    
    

    private function saveConnection($login, $connectionResourceId) {
        // Подключение к базе данных
        $servername = "localhost";
        $username = "all";
        $password = "-g6SIvTOxW";
        $dbname = "main";
    
        // Создаем подключение
        $db = new mysqli($servername, $username, $password, $dbname);
    
        // Проверяем подключение
        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }
    
        // Выполняем запрос на вставку
        $stmt = $db->prepare("INSERT INTO websocket_connections (login, connection_resource_id, connected_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("si", $login, $connectionResourceId);
        $stmt->execute();
        echo "New connection saved for login '{$login}'";
    
        // Закрываем запрос и подключение
        $stmt->close();
        $db->close();
    }
    
    

        public function onClose(ConnectionInterface $db) {
            $this->clients->detach($db);
            echo "Connection {$db->resourceId} ({$db->nickname}) has disconnected\n";
        }

        public function onMessage(ConnectionInterface $from, $data) {
            // Разбиваем данные на сообщение, ник отправителя и получателя
            $parts = json_decode($data, true);
        
            if (isset($parts['message']) && isset($parts['from']) && isset($parts['to'])) {
                $message = $parts['message'];
                $fromNickname = $parts['from'];
                $toNickname = $parts['to'];
        
                // Добавляем новый аргумент 'extra' по умолчанию пустой
                $parts['extra'] = $parts['extra'] ?? '';
        
                // Преобразуем данные обратно в JSON-строку
                $modifiedData = json_encode($parts);
        
                // Определяем, нужно ли отправлять всем или конкретному пользователю
                if ($toNickname === 'all') {
                    // Отправляем сообщение всем клиентам, включая отправителя
                    foreach ($this->clients as $client) {
                        $client->send($modifiedData); // Отправляем модифицированные данные
                    }
                } else {
                    // Отправляем сообщение конкретному пользователю, если он существует
                    $this->sendMessageToUsers([$toNickname], $modifiedData); // Отправляем модифицированные данные
                }
            }
        }
        
        


    public function onError(ConnectionInterface $db, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $db->close();
    }
}

// Создаем сервер и привязываем обработчик
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new MyWebSocketServer()
        )
    ),
    8080 // Порт, на котором будет работать сервер
);

echo "WebSocket server running at port 8080...\n";

// Запускаем сервер
$server->run();
