<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jouer - 2Fast4U</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/game.css">
    <script src="../js/game.js" defer></script>
    <script src="../js/regles.js" defer></script>
    <link rel="icon" href="../media/car-icon.ico" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&display=swap');

        :root { 
            --cell-size: 50px; 
        }

        /* ----- CONTENEUR PRINCIPAL ----- */
        #grid-container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* EMPÊCHE L'ÉTIREMENT VERTICAL DES CASES */
            padding: 20px 0;
        }

        #grid {
            border-collapse: collapse;
            table-layout: fixed; /* FORCE LE RESPECT DES TAILLES CALCULÉES */
            width: auto !important;
            height: auto !important;
            background-color: #222;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }

        /* ----- LA CELLULE CARRÉE PARFAITE ----- */
        .cell {
            width: var(--cell-size) !important;
            height: var(--cell-size) !important;
            min-width: var(--cell-size) !important;
            min-height: var(--cell-size) !important;
            
            position: relative;
            cursor: pointer;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            
            background-repeat: no-repeat !important;
            background-size: 100% 100% !important; /* STRETCH PARFAIT DE LA TEXTURE */
            background-position: center !important;
        }

        /* ----- TRACES DE PNEUS ----- */
        .cell.track-h::before, .cell.track-v::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: var(--pneu-url);
            background-repeat: no-repeat !important;
            background-size: 100% 100% !important; /* STRETCH DES PNEUS SUR TOUTE LA CASE */
            z-index: 1;
            pointer-events: none;
            opacity: 0.8;
        }
        .cell.track-h::before { transform: rotate(90deg); } /* Lignes */
        .cell.track-v::before { transform: rotate(0deg); }  /* Colonnes */

        /* ----- VOITURES ET CROIX ----- */
        .car-img {
            width: 85%;
            height: 85%;
            object-fit: contain;
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            z-index: 5;
        }

        .x-mark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            font-size: calc(var(--cell-size) * 0.6); 
            color: rgba(0, 0, 0, 0.5);
            font-family: Arial, sans-serif;
            font-weight: bold;
            z-index: 10;
            pointer-events: none;
        }

        .hidden-mode { display: none !important; }
    </style>
</head>
<body class="game-page">

    <header>
    <a href="index.php" class="logo">
        <img src="../media/2fast.png" alt="2Fast4U" style="height: 40px;">
    </a>  
        <nav>
            <a href="index.php">Home</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="#" id="openRules">Rules</a>
        </nav>
    </header>

    <main class="game-main">
        <div id="chrono">00:00</div>
        
        <div id="controls">
            <button id="btn-reset">Réinitialiser</button>
            <button id="btn-new">Nouveau niveau</button>
        </div>

        <div class="game-middle">
            <div id="game-wrap">
                <div id="size-row">
                    SÉLECTIONNE TA TAILLE :
                    <select id="size-select">
                        <!-- TAILLES DE 5 À 12 -->
                        <option value="5">5×5</option>
                        <option value="6">6×6</option>
                        <option value="7">7×7</option>
                        <option value="8" selected>8×8</option>
                        <option value="9">9×9</option>
                        <option value="10">10×10</option>
                        <option value="11">11×11</option>
                        <option value="12">12×12</option>
                    </select>
                </div>
                <div id="grid-container">
                    <table id="grid"></table>
                </div>
                <p id="msg"></p>
            </div>
        </div>
    </main>

    <script>

        function newGame() {
            const urlParams = new URLSearchParams(window.location.search);
            const type = urlParams.get('type');
            const id = urlParams.get('id');
            const btnNew = document.getElementById('btn-new');
            const sizeRow = document.getElementById('size-row');

            if (type === 'fixed' && id !== null) {
                if(btnNew) btnNew.classList.add('hidden-mode');
                if(sizeRow) sizeRow.classList.add('hidden-mode');
                const data = PREDEFINED_LEVELS[parseInt(id)];
                SIZE = data.size;
                grid = data.map.map(row => row.map(cid => ({ colorId: cid, hasCar: false, hasX: false })));
            } else {
                if(btnNew) btnNew.classList.remove('hidden-mode');
                if(sizeRow) sizeRow.classList.remove('hidden-mode');
                SIZE = +document.getElementById('size-select').value;
                grid = genererNiveau(SIZE); // Vient de game.js
            }
            renderGrid(); // Appellera la bonne version (celle de game.js)
            startChrono(); // Vient de game.js
        }

        document.getElementById('btn-new').addEventListener('click', newGame);
        document.getElementById('size-select').addEventListener('change', newGame);
        document.getElementById('btn-reset').addEventListener('click', () => {
            for(let i=0;i<SIZE;i++) for(let j=0;j<SIZE;j++) { grid[i][j].hasCar=false; grid[i][j].hasX=false; }
            renderGrid();
        });

        window.addEventListener('DOMContentLoaded', newGame);
        window.addEventListener('resize', renderGrid);
    </script>

    <div id="overlay" class="overlay">
    <div class="modal">
        <h2>Game Rules</h2>
        <ul>
            <li>One car per row</li>
            <li>One car per column</li>
            <li>No cars touching each other</li>
            <li>Each color must contain one car</li>
        </ul>
        <a href="#" id="closeRules">Close</a>
    </div>
</div>
<footer>
    <p>2Fast4U • Racing Puzzle Experience</p>
</footer>
</body>
</html>