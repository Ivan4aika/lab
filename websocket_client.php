<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Игра</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }

        .chat-container {
            width: 80%; /* Ширина контейнера чата */
            max-width: 600px; /* Максимальная ширина контейнера, чтобы не растягивать его слишком сильно */
            border: 1px solid #ccc; /* Рамка вокруг чата */
            padding: 20px;
            background-color: #fff;
            overflow-y: auto; /* Добавляем вертикальную прокрутку, если сообщений много */
            max-height: 400px; /* Максимальная высота контейнера чата */
            text-align: center; /* Выравниваем текст по центру */
            margin-bottom: 20px; /* Отступ снизу для формы */
        }

        .message {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: inline-block;
            max-width: 80%;
            word-wrap: break-word;
            margin-bottom: 10px;
        }

        .metxt {
            background-color: #DCF8C6; /* Цвет фона для отправленных сообщений */
        }

        .alltxt {
            background-color: #F7E5E6; /* Цвет фона для полученных сообщений */
        }

        .message.sender {
            background-color: #e6f7ff;
            text-align: right;
        }

        #game-search {
            text-align: center;
            margin-top: 20px;
        }

        #start-game-btn {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        #start-game-btn:hover {
            background-color: #45a049;
        }


    </style>
</head>
<body>

    <div class="chat-container">
        <h1>Чат</h1>
        <div id="messages"></div>
        <form id="message-form">
            <input type="text" id="message-input" placeholder="Введите сообщение">
            <button type="submit">Отправить</button>
        </form> 
    </div>

    <div id="game-search">
        <button id="start-game-btn">Начать поиск игры</button>
    </div>

    <button id="queue-status-btn">Показать состояние очереди</button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    

    <?php
    session_start();

    // Проверка авторизации пользователя
    if (!isset($_SESSION['login'])) {
        header('Location: login.php');
        exit;
    }

    $login = $_SESSION['login'];
    ?>
    
    <script>
        const login = "<?php echo $login; ?>";

        const wsUri = `wss://ivan4aika.ru/websocket?login=${login}`;

        const websocket = new WebSocket(wsUri);

        websocket.onopen = function(evt) {
            console.log("WebSocket opened");
        };

        websocket.onmessage = function(evt) {
    const messagesDiv = document.getElementById("messages");
    const messageObject = JSON.parse(evt.data); // Парсим JSON-строку в объект

    const messageParagraph = document.createElement('p');

    // Сконструируем текст сообщения с логином отправителя
    let messageText = messageObject.from + ': ' + messageObject.message;
    messageParagraph.textContent = messageText;

    if (messageObject.from === login) {
        messageParagraph.classList.add('message', 'sender');
    } else {
        messageParagraph.classList.add('message');
    }

    if (messageObject.to === login) {
        messageParagraph.classList.add('metxt'); // Добавляем класс для сообщений, адресованных текущему пользователю
    } else {
        messageParagraph.classList.add('alltxt'); // Добавляем класс для общих сообщений (если нужно)
    }

    messagesDiv.appendChild(messageParagraph);

    // Прокручиваем контейнер вниз, чтобы видеть последнее сообщение
    messagesDiv.scrollTop = messagesDiv.scrollHeight;

    // Обрабатываем новое значение 'extra'
    console.log('Extra argument:', messageObject.extra);

    if (messageObject.extra.startsWith('start game')) {
        const idgame = messageObject.extra.split(':')[1]; // Извлекаем idgame из extra
        const arg1 = login;  
        const arg2 = messageText;  
        var sizegame = 3;
        const words = decodeURIComponent(arg2).split(' ');
        const lastWord = words[words.length - 1];

        // Формируем URL с аргументами
        let idvar;

        $.ajax({
            url: 'newgame.php',
            type: 'GET',
            data: {
                p1: arg1,
                p2: lastWord
            },
            success: function(response) {
                // Парсим JSON ответ
                var result = JSON.parse(response);
                // Проверяем статус ответа
                if (result.status === 'success') {
                    // Выводим ID игры
                    idvar = result.id;
                    console.log('ID игры:', result.id);

                    const url = `/game_match.php?p1=${encodeURIComponent(arg1)}&p2=${encodeURIComponent(lastWord)}&idgame=${encodeURIComponent(idgame)}&sizegame=${encodeURIComponent(sizegame)}`;
                    // Переадресация на указанный URL
                    window.location.href = url;
                } else {
                    // Обработка других случаев (например, если статус 'error')
                    console.error(result.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(error); // Код для обработки ошибки
            }
        });
    }
};





        websocket.onclose = function(evt) {
            console.log("WebSocket closed");
        };

        websocket.onerror = function(evt) {
            console.error("WebSocket error:", evt);
        };

        document.getElementById("message-form").addEventListener("submit", function(event) {
            event.preventDefault();
            const messageInput = document.getElementById("message-input");
            const message = messageInput.value;

            
            sendMessage(message, login, 'all');

        });


        function checkQueueAndEnqueue(login) {  //поверка очереди
            fetch('/check_queue.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.inQueue) {
                    alert('Вы уже находитесь в очереди для начала игры.');
                } else {
                    addToQueue(login);
                }
            })
            .catch(error => {
                console.error('Ошибка при проверке очереди:', error);
                alert('Произошла ошибка при проверке очереди.');
            });
        }

        function addToQueue(login) { //добавление в очередь
            const requestData = {
                action: "start_game",
                login: login
            };

            fetch('/enqueue.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                getQueueStatus(login);
            })
            .catch(error => {
                console.error('Ошибка при добавлении в очередь:', error);
                alert('Произошла ошибка при добавлении в очередь.');
            });
        }

        function getQueueStatus(login) { //получение статуса очереди
            fetch('/queue_status.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(queueData => {
                displayQueueStatus(queueData,login);
                
            })
            .catch(error => {
                console.error('Ошибка при получении информации о состоянии очереди:', error);
                alert('Произошла ошибка при получении информации о состоянии очереди.');
            });
        }

        function displayQueueStatus(queueData,login) { //отображение статуса очереди
            const { playersCount, players } = queueData;
            let message = `В очереди находится ${playersCount} человек.`;

            if (playersCount > 1) {
                message += '\nЛогины игроков:';

                let player1 = players[0];
                let player2 = players[1];

                // Проверяем, что в массиве есть как минимум два игрока
                if (players.length >= 2) {
                    

                    message += `\nИгрок 1: ${player1}`;
                    message += `\nИгрок 2: ${player2}`;

                    
                }

                players.forEach((player, index) => {
                    if (index >= 2) { // Пропускаем первых двух игроков, так как они уже добавлены в player1 и player2
                        message += `\nИгрок ${index + 1}: ${player}`;
                    }
                });

                additionalLogic(login,player1,player2);
            }

            alert(message);
        }

        function additionalLogic(login, player1, player2) {
    let message2, message3, idgamePlayer1, idgamePlayer2;
    const loginadm = 'admin';

    // Randomly select idgame=0 for one player and idgame=1 for the other
    if (Math.random() < 0.5) {
        idgamePlayer1 = 0;
        idgamePlayer2 = 1;
    } else {
        idgamePlayer1 = 1;
        idgamePlayer2 = 0;
    }

    if (login == player1) {
        message2 = 'Вам нашло игру ваш противник: ' + player2;
        sendMessage(message2, loginadm, player1, `start game:${idgamePlayer1}`);
        message3 = 'Вам нашло игру ваш противник: ' + player1;
        sendMessage(message3, loginadm, player2, `start game:${idgamePlayer2}`);
    } else if (login == player2) {
        message2 = 'Вам нашло игру ваш противник: ' + player1;
        sendMessage(message2, loginadm, player2, `start game:${idgamePlayer2}`);
        message3 = 'Вам нашло игру ваш противник: ' + player2;
        sendMessage(message3, loginadm, player1, `start game:${idgamePlayer1}`);
    } else {
        console.log('Не удалось определить противника для игры.');
        return;
    }
}



        document.getElementById("start-game-btn").addEventListener("click", function() {
        const login = '<?php echo $login; ?>'; // Получаем логин пользователя из PHP

        checkQueueAndEnqueue(login);
    });










        document.getElementById("queue-status-btn").addEventListener("click", function() {
            fetch('/queue_status.php') // Поменяйте путь на соответствующий, где будет обработчик для получения состояния очереди
            .then(response => response.json())
            .then(data => {
                console.log('Состояние очереди:', data); // Выводим данные в консоль
            })
            .catch(error => {
                console.error('Ошибка получения состояния очереди:', error);
                alert('Произошла ошибка при получении состояния очереди.');
            });
        });

        // Функция начала игры (вы можете реализовать её логику)
        function startGame() {
            // Добавьте здесь логику для начала игры
            console.log('Игра началась!');
            // Пример: переход на страницу игры или другие действия
            // window.location.href = '/game.php';
        }

        $(window).on('beforeunload', function() {
    // Отправляем AJAX-запрос на сервер для вызова delquie.php
    $.ajax({
        url: 'delquie.php',
        type: 'POST',
        contentType: 'application/json',
        success: function(response) {
            console.log('Запрос delquie.php выполнен успешно');
        },
        error: function(xhr, status, error) {
            console.error('Произошла ошибка при выполнении запроса delquie.php:', error);
        }
    });
});


function sendMessage(message, from, to, extra = '') {
    const data = {
        message: message,
        from: from,
        to: to,
        extra: extra
    };

    websocket.send(JSON.stringify(data));
}






    </script>
</body>
</html>

