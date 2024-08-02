<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Форма авторизации и регистрации</title>

</head>
<body>

    <?php include 'header.php'; ?>

    <h2>Авторизация</h2>
    <div>
        <form id="loginForm">
            <label for="login">Логин:</label><br>
            <input type="text" id="login" name="login" required><br>
            <label for="password">Пароль:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" value="Войти">
        </form>
    </div>

    <h2>Регистрация</h2>
    <div>
        <form id="registerForm">
            <label for="registerLogin">Логин:</label><br>
            <input type="text" id="registerLogin" name="login" required><br>
            <label for="registerPassword">Пароль:</label><br>
            <input type="password" id="registerPassword" name="password" required><br><br>
            <input type="submit" value="Зарегистрироваться">
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(event) {
                event.preventDefault(); // Предотвращаем стандартное поведение отправки формы
                
                let login = $('#login').val();
                let password = $('#password').val();
                
                $.ajax({
                    type: 'POST',
                    url: 'auth.php',
                    data: {
                        login: login,
                        password: password
                    },
                    dataType: 'json', // Ожидаемый тип данных от сервера
                    success: function(response) {
                        console.log(response); // Выводим ответ сервера в консоль
                        
                        if (response.status === 'success') {
                            alert('Успешная авторизация!'); // Пример обработки успешного ответа
                            window.location.href = 'index.php';
                            // Здесь можно перенаправить пользователя на другую страницу или выполнить другие действия
                        } else {
                            alert('Ошибка авторизации: ' + response.message); // Выводим сообщение об ошибке
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ошибка при выполнении запроса:', status, error);
                        alert('Произошла ошибка при отправке запроса на сервер.'); // Обработка ошибки AJAX запроса
                    }
                });
            });

            $('#registerForm').submit(function(event) {
                event.preventDefault();
                let registerLogin = $('#registerLogin').val();
                let registerPassword = $('#registerPassword').val();

                // AJAX запрос для регистрации
                $.ajax({
                    type: 'POST',
                    url: 'register.php', // Файл для обработки регистрации
                    data: {
                        login: registerLogin,
                        password: registerPassword
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response.status === 'success') {
                            alert('Регистрация успешна!');
                            window.location.href = 'index.php';
                            // Опционально: автоматическая авторизация после регистрации
                            // Можно вызвать функцию для авторизации здесь
                        } else {
                            alert('Ошибка регистрации: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ошибка при выполнении запроса:', status, error);
                        alert('Произошла ошибка при отправке запроса на сервер.');
                    }
                });
            });
        });
    </script>

</body>
</html>
