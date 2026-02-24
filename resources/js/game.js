export function posision(curentstatus,leagalmove,board) {
    for (let i = 0; i < 9; i++) {
        for(let j = 0; j < 9 ;j ++){
            let element = document.getElementById(`S${i}${j}`);
            
            if( element!= null && element.innerHTML != curentstatus[`S${i}`][j]){
                element.innerHTML = curentstatus[`S${i}`][j];
            }
        }
        winning(i,board[`S${i}`])
    }
    
    // Update board playability
    updateBoardPlayability(leagalmove);
}

function updateBoardPlayability(leagalmove) {
    if(leagalmove != 9){
        for (let i = 0; i < 9; i++) {
            const board = document.getElementById(`item${i}`);
            if (i != leagalmove) {
                // Mark as non-playable
                setNonPlayableBoard(board);
            } else {
                // Mark as playable
                setPlayableBoard(board);
            }
        }
    } else {
        // All boards are playable
        for (let i = 0; i < 9; i++) {
            const board = document.getElementById(`item${i}`);
            setPlayableBoard(board);
        }
    }
}

function setNonPlayableBoard(board) {
    // Visual indicators for non-playable boards
    board.style.background = 'rgba(240, 255, 255, 0.15)';
    board.style.opacity = '0.6';
    board.style.transform = 'scale(0.95)';
    board.style.filter = 'grayscale(0.3)';
    board.classList.add('non-playable');
    
    // Disable interactions on cells
    const cells = board.querySelectorAll('.main2');
    cells.forEach(cell => {
        if (!cell.closest('.winner-display')) {
            cell.style.pointerEvents = 'none';
            cell.style.opacity = '0.5';
        }
    });
}

function setPlayableBoard(board) {
    // Visual indicators for playable boards
    board.style.background = '';
    board.style.opacity = '1';
    board.style.transform = 'scale(1)';
    board.style.filter = 'none';
    board.classList.remove('non-playable');
    
    // Enable interactions on cells (unless board is won)
    if (!board.classList.contains('winner')) {
        const cells = board.querySelectorAll('.main2');
        cells.forEach(cell => {
            cell.style.pointerEvents = 'auto';
            cell.style.opacity = '1';
        });
    }
}

function winning(index, A) {
    const mainBoard = document.getElementById(`item${index}`);
    
    if (A != null && A !== '') {
        // Create winner overlay instead of replacing innerHTML
        mainBoard.classList.add('winner');
        
        // Create or update winner display
        let winnerDisplay = mainBoard.querySelector('.winner-display');
        if (!winnerDisplay) {
            winnerDisplay = document.createElement('div');
            winnerDisplay.className = 'winner-display';
            winnerDisplay.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: clamp(60px, 10vw, 120px);
                font-weight: 600;
                color: rgba(255, 255, 255, 0.9);
                background: rgba(60, 60, 60, 1);
                border-radius: 8px;
                z-index: 100;
            `;
            mainBoard.appendChild(winnerDisplay);
        }
        winnerDisplay.textContent = A;
        
        // Hide all main2 cells in this board
        const cells = mainBoard.querySelectorAll('.main2');
        cells.forEach(cell => {
            cell.style.opacity = '0.2';
            cell.style.pointerEvents = 'none';
        });
    } else {
        // Remove winner state
        mainBoard.classList.remove('winner');
        const winnerDisplay = mainBoard.querySelector('.winner-display');
        if (winnerDisplay) {
            winnerDisplay.remove();
        }
        
        // Restore main2 cells
        const cells = mainBoard.querySelectorAll('.main2');
        cells.forEach(cell => {
            cell.style.opacity = '1';
            cell.style.pointerEvents = 'auto';
        });
    }
}

// Error display function
export function showError(message, duration = 5000) {
    // Remove any existing error messages
    removeError();
    
    // Create error container
    const errorContainer = document.createElement('div');
    errorContainer.id = 'game-error';
    errorContainer.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        font-weight: bold;
        font-size: 16px;
        box-shadow: 0 4px 20px rgba(231, 76, 60, 0.4);
        border: 1px solid rgba(231, 76, 60, 0.3);
        z-index: 1000;
        opacity: 0;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        max-width: 90%;
        text-align: center;
    `;
    
    // Create close button
    const closeBtn = document.createElement('span');
    closeBtn.innerHTML = '×';
    closeBtn.style.cssText = `
        position: absolute;
        top: 5px;
        right: 10px;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
        opacity: 0.7;
        transition: opacity 0.2s ease;
    `;
    closeBtn.onmouseover = () => closeBtn.style.opacity = '1';
    closeBtn.onmouseout = () => closeBtn.style.opacity = '0.7';
    closeBtn.onclick = removeError;
    
    // Add content
    errorContainer.innerHTML = message;
    errorContainer.appendChild(closeBtn);
    
    // Add to document
    document.body.appendChild(errorContainer);
    
    // Animate in
    setTimeout(() => {
        errorContainer.style.opacity = '1';
        errorContainer.style.transform = 'translateX(-50%) translateY(0)';
    }, 10);
    
    // Auto remove after duration
    if (duration > 0) {
        setTimeout(removeError, duration);
    }
}

function removeError() {
    const errorElement = document.getElementById('game-error');
    if (errorElement) {
        errorElement.style.opacity = '0';
        errorElement.style.transform = 'translateX(-50%) translateY(-20px)';
        setTimeout(() => {
            if (errorElement.parentNode) {
                errorElement.parentNode.removeChild(errorElement);
            }
        }, 300);
    }
}

// Success message function
export function showSuccess(message, duration = 3000) {
    // Remove any existing messages
    removeError();
    
    const successContainer = document.createElement('div');
    successContainer.id = 'game-error';
    successContainer.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #4facfe, #00f2fe);
        color: #1a1a2e;
        padding: 15px 25px;
        border-radius: 10px;
        font-weight: bold;
        font-size: 16px;
        box-shadow: 0 4px 20px rgba(79, 172, 254, 0.4);
        border: 1px solid rgba(79, 172, 254, 0.3);
        z-index: 1000;
        opacity: 0;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        max-width: 90%;
        text-align: center;
    `;
    
    successContainer.innerHTML = message;
    document.body.appendChild(successContainer);
    
    setTimeout(() => {
        successContainer.style.opacity = '1';
        successContainer.style.transform = 'translateX(-50%) translateY(0)';
    }, 10);
    
    if (duration > 0) {
        setTimeout(removeError, duration);
    }
}

export function showGameWinner(winner, duration = 8000) {
    // Remove any existing messages first
    removeError();
    removeGameWinner();
    
    // Create compact winner toast
    const winnerToast = document.createElement('div');
    winnerToast.id = 'game-winner-overlay';
    winnerToast.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(-20px);
        background: rgba(30, 30, 40, 0.95);
        backdrop-filter: blur(8px);
        z-index: 2000;
        padding: 14px 20px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        opacity: 0;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 14px;
    `;
    
    // Winner symbol (simple)
    const winnerSymbol = document.createElement('div');
    winnerSymbol.textContent = winner;
    winnerSymbol.style.cssText = `
        font-size: 24px;
        font-weight: bold;
        color: #fff;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 6px;
    `;
    
    // Text container
    const textContainer = document.createElement('div');
    textContainer.style.cssText = `display: flex; flex-direction: column; gap: 1px;`;
    
    // Winner text (simple)
    const winnerText = document.createElement('div');
    winnerText.innerHTML = `<strong>${winner}</strong> Wins`;
    winnerText.style.cssText = `
        font-size: 14px;
        font-weight: 500;
        color: #fff;
    `;
    
    // Subtitle
    const subtitle = document.createElement('div');
    subtitle.textContent = 'Game Over';
    subtitle.style.cssText = `
        font-size: 11px;
        color: rgba(255, 255, 255, 0.5);
    `;
    
    textContainer.appendChild(winnerText);
    textContainer.appendChild(subtitle);
    
    // Button container
    const buttonContainer = document.createElement('div');
    buttonContainer.style.cssText = `display: flex; gap: 6px; margin-left: 6px;`;
    
    // Play again button (simple)
    const playAgainBtn = document.createElement('button');
    playAgainBtn.textContent = 'Replay';
    playAgainBtn.style.cssText = `
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.2s ease;
    `;
    
    // Close button (simple)
    const closeBtn = document.createElement('button');
    closeBtn.textContent = '✕';
    closeBtn.style.cssText = `
        padding: 5px 8px;
        font-size: 11px;
        background: transparent;
        color: rgba(255, 255, 255, 0.5);
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: color 0.2s ease;
    `;
    
    // Hover effects
    playAgainBtn.onmouseover = () => { playAgainBtn.style.background = 'rgba(255, 255, 255, 0.25)'; };
    playAgainBtn.onmouseout = () => { playAgainBtn.style.background = 'rgba(255, 255, 255, 0.15)'; };
    closeBtn.onmouseover = () => { closeBtn.style.color = 'rgba(255, 255, 255, 0.8)'; };
    closeBtn.onmouseout = () => { closeBtn.style.color = 'rgba(255, 255, 255, 0.5)'; };
    
    // Button actions
    playAgainBtn.onclick = () => { window.location.reload(); };
    closeBtn.onclick = removeGameWinner;
    
    buttonContainer.appendChild(playAgainBtn);
    buttonContainer.appendChild(closeBtn);
    
    // Assemble toast
    winnerToast.appendChild(winnerSymbol);
    winnerToast.appendChild(textContainer);
    winnerToast.appendChild(buttonContainer);
    
    document.body.appendChild(winnerToast);
    
    // Animate in
    setTimeout(() => {
        winnerToast.style.opacity = '1';
        winnerToast.style.transform = 'translateX(-50%) translateY(0)';
    }, 50);
    
    // Auto remove
    if (duration > 0) {
        setTimeout(removeGameWinner, duration);
    }
    document.getElementById("title").innerHTML = "Game Over - " + winner + " Wins";
    console.log(document.getElementById("title"));
    
}

function removeGameWinner() {
    const winnerToast = document.getElementById('game-winner-overlay');
    if (winnerToast) {
        winnerToast.style.opacity = '0';
        winnerToast.style.transform = 'translateX(-50%) translateY(-20px)';
        setTimeout(() => {
            if (winnerToast.parentNode) {
                winnerToast.parentNode.removeChild(winnerToast);
            }
        }, 300);
    }
}

// Make functions available globally for inline onclick handlers and x-init
window.posision = posision;
window.showError = showError;
window.showGameWinner = showGameWinner;