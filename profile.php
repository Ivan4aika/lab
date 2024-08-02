<?php
// Подключение к базе данных
include 'db.php';
include 'header.php';
session_start();

// Функция для проверки авторизации пользователя
function check_authorization() {
    if (!isset($_SESSION['login'])) {
        header('Location: login.php');
        exit();
    }
}

// Функция для получения данных пользователя
function get_user_data($db, $user_login) {
    $query = "SELECT login, name, email, registration_date, avatar_path FROM users WHERE login = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $user_login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    } else {
        echo "Ошибка: Пользователь не найден.";
        exit();
    }
}

// Функция для обновления имени пользователя
function update_user_name($db, $user_login, $new_name) {
    $update_query = "UPDATE users SET name = ? WHERE login = ?";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bind_param("ss", $new_name, $user_login);
    if (!$update_stmt->execute()) {
        echo "Ошибка при обновлении имени: " . $update_stmt->error;
    }
    $update_stmt->close();
}

// Функция для обновления email пользователя
function update_user_email($db, $user_login, $new_email) {
    $update_query = "UPDATE users SET email = ? WHERE login = ?";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bind_param("ss", $new_email, $user_login);
    if (!$update_stmt->execute()) {
        echo "Ошибка при обновлении email: " . $update_stmt->error;
    }
    $update_stmt->close();
}

// Функция для обновления аватара пользователя
function update_user_avatar($db, $user_login) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatar_tmp_name = $_FILES['avatar']['tmp_name'];
        $avatar_name = $_FILES['avatar']['name'];
        $avatar_size = $_FILES['avatar']['size'];

        $allowed_mime_types = array('image/jpeg', 'image/png', 'image/gif');
        if (!in_array($_FILES['avatar']['type'], $allowed_mime_types)) {
            echo "Ошибка: Недопустимый тип файла. Допустимые типы: JPEG, PNG, GIF.";
            exit();
        }

        $max_file_size = 1000000; // 1 MB
        if ($avatar_size > $max_file_size) {
            echo "Ошибка: Размер файла превышает допустимый лимит (1 MB).";
            exit();
        }

        $avatar_path = 'avatars/' . $avatar_name;

        if (move_uploaded_file($avatar_tmp_name, $avatar_path)) {
            $update_query = "UPDATE users SET avatar_path = ? WHERE login = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bind_param("ss", $avatar_path, $user_login);
            if (!$update_stmt->execute()) {
                echo "Ошибка при сохранении пути к аватару: " . $update_stmt->error;
            }
            $update_stmt->close();
            return $avatar_path;
        } else {
            echo "Ошибка при загрузке аватара на сервер.";
            exit();
        }
    }
}

// Проверка авторизации
check_authorization();

// Получение логина текущего пользователя из сессии
$user_login = $_SESSION['login'];

// Извлечение данных пользователя
$user = get_user_data($db, $user_login);

// Обработка данных из формы, если она была отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name'])) {
        update_user_name($db, $user_login, $_POST['name']);
        $user['name'] = $_POST['name'];
    }
    if (isset($_POST['email'])) {
        update_user_email($db, $user_login, $_POST['email']);
        $user['email'] = $_POST['email'];
    }
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $user['avatar_path'] = update_user_avatar($db, $user_login);
    }
}

$db->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль пользователя</title>
</head>
<body>
    <h1>Профиль пользователя</h1>
    <p><strong>Логин:</strong> <?php echo htmlspecialchars($user['login']); ?></p>
    
    <!-- Форма для редактирования профиля -->
    <form action="profile.php" method="post" enctype="multipart/form-data">
        <p><strong>Имя:</strong> <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"></p>
        <p><strong>Email:</strong> <input type="text" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"></p>
        <p><strong>Аватар:</strong> <input type="file" name="avatar" accept="image/jpeg, image/png, image/gif"></p>
        <input type="submit" name="submit" value="Сохранить изменения">
    </form>

    <!-- Отображение аватара пользователя -->
    <div class="avatar-wrapper">
        <?php if (!empty($user['avatar_path'])): ?>
            <img src="<?php echo htmlspecialchars($user['avatar_path']); ?>" alt="Аватар пользователя">
        <?php else: ?>
            <p>Аватар не загружен</p>
        <?php endif; ?>
    </div>

    <p><strong>Дата регистрации:</strong> <?php echo htmlspecialchars($user['registration_date']); ?></p>
    <button onclick="window.location.href='logout.php'">Выход</button>
</body>
</html>
