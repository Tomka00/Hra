<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tetris</title>
    <style>
        body {
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .game-board {
            width: 200px;
            height: 400px;
            border: 1px solid black;
            position: relative;
            background-color: #fff;
        }
        .block {
            width: 20px;
            height: 20px;
            background-color: #ccc;
            position: absolute;
        }
    </style>
</head>
<body>
    <h1>Tetris</h1>

    <div class="game-board" id="game-board"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const gameBoard = document.getElementById('game-board');
            const boardWidth = 10;
            const boardHeight = 20;
            const blockSize = 20;
            let board = [];
            let currentPiece = {x: 0, y: 0, shape: [[1,1],[1,1]]}; // Square shape as default
            let intervalId;

            // Initialize the game board
            for (let i = 0; i < boardHeight; i++) {
                board.push(Array(boardWidth).fill(0));
            }

            // Draw the game board
            function drawBoard() {
                gameBoard.innerHTML = '';
                for (let y = 0; y < boardHeight; y++) {
                    for (let x = 0; x < boardWidth; x++) {
                        if (board[y][x] === 1) {
                            const block = document.createElement('div');
                            block.classList.add('block');
                            block.style.left = x * blockSize + 'px';
                            block.style.top = y * blockSize + 'px';
                            gameBoard.appendChild(block);
                        }
                    }
                }
            }

            // Move the current piece down
            function moveDown() {
                currentPiece.y++;
                if (checkCollision()) {
                    currentPiece.y--;
                    placePiece();
                    clearLines();
                    currentPiece = randomPiece();
                }
                draw();
            }

            // Move the current piece left
            function moveLeft() {
                currentPiece.x--;
                if (checkCollision()) {
                    currentPiece.x++;
                }
                draw();
            }

            // Move the current piece right
            function moveRight() {
                currentPiece.x++;
                if (checkCollision()) {
                    currentPiece.x--;
                }
                draw();
            }

            // Rotate the current piece
            function rotatePiece() {
                const rotatedPiece = [];
                for (let x = 0; x < currentPiece.shape[0].length; x++) {
                    const newRow = [];
                    for (let y = currentPiece.shape.length - 1; y >= 0; y--) {
                        newRow.push(currentPiece.shape[y][x]);
                    }
                    rotatedPiece.push(newRow);
                }
                currentPiece.shape = rotatedPiece;
                if (checkCollision()) {
                    currentPiece.shape = rotateArray(rotatedPiece);
                }
                draw();
            }

            // Rotate a 2D array
            function rotateArray(array) {
                return array[0].map((_, colIndex) => array.map(row => row[colIndex])).reverse();
            }

            // Check collision with the bottom of the board or existing blocks
            function checkCollision() {
                for (let y = 0; y < currentPiece.shape.length; y++) {
                    for (let x = 0; x < currentPiece.shape[y].length; x++) {
                        if (currentPiece.shape[y][x] === 1) {
                            const boardX = currentPiece.x + x;
                            const boardY = currentPiece.y + y;
                            if (boardY >= boardHeight || boardX < 0 || boardX >= boardWidth || board[boardY][boardX] === 1) {
                                return true;
                            }
                        }
                    }
                }
                return false;
            }

            // Place the current piece on the board
            function placePiece() {
                for (let y = 0; y < currentPiece.shape.length; y++) {
                    for (let x = 0; x < currentPiece.shape[y].length; x++) {
                        if (currentPiece.shape[y][x] === 1) {
                            const boardX = currentPiece.x + x;
                            const boardY = currentPiece.y + y;
                            board[boardY][boardX] = 1;
                        }
                    }
                }
            }

            // Clear full lines
            function clearLines() {
                for (let y = boardHeight - 1; y >= 0; y--) {
                    let isLineFull = true;
                    for (let x = 0; x < boardWidth; x++) {
                        if (board[y][x] !== 1) {
                            isLineFull = false;
                            break;
                        }
                    }
                    if (isLineFull) {
                        // Remove the line
                        board.splice(y, 1);
                        // Add new empty line at the top
                        board.unshift(Array(boardWidth).fill(0));
                    }
                }
            }

            // Generate a random Tetris piece
            function randomPiece() {
                const shapes = [
                    [[1,1],[1,1]], // Square
                    [[1,1,1,1]],   // Line
                    [[1,1,0],[0,1,1]], // Z
                    [[0,1,1],[1,1,0]], // S
                    [[1,0],[1,0],[1,1]], // L
                    [[0,1],[0,1],[1,1]], // J
                    [[1,1,1],[0,1,0]] // T
                ];
                return {x: Math.floor(Math.random() * (boardWidth - 1)), y: 0, shape: shapes[Math.floor(Math.random() * shapes.length)]};
            }

            // Draw the current piece on the board
            function draw() {
                drawBoard();
                for (let y = 0; y < currentPiece.shape.length; y++) {
                    for (let x = 0; x < currentPiece.shape[y].length; x++) {
                        if (currentPiece.shape[y][x] === 1) {
                            const block = document.createElement('div');
                            block.classList.add('block');
                            block.style.left = (currentPiece.x + x) * blockSize + 'px';
                            block.style.top = (currentPiece.y + y) * blockSize + 'px';
                            gameBoard.appendChild(block);
                        }
                    }
                }
            }

            // Start the game loop
            function startGame() {
                intervalId = setInterval(moveDown, 500); // Move piece down every 500ms
                draw();
            }

            // Event listeners for keyboard controls
            document.addEventListener('keydown', (event) => {
                switch (event.key) {
                    case 'ArrowLeft':
                        moveLeft();
                        break;
                    case 'ArrowRight':
                        moveRight();
                        break;
                    case 'ArrowDown':
                        moveDown();
                        break;
                    case 'ArrowUp':
                        rotatePiece();
                        break;
                }
            });

            startGame();
        });
    </script>
</body>
</html>
