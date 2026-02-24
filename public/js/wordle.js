document.addEventListener('turbo:load', () => {
    const grid = document.getElementById('wordle-grid');
    const message = document.getElementById('wordle-message');
    const hiddenInput = document.getElementById('wordle-hidden-input');

    let currentRow = 0;
    let gameOver = false;
    let currentGuess = "";

    // Load initial state
    loadGameState();

    async function loadGameState() {
        try {
            const response = await fetch('/api/wordle/estado');
            if (!response.ok) return;

            const state = await response.json();
            if (state.historial && state.historial.length > 0) {
                restoreGrid(state.historial);
                currentRow = state.historial.length;
            }
            if (state.completado) {
                gameOver = true;
                // Check if last attempt was a win
                const lastResult = state.historial[state.historial.length - 1];
                const isWin = lastResult.every(r => r.estado === 'correct');
                if (isWin) {
                    showMessage("Ya lo has clavado hoy 🎉", "text-success");
                } else {
                    showMessage("Mañana más... 💀", "text-muted");
                }
            }
        } catch (error) {
            console.error("Error loading Wordle state:", error);
        }
    }

    function restoreGrid(historial) {
        historial.forEach((guess, rowIndex) => {
            const row = grid.querySelector(`[data-row="${rowIndex}"]`);
            const tiles = row.querySelectorAll('.tile');
            guess.forEach((res, i) => {
                const tile = tiles[i];
                tile.textContent = res.letra;
                tile.classList.add(res.estado);
            });
        });
    }

    // Focus hidden input on click to allow mobile keyboard
    document.getElementById('wordle-sidebar').addEventListener('click', (e) => {
        if (gameOver) return;

        // Handle virtual keyboard clicks
        const keyBtn = e.target.closest('.kb-key');
        if (keyBtn) {
            const key = keyBtn.getAttribute('data-key');
            handleInput(key);
            return;
        }

        hiddenInput.focus();
    });

    hiddenInput.addEventListener('keydown', (e) => {
        if (gameOver) return;
        // Prevenir scroll si se pulsa espacio (aunque Wordle no suele usarlo)
        if (e.key === " ") e.preventDefault();
        handleInput(e.key);
    });

    function handleInput(key) {
        if (key === 'Enter') {
            submitGuess();
        } else if (key === 'Backspace') {
            deleteLetter();
        } else if (key.length === 1 && key.match(/[a-zñA-ZÑ]/)) {
            addLetter(key.toUpperCase());
        }
    }


    function addLetter(letter) {
        if (currentGuess.length < 5) {
            currentGuess += letter;
            updateGrid();
        }
    }

    function deleteLetter() {
        if (currentGuess.length > 0) {
            currentGuess = currentGuess.slice(0, -1);
            updateGrid();
        }
    }

    function updateGrid() {
        const row = grid.querySelector(`[data-row="${currentRow}"]`);
        const tiles = row.querySelectorAll('.tile');

        tiles.forEach((tile, index) => {
            tile.textContent = currentGuess[index] || "";
            if (index === currentGuess.length - 1 && currentGuess[index]) {
                tile.classList.add('active');
            } else {
                tile.classList.remove('active');
            }
        });
    }

    async function submitGuess() {
        if (currentGuess.length !== 5) {
            showMessage("Poco corto, ¿no?", "text-warning");
            return;
        }

        try {
            const response = await fetch('/api/wordle/comprobar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ intento: currentGuess })
            });

            const result = await response.json();

            if (!response.ok) {
                showMessage(result.error || "Error al comprobar", "text-danger");
                return;
            }

            revealTiles(result);
        } catch (error) {
            showMessage("Error de conexión", "text-danger");
        }
    }

    function revealTiles(result) {
        const row = grid.querySelector(`[data-row="${currentRow}"]`);
        const tiles = row.querySelectorAll('.tile');
        let correctCount = 0;

        result.forEach((res, i) => {
            const tile = tiles[i];
            setTimeout(() => {
                tile.classList.add('flip');
                tile.classList.add(res.estado);
                if (res.estado === 'correct') correctCount++;
            }, i * 100);
        });

        setTimeout(() => {
            if (correctCount === 5) {
                showMessage("¡Increíble! 🎉", "text-success");
                gameOver = true;
            } else {
                currentRow++;
                currentGuess = "";
                if (currentRow === 6) {
                    showMessage("Mañana será otro día... 💀", "text-muted");
                    gameOver = true;
                }
            }
        }, 600);
    }

    function showMessage(msg, className) {
        message.textContent = msg;
        message.className = `text-center small fw-bold mb-2 ${className}`;
        setTimeout(() => {
            if (!gameOver) message.textContent = "";
        }, 3000);
    }
});
