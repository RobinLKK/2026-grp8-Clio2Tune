<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jouer - 2Fast4U</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/game.css">
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
        <a href="index.php" class="logo">2Fast4U</a>
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
        function renderGrid() {
            if (typeof SIZE === 'undefined' || !grid.length) return;

            // ON CALCULE LA TAILLE POUR QUE LE PLATEAU FASSE 600PX MAX
            // On s'adapte si l'écran est plus petit (mobile-friendly)
            const availableWidth = Math.min(600, window.innerWidth - 40);
            const cellPx = availableWidth / SIZE;
            
            document.documentElement.style.setProperty('--cell-size', cellPx + 'px');
            
            const table = document.getElementById('grid');
            table.innerHTML = '';
            
            let lignesV = new Set(), colonnesV = new Set();
            for (let i = 0; i < SIZE; i++) {
                for (let j = 0; j < SIZE; j++) {
                    if (grid[i][j].hasCar) { lignesV.add(i); colonnesV.add(j); }
                }
            }

            for (let i = 0; i < SIZE; i++) {
                const tr = document.createElement('tr');
                for (let j = 0; j < SIZE; j++) {
                    const td = document.createElement('td');
                    td.className = 'cell';
                    const rid = grid[i][j].colorId % TEXTURE_URLS.length;
                    
                    td.style.backgroundColor = PALETTE[rid];
                    td.style.backgroundImage = `url('${TEXTURE_URLS[rid]}')`;

                    if (lignesV.has(i) || colonnesV.has(j)) {
                        const pneuImg = PNEU_IMAGES[rid];
                        td.style.setProperty('--pneu-url', `url('${pneuImg}')`);
                        if (lignesV.has(i)) td.classList.add('track-h');
                        if (colonnesV.has(j)) td.classList.add('track-v');
                    }

                    if (grid[i][j].hasCar) {
                        const img = document.createElement('img');
                        img.src = CAR_IMAGES[rid];
                        img.className = 'car-img';
                        td.appendChild(img);
                    } else if (grid[i][j].hasX) {
                        const xMark = document.createElement('span');
                        xMark.className = 'x-mark';
                        xMark.textContent = '✕';
                        td.appendChild(xMark);
                    }

                    td.dataset.r = i; td.dataset.c = j;
                    td.addEventListener('click', onCellClick);
                    tr.appendChild(td);
                }
                table.appendChild(tr);
            }
        }

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
                grid = genererNiveau(SIZE);
            }
            renderGrid();
            startChrono();
        }

        document.getElementById('btn-new').addEventListener('click', newGame);
        document.getElementById('size-select').addEventListener('change', newGame);
        document.getElementById('btn-reset').addEventListener('click', () => {
            for(let i=0;i<SIZE;i++) for(let j=0;j<SIZE;j++) { grid[i][j].hasCar=false; grid[i][j].hasX=false; }
            renderGrid();
        });

        window.addEventListener('DOMContentLoaded', newGame);
        // Recalculer si on redimensionne la fenêtre
        window.addEventListener('resize', renderGrid);
    </script>
</body>
</html>