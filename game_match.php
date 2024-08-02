<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Крестики-нолики</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }

        .container {
            display: flex;
            max-width: 2000px;
            margin: 20px auto;
            flex-direction: column;
            align-items: center;
        }

        .chat-container {
            width: 80%;
            max-width: 600px;
            border: 1px solid #ccc;
            padding: 20px;
            background-color: #fff;
            overflow-y: auto;
            max-height: 400px;
            text-align: center;
            margin-bottom: 20px;
        }

        .tic-tac-toe {
            display: grid;
            gap: 0;
            border: 3px solid #333;
            background-color: #fff;
        }

        .cell {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2em;
            cursor: pointer;
            border: 1px solid #333;
            transition: background-color 0.3s ease;
        }

        .cell.unfilled {
            background-color: #fff;
        }

        .cell.o {
            background-color: #F24;
        }

        .cell.x {
            background-color: #90ee90;
        }

        .game_bar {
            display: flex;
            gap: 20px;
        }

        .controls {
            margin-bottom: 0px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 id='oponent'>xxx</h1>
        <h1 id='init'>xxx</h1>

        <div class="game_bar">
            <div class="tic-tac-toe" id="grid-container"></div>

            <div class="chat-container">
                <h1>Чат</h1>
                <div id="messages"></div>
                <form id="message-form">
                    <input type="text" id="message-input" placeholder="Введите сообщение">
                    <button type="submit">Отправить</button>
                </form>
            </div>
        </div>
    </div>

    <script>
       document.addEventListener('DOMContentLoaded', () => {
    const gridContainer = document.getElementById('grid-container');
    let currentPlayer = 'X'; // Определяем текущего игрока
    let myTurn = true; // Определяем очередь текущего игрока

    const createGrid = (size) => {
        gameinfo = '1' + size + size;

        for (let i = 1; i <= size * size; i++) {
            gameinfo += '2';
        }

        gridContainer.innerHTML = '';

        gridContainer.style.gridTemplateColumns = `repeat(${size}, 100px)`;
        gridContainer.style.gridTemplateRows = `repeat(${size}, 100px)`;

        for (let i = 0; i < size * size; i++) {
            const cell = document.createElement('div');
            cell.classList.add('cell', 'unfilled');
            cell.id = `cell-${i}`;
            cell.addEventListener('click', () => {
                if (cell.classList.contains('unfilled') && myTurn) {
                    updateCellColor(cell, currentPlayer);
                    currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
                    myTurn = false; // Блокируем ход текущему игроку
                    // Обновление gameinfo при заполнении ячейки
                    updateGameInfo(i, currentPlayer === 'X' ? 'O' : 'X');
                    sendMessage(i, p1, p2, 'new move'); // отправляем индекс ячейки
                }
            });
            gridContainer.appendChild(cell);
        }
    };

    function updateGameInfo(cellIndex, symbol) { // обновление ячейки при нажатии
        let newIndex = cellIndex + 3;
        if (newIndex < gameinfo.length) {
            gameinfo = gameinfo.substring(0, newIndex) + symbol + gameinfo.substring(newIndex + 1);
            
        }

        let sizewin = 3;
        const result = checkWin(gameinfo, newIndex - 3, sizewin);
        

        if (result.result === 'победа') {
            alert('Победа! Выигрышная комбинация: ' + result.cause);
        }
    }

    let login = "<?php echo $login; ?>";
    let p1 = "<?php echo htmlspecialchars($_GET['p1']); ?>";
    login = p1;
    let p2 = "<?php echo htmlspecialchars($_GET['p2']); ?>";
    document.getElementById("oponent").textContent = 'Ваш противник ' + p2;
    let init = "<?php echo htmlspecialchars($_GET['idgame']); ?>";

    if (init == '0') {
        document.getElementById("init").textContent = 'Вы играете за нолики';
        currentPlayer = 'O'; // Устанавливаем текущего игрока в 'O', если он играет за нолики
        myTurn = false; // Если игрок за нолики, его ход не первый
    } else {
        document.getElementById("init").textContent = 'Вы играете за крестики';
        currentPlayer = 'X'; // Устанавливаем текущего игрока в 'X', если он играет за крестики
    }

            console.log(init);

            let sizegame = "<?php echo htmlspecialchars($_GET['sizegame']); ?>";
            createGrid(sizegame);

            // ВЕБ СОКЕТ----------------------------------------------------------------------------
            let wsUri = `wss://ivan4aika.ru/websocket?login=${login}`;
    let websocket = new WebSocket(wsUri);

    websocket.onopen = function(evt) {
        console.log("WebSocket opened");
    };

    websocket.onmessage = function(evt) {
        let messagesDiv = document.getElementById("messages");
        let messageObject = JSON.parse(evt.data);

        console.log(p1);
        console.log(p2);

        if ((messageObject.from === p2) || (messageObject.from === p1 && messageObject.sender === p1)) {
            let messageParagraph = document.createElement('p');

            if (messageObject.extra == 'new move') {
                let cellIndex = parseInt(messageObject.message);
                let cell = document.getElementById(`cell-${cellIndex}`);
                if (cell) {
                    updateCellColor(cell, currentPlayer);
                    currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
                }
                myTurn = true; // Разрешаем ход текущему игроку
                updateGameInfo(cellIndex, currentPlayer === 'X' ? 'O' : 'X'); // Обновление gameinfo и проверка на победу
            } else {
                messageParagraph.textContent = messageObject.from + ': ' + messageObject.message;
                messageParagraph.classList.add('message', 'sender');
                messagesDiv.appendChild(messageParagraph);
            }
        } else {
            return;
        }

        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    };

    function sendMessage(message, from, to, extra = '') {
        let data = {
            message: message,
            from: from,
            to: to,
            extra: extra
        };

        websocket.send(JSON.stringify(data));
    }

    document.getElementById("message-form").addEventListener("submit", function(event) {
        event.preventDefault();
        let messageInput = document.getElementById("message-input");
        let message = messageInput.value;
        sendMessage(message, p1, p2);
        sendMessage(message, p1, p1);
    });
        });

        function checkWin(gameinfo, nevchels, sizewin) {

            console.log(gameinfo);

            var init = gameinfo.charAt(0); // символ, которым играет текущий игрок

            var sizeStr = gameinfo.substring(1, 3); // размер поля (длина и ширина одинаковы)
            var size = parseInt(sizeStr, 10); // размер поля (преобразование из строки в число)

            var sizewin = sizewin; // количество подряд идущих символов для победы

            // Оставшаяся часть строки gameinfo - информация о состоянии ячеек
            var gameState = gameinfo.substring(3);

            // Преобразование строки gameState в двумерный массив matrix
            var matrix = [];

            console.log(size);

            for (var i = 0; i < size; i++) {
                matrix[i] = [];
                for (var j = 0; j < size; j++) {
                    matrix[i][j] = gameState.charAt(i * size + j);
                }
            }

            var x = Math.floor(nevchels / size);
            var y = nevchels % size;


            

            console.log(matrix);
            console.log('строка:' + x + ' столбец:' + y);

            // Функция для проверки линии
            function checkLine(startX, startY, dirX, dirY) {
                var maxsize = 0;
                var cells = [];

                // Проверяем в направлении (dirX, dirY)
                var currentX = startX;
                var currentY = startY;

                // Проверка в одну сторону (право, низ, или диагональ)
                while (currentX >= 0 && currentX < size && currentY >= 0 && currentY < size) {
                    if (matrix[currentX][currentY] === init) {
                        cells.push(currentX * size + currentY); // Преобразование координат в номер ячейки
                        maxsize++;
                        if (maxsize === sizewin) return { win: true, cells: cells };
                    } else {
                        break;
                    }
                    currentX += dirX;
                    currentY += dirY;
                }

                // Проверяем в другую сторону (лево, верх, или диагональ)
                currentX = startX - dirX;
                currentY = startY - dirY;
                while (currentX >= 0 && currentX < size && currentY >= 0 && currentY < size) {
                    if (matrix[currentX][currentY] === init) {
                        cells.push(currentX * size + currentY); // Преобразование координат в номер ячейки
                        maxsize++;
                        if (maxsize === sizewin) return { win: true, cells: cells };
                    } else {
                        break;
                    }
                    currentX -= dirX;
                    currentY -= dirY;
                }

                return { win: false, cells: [] };
            }

            // Проверка всех направлений
            var directions = [
                { name: 'горизонтально', startX: x, startY: y, dirX: 0, dirY: 1 },
                { name: 'вертикально', startX: x, startY: y, dirX: 1, dirY: 0 },
                { name: 'диагональ из верхнего левого в нижний правый', startX: x, startY: y, dirX: 1, dirY: 1 },
                { name: 'диагональ из верхнего правого в нижний левый', startX: x, startY: y, dirX: 1, dirY: -1 },
            ];

            for (var i = 0; i < directions.length; i++) {
                var direction = directions[i];
                var result = checkLine(direction.startX, direction.startY, direction.dirX, direction.dirY);
                if (result.win) {
                    console.log('Победа:', direction.name);
                    console.log('Победные ячейки:', result.cells);
                    return { result: 'победа', cause: direction.name, winningCells: result.cells };
                }
            }

            console.log('Нет победы');
            return { result: 'нет победы', cause: '', winningCells: [] };
        }

        function updateCellColor(cell, symbol) {
            cell.classList.remove('unfilled');
            if (symbol === 'X') {
                cell.classList.add('x');
            } else {
                cell.classList.add('o');
            }
        }
    </script>

    <?php
        session_start();
        if (!isset($_SESSION['login'])) {
            header('Location: login.php');
            exit;
        }
        if (!isset($_GET['p1']) || !isset($_GET['p2']) || !isset($_GET['idgame'])) {
            header('Location: index.php');
            exit;
        }
        $login = $_SESSION['login'];
    ?>
</body>
</html>




