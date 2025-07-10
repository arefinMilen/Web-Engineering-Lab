let gameState = {
            board: Array(9).fill(''),
            currentPlayer: 'X',
            gameActive: false,
            player1Name: '',
            player2Name: '',
            scores: {
                player1: 0,
                player2: 0,
                ties: 0
            }
        };

        const winningCombinations = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // rows
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // columns
            [0, 4, 8], [2, 4, 6] // diagonals
        ];

        function startGame() {
            const player1Input = document.getElementById('player1').value.trim();
            const player2Input = document.getElementById('player2').value.trim();

            if (!player1Input || !player2Input) {
                alert('Please enter names for both players!');
                return;
            }

            gameState.player1Name = player1Input;
            gameState.player2Name = player2Input;
            gameState.gameActive = true;

            document.getElementById('playerSetup').classList.add('hidden');
            document.getElementById('gameArea').classList.remove('hidden');

            document.getElementById('player1Score').textContent = gameState.player1Name;
            document.getElementById('player2Score').textContent = gameState.player2Name;

            updateCurrentPlayer();
        }

        function makeMove(index) {
            if (!gameState.gameActive || gameState.board[index] !== '') {
                return;
            }

            gameState.board[index] = gameState.currentPlayer;
            const cell = document.querySelectorAll('.cell')[index];
            cell.textContent = gameState.currentPlayer;
            cell.classList.add(gameState.currentPlayer.toLowerCase());

            if (checkWinner()) {
                gameState.gameActive = false;
                const winner = gameState.currentPlayer === 'X' ? gameState.player1Name : gameState.player2Name;
                const loser = gameState.currentPlayer === 'X' ? gameState.player2Name : gameState.player1Name;
                
                if (gameState.currentPlayer === 'X') {
                    gameState.scores.player1++;
                } else {
                    gameState.scores.player2++;
                }
                
                updateScoreboard();
                showResult(`🎉 ${winner} Wins!`, `Congratulations ${winner}! You defeated ${loser}!`);
                return;
            }

            if (gameState.board.every(cell => cell !== '')) {
                gameState.gameActive = false;
                gameState.scores.ties++;
                updateScoreboard();
                showResult('🤝 It\'s a Tie!', `Both ${gameState.player1Name} and ${gameState.player2Name} played well!`);
                return;
            }

            gameState.currentPlayer = gameState.currentPlayer === 'X' ? 'O' : 'X';
            updateCurrentPlayer();
        }

        function checkWinner() {
            return winningCombinations.some(combination => {
                const [a, b, c] = combination;
                return gameState.board[a] && 
                       gameState.board[a] === gameState.board[b] && 
                       gameState.board[a] === gameState.board[c];
            });
        }

        function updateCurrentPlayer() {
            const currentPlayerElement = document.getElementById('currentPlayer');
            const playerName = gameState.currentPlayer === 'X' ? gameState.player1Name : gameState.player2Name;
            currentPlayerElement.textContent = `${playerName}'s Turn (${gameState.currentPlayer})`;
        }

        function updateScoreboard() {
            document.getElementById('player1Wins').textContent = gameState.scores.player1;
            document.getElementById('player2Wins').textContent = gameState.scores.player2;
            document.getElementById('ties').textContent = gameState.scores.ties;
        }

        function resetGame() {
            gameState.board = Array(9).fill('');
            gameState.currentPlayer = 'X';
            gameState.gameActive = true;

            const cells = document.querySelectorAll('.cell');
            cells.forEach(cell => {
                cell.textContent = '';
                cell.classList.remove('x', 'o');
            });

            updateCurrentPlayer();
        }

        function showResult(title, message) {
            document.getElementById('resultTitle').textContent = title;
            document.getElementById('resultMessage').textContent = message;
            document.getElementById('resultPopup').style.display = 'flex';
        }

        function closeResult() {
            document.getElementById('resultPopup').style.display = 'none';
            resetGame();
        }