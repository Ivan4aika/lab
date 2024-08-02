<?php
// Подключение к базе данных
include 'db.php';
include 'header.php';
// Начало сессии
echo $_FILES['avatar']['size'];

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['login'])) {
    // Если пользователь не авторизован, перенаправляем на страницу входа
    header('Location: login.php');
    exit();
}

// Получение логина текущего пользователя из сессии
$user_login = $_SESSION['login'];

// Извлечение данных пользователя из базы данных
$query = "SELECT login, name, email, registration_date, avatar_path FROM users WHERE login = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $user_login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Получение данных пользователя
    $user = $result->fetch_assoc();
} else {
    echo "Ошибка: Пользователь не найден.";
    exit();
}

$stmt->close();

// Обработка данных из формы, если она была отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Обработка изменения имени
    if (isset($_POST['name'])) {
        $new_name = $_POST['name'];
        $update_query = "UPDATE users SET name = ? WHERE login = ?";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bind_param("ss", $new_name, $user_login);
        if ($update_stmt->execute()) {
            $user['name'] = $new_name; // Обновляем имя в массиве $user для отображения обновленных данных на странице
        } else {
            echo "Ошибка при обновлении имени: " . $update_stmt->error;
        }
        $update_stmt->close();
    }

    // Обработка изменения email
    if (isset($_POST['email'])) {
        $new_email = $_POST['email'];
        $update_query = "UPDATE users SET email = ? WHERE login = ?";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bind_param("ss", $new_email, $user_login);
        if ($update_stmt->execute()) {
            $user['email'] = $new_email; // Обновляем email в массиве $user для отображения обновленных данных на странице
        } else {
            echo "Ошибка при обновлении email: " . $update_stmt->error;
        }
        $update_stmt->close();
    }

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatar_tmp_name = $_FILES['avatar']['tmp_name'];
        $avatar_name = $_FILES['avatar']['name'];
        $avatar_size = $_FILES['avatar']['size']; // Размер файла в байтах
    
        // Проверка MIME типа загруженного файла
        $allowed_mime_types = array('image/jpeg', 'image/png', 'image/gif');
        if (!in_array($_FILES['avatar']['type'], $allowed_mime_types)) {
            echo "Ошибка: Недопустимый тип файла. Допустимые типы: JPEG, PNG, GIF.";
            exit();
        }
    
        // Проверка размера загруженного файла
        $max_file_size = 1000000; // 1 MB
        if (filesize($avatar_name) > $max_file_size) {
            echo "Ошибка: Размер файла превышает допустимый лимит (10 MB).";
            exit();
        }
    
        $avatar_path = 'avatars/' . $avatar_name; // Путь, куда сохранить файл на сервере
    
        if (move_uploaded_file($avatar_tmp_name, $avatar_path)) {
            // Обновляем путь к аватару в базе данных
            $update_query = "UPDATE `users` SET `avatar_path` = ? WHERE login = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bind_param("ss", $avatar_path, $user_login);
            if ($update_stmt->execute()) {
                $user['avatar_path'] = $avatar_path; // Обновляем путь к аватару в массиве $user для отображения обновленных данных на странице
                echo "Аватар успешно загружен и сохранен.";
            } else {
                echo "Ошибка при сохранении пути к аватару: " . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            echo "Ошибка при загрузке аватара на сервер.";
        }
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
    <p><strong>Имя:</strong> <input style="width:200px;" type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"></p>
    <p><strong>Email:</strong> <input style="width:200px;" type="text" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"></p>
    <p><strong>Аватар:</strong> <input type="file" name="avatar" id = "avatar" accept="image/jpeg, image/png, image/gif" maxlength="1M" onchange="check_wedth(this)"></p>
    <script>
   function check_wedth(file) {
    const selectedFile = document.getElementById("avatar").files[0];

if(selectedFile['size']> 1024 * 1024){
let input = document.getElementById('avatar');
    input.value = '';

alert("Выбранная аватарка больше 1 мб выбери дугую");
console.log(selectedFile);
}
};
</script>
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
