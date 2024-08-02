<?php
include 'db.php'; // Подключение к базе данных
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="styles.css">
<?php
session_start(); // Начинаем сессию

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['login'])) {
    echo '<script>console.log("Пользователь авторизован");</script>';
    echo '<button onclick="logout()">Выход</button>';
    echo '<button onclick="redirectToProfile()">Профиль</button>';
} else {
    echo '<script>console.log("Пользователь не авторизован");</script>';
}
?>

<script>
    // Вынесем функцию logout() в глобальную область видимости
    function logout() {
        $.ajax({
            type: 'POST',
            url: 'logout.php', // Файл для обработки выхода (логаута)
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.status === 'success') {
                    alert('Вы успешно вышли из системы.');
                    window.location.href = 'index.php'; // Перенаправляем на страницу авторизации после выхода
                } else {
                    alert('Ошибка выхода из системы: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Ошибка при выполнении запроса:', status, error);
                alert('Произошла ошибка при отправке запроса на сервер.');
            }
        });
    }

    function redirectToProfile() {
        window.location.href = 'profile.php'; // Замените 'profile.php' на URL вашей страницы профиля
    }    
   
</script>
