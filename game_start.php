<?php


 include 'db.php';

 include 'header.php';


if (!isset($_SESSION['login'])) {
    // Редирект на страницу авторизации 
    header('Location: login.php');
    exit;
}


$otherPlayersExist = true; 

if ($otherPlayersExist) {
    // Если другие игроки найдены, редиректим всех на страницу игры
    header('Location: game_room.php');
    exit;
} else {
    // Если других игроков нет, можно показать сообщение или другое действие
    echo "Пока нет других игроков. Ожидайте...";
    
}
?>
