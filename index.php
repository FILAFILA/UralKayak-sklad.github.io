<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drag and Drop с LocalStorage</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            padding: 40px;
            background-color: #f4f4f9;
        }

        .container {
            display: flex;
            gap: 20px;
            justify-content: center;
            width: 100%;
        }

        .board {
            width: 300px;
            min-height: 400px;
            background: #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            border: 2px dashed #cbd5e1;
        }

        .board h3 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #334155;
        }

        .item {
            background: white;
            border-radius: 6px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: grab;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            user-select: none;
        }

        .item:active {
            cursor: grabbing;
        }

        .item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 8px;
            pointer-events: none;
        }

        .board.hovered {
            background: #cbd5e1;
            border-color: #64748b;
        }

        .item.dragging {
            opacity: 0.4;
        }

        .btn-reset {
            padding: 10px 20px;
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-reset:hover {
            background-color: #dc2626;
        }
    </style>
</head>
<body>

    <button class="btn-reset" id="resetBtn">Сбросить положение карточек</button>

    <div class="container">

		<!-- Первый контейнер -->
		<div class="board" id="board1">
			<h3>Склад Смолино</h3>
			<div class="item" draggable="true">
				<img src="https://cdn.paddlesports.capacitysports.com.au/assets/images/Braap-69-Bluegrass.jpg" alt="liquidlogic braaap (Виталя)">
				<span>liquidlogic braaap (Виталя)</span>
			</div>
			<div class="item" draggable="true">
				<img src="https://kayaker.ru/upload/iblock/bd0/bd0aeae6a6b15126e49fcc9fc8ef69c6.jpg" alt="liquidlogic alpha (Виталя)">
				<span>liquidlogic alpha (Виталя)</span>
			</div>
		</div>

		<!-- Второй контейнер -->
		<div class="board" id="board2">
			<h3>Гараж Витали</h3>
			<div class="item" draggable="true">
				<img src="https://kayaker.ru/upload/iblock/0a8/0a82af0666321cd108403f2bac21c020.jpg" alt="Картинка 1">
				<span>карма</span>
			</div>
			<div class="item" draggable="true">
				<img src="https://paddling.com/imager/productimages/Liquidlogic/403241/Mullet-SB_5c5ff4e405ba15020d2f3dfff5a4017c.png" alt="Картинка 2">
				<span>муллет</span>
			</div>

		</div>

		<!-- Третий контейнер -->
		<div class="board" id="board3">
			<h3>Гараж Данила</h3>
			<div class="item" draggable="true">
				<img src="https://www.wavesport.com/sites/default/files/styles/full_size/public/2026-10/DIESEL_70_WhiteOut_Cherry-Bomb.png?itok=L5GUpEbV" alt="Diesel 70 (Алевтина)">
				<span>Diesel 70 (Алевтина)</span>
			</div>
			<div class="item" draggable="true">
				<img src="https://www.kayak-travel.ru/wp-content/uploads/2021/04/diesel75.jpg" alt="Diesel 80 (Виталя)">
				<span>Diesel 80 (Виталя)</span>
			</div>
			<div class="item" draggable="true">
				<img src="https://img.nauticexpo.ru/images_ne/photo-g/22063-9235259.jpg" alt="Картинка 2">
				<span>Remix69 (Данил)</span>
			</div>

		</div>

		<!-- четвёртый контейнер -->
		<div class="board" id="board4">
			<h3>Гараж Алексея</h3>
			<div class="item" draggable="true">
				<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTwU7_DmDBsSLQYpPww_JYddUKhjUnV5ujuwdqiyM-4S5yM-JCb7y965rs&s=10" alt="Картинка 2">
				<span>Френки (Виталя)</span>
			</div>
		</div>


    </div>

    <script>
        const items = document.querySelectorAll('.item');
        const boards = document.querySelectorAll('.board');
        const resetBtn = document.getElementById('resetBtn');

        let draggedItem = null;

        // --- ЛОГИКА DRAG AND DROP ---

        items.forEach(item => {
            item.addEventListener('dragstart', function () {
                draggedItem = this;
                setTimeout(() => this.classList.add('dragging'), 0);
            });

            item.addEventListener('dragend', function () {
                this.classList.remove('dragging');
                draggedItem = null;
            });
        });

        boards.forEach(board => {
            board.addEventListener('dragover', function (e) {
                e.preventDefault();
            });

            board.addEventListener('dragenter', function (e) {
                e.preventDefault();
                this.classList.add('hovered');
            });

            board.addEventListener('dragleave', function () {
                this.classList.remove('hovered');
            });

            board.addEventListener('drop', function () {
                this.classList.remove('hovered');
                if (draggedItem) {
                    this.appendChild(draggedItem);
                    saveState(); // Сохраняем состояние после каждого успешного переноса
                }
            });
        });

        // --- ЛОГИКА LOCALSTORAGE ---

        // Функция сохранения состояния
        function saveState() {
            const state = {};

            boards.forEach(board => {
                // Находим id всех карточек внутри текущего контейнера
                const itemIds = Array.from(board.querySelectorAll('.item')).map(item => item.id);
                state[board.id] = itemIds;
            });

            // Записываем объект в LocalStorage, превратив его в строку
            localStorage.setItem('dragAndDropState', JSON.stringify(state));
        }

        // Функция восстановления состояния
        function restoreState() {
            const savedData = localStorage.getItem('dragAndDropState');
            
            if (!savedData) return; // Если в памяти ничего нет, выходим

            const state = JSON.parse(savedData);

            // Проходимся по сохраненным контейнерам
            Object.keys(state).forEach(boardId => {
                const board = document.getElementById(boardId);
                const itemIds = state[boardId];

                if (board) {
                    // Перемещаем карточки в нужный контейнер согласно их сохраненному порядку
                    itemIds.forEach(itemId => {
                        const item = document.getElementById(itemId);
                        if (item) {
                            board.appendChild(item);
                        }
                    });
                }
            });
        }

        // Сброс памяти по нажатию кнопки
        resetBtn.addEventListener('click', () => {
            localStorage.removeItem('dragAndDropState');
            window.location.reload(); // Перезагружаем страницу для возврата к исходному HTML
        });

        // Вызываем восстановление при каждой загрузке страницы
        document.addEventListener('DOMContentLoaded', restoreState);
    </script>

</body>
</html>
