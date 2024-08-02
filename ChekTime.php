<?php

// URL, на который будет выполняться запрос (замените на свой)
$url = 'https://ivan4aika.ru/chek.php';

while (true) {
    // Выполнение GET-запроса с помощью file_get_contents
    $response = file_get_contents($url);

    // Проверка ответа
    if ($response === false) {
        echo "Ошибка при выполнении запроса.\n";
    } else {
        echo "Запрос выполнен успешно.\n";
    }

    // Ждем 5 секунд перед следующим запросом
    sleep(5);
}
