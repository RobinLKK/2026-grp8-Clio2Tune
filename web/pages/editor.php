<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2Fast4U - Level Editor</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/editor.css">
    <link rel="icon" href="../media/ico-car.ico" type="image/x-icon">

</head>
<body>

<header>
    <a href="index.php" class="logo">
        <img src="../media/2fast.png" alt="2Fast4U" style="height: 40px;">
    </a> 

    <nav>
        <a href="index.php">Home</a>
        <a href="leaderboard.php">Leaderboard</a>

        <?php if (isset($_SESSION["pseudo"])): ?>
            <a href="profile.php"><?= htmlspecialchars($_SESSION["pseudo"]) ?></a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<main class="editor-container">

    <section class="editor-left">
        <h2>LEVEL EDITOR</h2>
        <p class="subtitle">Build your own racing puzzle track.</p>

        <div class="editor-options">
            <div class="option-card">
                <h3>Grid Size</h3>
                <select id="gridSize">
                    <option value="5">5 x 5</option>
                    <option value="6">6 x 6</option>
                    <option value="7">7 x 7</option>
                    <option value="8" selected>8 x 8</option>
                    <option value="9">9 x 9</option>
                    <option value="10">10 x 10</option>
                    <option value="11">11 x 11</option>
                </select>
            </div>

            <div class="option-card">
                <h3>Difficulty</h3>
                <select>
                    <option>★☆☆☆☆</option>
                    <option>★★☆☆☆</option>
                    <option>★★★☆☆</option>
                    <option>★★★★☆</option>
                    <option>★★★★★</option>
                </select>
            </div>

            <div class="option-card">
                <h3>Cell Color</h3>
                <select id="cellColor">
                    <option value="#243b67">Blue</option>
                    <option value="#eeff00">yellow</option>
                    <option value="#2e8b57">Green</option>
                    <option value="#b22222">Red</option>
                    <option value="#555555">Gray</option>
                    <option value="#9f02befb">Purple</option>
                    <option value="#ff6600f3">orange</option>
                    <option value="#ff660063">brown</option>
                    <option value="#fff098bb">Beige</option>
                    <option value="#ff00aa">pink</option>
                    <option value="#ffffff">white</option>
                    <option value="#00ffff">Cyan</option>
                </select>
            </div>
        </div>

        <div class="editor-buttons">
            <button class="btn-outline" id="saveBtn">Save</button>
            <button class="btn-reset" id="resetGrid">Reset</button>
        </div>
    </section>

    <section class="editor-right">
        <div class="grid-preview" id="gridPreview"></div>
    </section>

</main>

<div class="modal-overlay" id="saveModal">
    <div class="custom-modal">
        <button class="modal-close" id="closeModal">&times;</button>
        
        <div class="modal-header">
            <span>🏆</span>
            <h2 class="modal-title">VICTORY</h2>
            <span>🏆</span>
        </div>

        <div class="modal-body">
            <p style="font-weight: bold; color: #ffffff; font-size: 1.3rem; margin-bottom: 5px;">Level created !</p>
            <hr class="modal-divider">
            <p>Your track is ready. Copy the link below to share it :</p>
            <div class="modal-link-box">
                <span class="modal-link-text" id="modalLevelLink">http://localhost/...</span>
            </div>
        </div>

        <button class="modal-btn" id="modalCopyBtn">Copy</button>
    </div>
</div>

<footer>
    <p>© 2Fast4U • Racing Queen's Game</p>
</footer>

<script>
    const gridPreview = document.getElementById("gridPreview");
    const gridSize = document.getElementById("gridSize");
    const cellColor = document.getElementById("cellColor");

    let currentLevelId = 10;

    function createGrid(size) {
        gridPreview.innerHTML = "";
        gridPreview.style.gridTemplateColumns = `repeat(${size}, 1fr)`;

        for(let i = 0; i < size * size; i++) {
            const cell = document.createElement("div");
            cell.classList.add("cell");
            cell.addEventListener("click", () => {
                cell.style.background = cellColor.value;
            });
            gridPreview.appendChild(cell);
        }
    }
    
    gridSize.addEventListener("change", () => createGrid(gridSize.value));
    const resetGrid = document.getElementById("resetGrid");
    resetGrid.addEventListener("click", () => createGrid(gridSize.value));
    
    createGrid(8);

    // --- LOGIQUE DE LA MODALE & DE COPIE ---
    const saveBtn = document.getElementById("saveBtn");
    const saveModal = document.getElementById("saveModal");
    const closeModal = document.getElementById("closeModal");
    const modalCopyBtn = document.getElementById("modalCopyBtn");
    const modalLevelLink = document.getElementById("modalLevelLink");

    let targetUrl = "";

    saveBtn.addEventListener("click", () => {
        targetUrl = `http://localhost/bim/hello/web/pages/game.php?type=fixed&id=${currentLevelId}`;
        
        modalLevelLink.textContent = targetUrl;
        modalCopyBtn.textContent = "Copy";
        modalCopyBtn.classList.remove("copied");

        saveModal.style.display = "flex";
        
        currentLevelId++;
    });

    modalCopyBtn.addEventListener("click", () => {
        navigator.clipboard.writeText(targetUrl).then(() => {
            modalCopyBtn.textContent = "Copied !";
            modalCopyBtn.classList.add("copied");
            
            setTimeout(() => {
                modalCopyBtn.textContent = "Copy";
                modalCopyBtn.classList.remove("copied");
            }, 2000);
        }).catch(err => {
            console.error("Erreur lors de la copie : ", err);
        });
    });

    function hideModal() {
        saveModal.style.display = "none";
    }

    closeModal.addEventListener("click", hideModal);
    saveModal.addEventListener("click", (e) => {
        if (e.target === saveModal) hideModal();
    });
</script>

</body>
</html>