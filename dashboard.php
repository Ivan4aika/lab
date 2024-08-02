<?php
// Проверка сессии для защищенного доступа
session_start();
if (!isset($_SESSION['login'])) {
    // Если сессия не установлена, перенаправляем на страницу авторизации
    header("Location: login.php");
    exit();
}

// Ваш код защищенной страницы здесь
?>

<!DOCTYPE html>
<html>
<head>
    <title>Защищенная страница</title>
</head>
<body>
    <h2>Добро пожаловать, <?php echo $_SESSION['login']; ?>!</h2>
    <p>Это защищенная страница.</p>
    <a href="logout.php">Выйти</a> <!-- Ссылка для выхода из учетной записи -->
</body>
</html>
