<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Форма авторизации и регистрации</title>
</head>
<body>

<?php 
    include 'header.php'; 

    if (!isset($_SESSION['login'])) {
        // Если пользователь не авторизован, показываем кнопку "Авторизация"
?>
        <a href="login.php" style="text-decoration: none;">
            <button style="padding: 10px 20px; font-size: 16px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">
                Авторизация
            </button>
        </a>
<?php
    } else {
        // Если пользователь авторизован, показываем кнопку "ИГРАТЬ" с добавленным скриптом
?>
       
        <button onclick="redirectToPlay()">Играть</button>
        

<?php
    }
?>

   <script>

    function redirectToPlay() {
        window.location.href = 'websocket_client.php'; 
    } 

   </script>


</body>
</html>
