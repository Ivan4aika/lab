<?php

include 'db.php';

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Защита от SQL инъекций
    $login = mysqli_real_escape_string($db, $login);
    $password = mysqli_real_escape_string($db, $password);

    // Хэширование пароля (рекомендуется для безопасности)
    $hashed_password = md5($password);

    // Проверка наличия пользователя в базе данных
    $check_query = "SELECT * FROM users WHERE login='$login'";
    $check_result = $db->query($check_query);

    if ($check_result->num_rows > 0) {
        // Пользователь уже существует, возвращаем ошибку
        echo json_encode(array('status' => 'error', 'message' => 'Пользователь с таким логином уже зарегистрирован.'));
    } else {
        // Регистрация нового пользователя
        $insert_query = "INSERT INTO users (login, password) VALUES ('$login', '$hashed_password')";
        if ($db->query($insert_query) === TRUE) {
            echo json_encode(array('status' => 'success', 'message' => 'Регистрация успешна!'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Ошибка регистрации. Попробуйте позже.'));
        }
    }
} else {
    // Если запрос не был POST, возвращаем ошибку
    echo json_encode(array('status' => 'error', 'message' => 'Метод запроса не поддерживается.'));
}

$db->close();
?>
