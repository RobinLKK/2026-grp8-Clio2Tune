const board = document.getElementById("board");

// Grille 5x5 pour tester (à remplacer par les vraies données)
const size = 5;

for (let row = 0; row < size; row++) {
    for (let col = 0; col < size; col++) {
        const cell = document.createElement("div");
        cell.classList.add("cell");
        cell.dataset.row = row;
        cell.dataset.col = col;
        cell.addEventListener("click", placeQueen);
        board.appendChild(cell);
    }
}

board.style.gridTemplateColumns = `repeat(${size}, 60px)`;

function placeQueen(e) {
    const cell = e.target;
    cell.textContent = cell.textContent === "♛" ? "" : "♛";
}