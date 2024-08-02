<?php

if (isset($_GET['p1']) && isset($_GET['p2'])) {
    $p1 = $_GET['p1'];
    $p2 = $_GET['p2'];
    

    // Подключение к базе данных
    include 'db.php';

    // Генерация случайного числа от 1 до 2
    $x = rand(1, 2);

    // Подготовка SQL запроса для вставки новой записи
    $sql = "INSERT INTO game_state (player1, player2, init, x) VALUES (?, ?, ?, ?)";

    if ($stmt = $db->prepare($sql)) {
        // Привязка параметров к SQL запросу
        $stmt->bind_param('ssii', $p1, $p2, $x, $x);

        // Выполнение запроса
        if ($stmt->execute()) {
            // Получение ID вставленной записи
            $inserted_id = $stmt->insert_id;
            // Отправка успешного ответа клиенту
            echo json_encode([
                'status' => 'success',
                'message' => 'Record inserted',
                'id' => $inserted_id
            ]);
        } else {
            // Обработка ошибки выполнения запроса
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert record']);
        }

        // Закрытие подготовленного выражения
        $stmt->close();
    } else {
        // Обработка ошибки подготовки запроса
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement']);
    }

    // Закрытие соединения с базой данных (если необходимо)
    // $db->close();
} else {
    // Отправка ответа клиенту в случае ошибки
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
}

?>
