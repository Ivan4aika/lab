<?php
include 'db.php'; // Подключение к базе данных

session_start();

// Проверяем, был ли отправлен POST запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из POST запроса
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Защита от SQL инъекций
    $login = mysqli_real_escape_string($db, $login);

    // Хэшируем пароль
    $hashed_password = md5($password); 

    // Формируем SQL запрос для поиска пользователя
    $sql = "SELECT * FROM users WHERE login='$login' AND password='$hashed_password'";
    $result = $db->query($sql);

    // Проверяем, найден ли пользователь
    if ($result->num_rows == 1) {
        // Пользователь найден, отправляем успешный ответ
        $_SESSION['login'] = $login;
        echo json_encode(array('status' => 'success', 'message' => 'Авторизация успешна!'));
    } else {
        // Пользователь не найден, отправляем ошибку
        echo json_encode(array('status' => 'error', 'message' => 'Ошибка авторизации. Проверьте логин и пароль.'));
    }
} else {
    // Если запрос не был POST, возвращаем ошибку
    echo json_encode(array('status' => 'error', 'message' => 'Метод запроса не поддерживается.'));
}

// Закрываем соединение с базой данных
$db->close();
?>
