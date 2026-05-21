<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jouer - Queen's Game</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/game.css">
    <script src="../js/regles.js" defer></script>
    <link rel="icon" href="../media/car-icon.ico" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&display=swap');

        :root { --cell: 52px; }

        .cell {
            width: var(--cell); 
            height: var(--cell);
            min-width: var(--cell);
            max-width: var(--cell);
            min-height: var(--cell);
            max-height: var(--cell);
            cursor: pointer; 
            text-align: center; 
            vertical-align: middle;
            user-select: none;
            transition: filter 0.1s;
            border: none; 
            box-shadow: inset 0 0 0 0.5px rgba(0,0,0,0.25);
            background-size: 101% 101%;
            background-repeat: no-repeat;
            background-position: center;
            position: relative;
            padding: 0;
            box-sizing: border-box;
            overflow: hidden; 
        }
        .cell:hover { filter: brightness(0.88); }

        .car-img {
            width: 90%;
            height: 90%;
            object-fit: contain;
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 2;
        }

        /* ----- SYSTÈME DE TRACES DE PNEUS ----- */
        .cell.track-h::before, .cell.track-v::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: var(--pneu-url);
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            z-index: 1;
            pointer-events: none;
            opacity: 0.85; 
        }

        .cell.track-h::before { transform: rotate(90deg); }
        .cell.track-v::before { transform: rotate(0deg); }
        .cell.track-h.track-v::before { transform: rotate(0deg); }
        /* -------------------------------------- */

        .cell.error-flash { animation: flash 0.4s; }
        @keyframes flash { 0%,100%{filter:brightness(1)} 50%{filter:brightness(0.6) saturate(2)} }
        
        #msg { font-size: 14px; min-height: 20px; color: #ccc; margin-top: 10px; text-align: center; }
        #msg.win { color: #1D9E75; font-weight: 500; font-size: 16px; }
    </style>
</head>
<body class="game-page">

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

    <header>
        <a href="index.php" class="logo">2Fast4U</a>
        <nav>
            <a href="index.php">Home</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="#" id="openRules">Rules</a>
        </nav>
        <div class="nav-right">
            <?php if (isset($_SESSION['pseudo'])): ?>
                <a href="profile.php"><?= htmlspecialchars($_SESSION['pseudo']) ?></a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
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
                    Taille :
                    <select id="size-select">
                        <option value="5">5×5</option>
                        <option value="6">6×6</option>
                        <option value="7">7×7</option>
                        <option value="8" selected>8×8</option>
                        <option value="9">9×9</option>
                        <option value="10">10×10</option>
                    </select>
                </div>
                <div id="grid-container"><table id="grid"></table></div>
                <p id="msg"></p>
            </div>

            <div id="level-leaderboard">
                <div class="lb-title">Top niveau</div>
                <div class="lb-empty">Aucun score<br>enregistré</div>
            </div>

        </div>

        <div class="game-bottom">
            <div class="game-bottom-btns">
                <button id="btn-hint">Un indice</button>
                <button id="btn-solution">Solution finale</button>
            </div>
            <div id="conseil">Cliquez pour placer une voiture · recliquez pour une croix · encore pour effacer</div>
        </div>
    </main>

    <script>
        const TEXTURE_URLS = [
            '../image/bleu.png',       // région 0
            '../image/vert.png',       // région 1
            '../image/rouge.png',      // région 2
            '../image/jaune.png',      // région 3
            '../image/violet.png',     // région 4
            '../image/rose.png',       // région 5
            '../image/bleu_ciel.png',  // région 6
            '../image/marron.png',     // région 7
            '../image/orange.png',     // région 8
            '../image/gris.png',       // région 9
        ];

        const CAR_IMAGES = [
            '../image/bleu_voiture.png',       // région 0
            '../image/vert_voiture.png',       // région 1
            '../image/rouge_voiture.png',      // région 2
            '../image/jaune_voiture.png',      // région 3
            '../image/violet_voiture.png',     // région 4
            '../image/rose_voiture.png',       // région 5
            '../image/bleu_ciel_voiture.png',  // région 6
            '../image/marron_voiture.png',     // région 7
            '../image/orange_voiture.png',     // région 8
            '../image/gris_voiture.png',       // région 9
        ];

        const PNEU_IMAGES = [
            '../image/bleu_pneux.png',       // région 0
            '../image/vert_pneux.png',       // région 1
            '../image/rouge_pneux.png',      // région 2
            '../image/jaune_pneux.png',      // région 3
            '../image/violet_pneux.png',     // région 4
            '../image/rose_pneux.png',       // région 5
            '../image/bleu_ciel_pneux.png',  // région 6
            '../image/marron_pneux.png',     // région 7
            '../image/orange_pneux.png',     // région 8
            '../image/gris_pneux.png',       // région 9
        ];

        const PALETTE = [
            '#E57373','#81C784','#64B5F6','#FFD54F',
            '#BA68C8','#4DB6AC','#FF8A65','#90A4AE',
            '#A5D6A7','#F48FB1','#80DEEA','#FFCC80'
        ];

        function getPatternDataURL(colorHex, patternIndex) {
            const c = colorHex;
            const dark = shadeColor(colorHex, -25);
            const patterns = [
                `<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16'><rect width='16' height='16' fill='${c}'/><line x1='0' y1='0' x2='16' y2='16' stroke='${dark}' stroke-width='1.5'/><line x1='-4' y1='12' x2='4' y2='20' stroke='${dark}' stroke-width='1.5'/><line x1='12' y1='-4' x2='20' y2='4' stroke='${dark}' stroke-width='1.5'/></svg>`,
                `<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12'><rect width='12' height='12' fill='${c}'/><circle cx='6' cy='6' r='2.5' fill='${dark}'/></svg>`,
                `<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16'><rect width='16' height='16' fill='${c}'/><rect x='0' y='0' width='8' height='8' fill='${dark}' opacity='0.3'/><rect x='8' y='8' width='8' height='8' fill='${dark}' opacity='0.3'/></svg>`,
                `<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16'><rect width='16' height='16' fill='${c}'/><line x1='8' y1='2' x2='8' y2='14' stroke='${dark}' stroke-width='1.5'/><line x1='2' y1='8' x2='14' y2='8' stroke='${dark}' stroke-width='1.5'/></svg>`,
                `<svg xmlns='http://www.w3.org/2000/svg' width='20' height='10'><rect width='20' height='10' fill='${c}'/><polyline points='0,5 5,0 10,5 15,0 20,5' fill='none' stroke='${dark}' stroke-width='1.5'/></svg>`,
                `<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16'><rect width='16' height='16' fill='${c}'/><polygon points='8,2 15,14 1,14' fill='${dark}' opacity='0.35'/></svg>`,
                `<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20'><rect width='20' height='20' fill='${c}'/><circle cx='10' cy='10' r='6' fill='none' stroke='${dark}' stroke-width='1.5'/></svg>`,
                `<svg xmlns='http://www.w3.org/2000/svg' width='8' height='8'><rect width='8' height='8' fill='${c}'/><line x1='0' y1='4' x2='8' y2='4' stroke='${dark}' stroke-width='1.2'/></svg>`,
                `<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12'><rect width='12' height='12' fill='${c}'/><line x1='0' y1='6' x2='12' y2='6' stroke='${dark}' stroke-width='0.8'/><line x1='6' y1='0' x2='6' y2='12' stroke='${dark}' stroke-width='0.8'/></svg>`,
                `<svg xmlns='http://www.w3.org/2000/svg' width='24' height='12'><rect width='24' height='12' fill='${c}'/><path d='M0 6 Q6 0 12 6 Q18 12 24 6' fill='none' stroke='${dark}' stroke-width='1.5'/></svg>`,
                `<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16'><rect width='16' height='16' fill='${c}'/><polygon points='8,2 14,8 8,14 2,8' fill='none' stroke='${dark}' stroke-width='1.5'/></svg>`,
                `<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16'><rect width='16' height='16' fill='${c}'/><line x1='16' y1='0' x2='0' y2='16' stroke='${dark}' stroke-width='1.5'/></svg>`,
            ];
            const svg = patterns[patternIndex % patterns.length];
            return `url("data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}")`;
        }

        function shadeColor(hex, pct) {
            let n = parseInt(hex.slice(1), 16);
            let r = Math.min(255, Math.max(0, (n >> 16) + pct));
            let g = Math.min(255, Math.max(0, ((n >> 8) & 0xff) + pct));
            let b = Math.min(255, Math.max(0, (n & 0xff) + pct));
            return '#' + [r, g, b].map(v => v.toString(16).padStart(2,'0')).join('');
        }

        function getCellStyle(colorId) {
            const idx = colorId % PALETTE.length;
            const userPng = TEXTURE_URLS[idx];
            if (userPng) {
                return { backgroundImage: `url('${userPng}')`, backgroundSize: 'cover', backgroundColor: PALETTE[idx] };
            } else {
                return { backgroundImage: getPatternDataURL(PALETTE[idx], idx), backgroundSize: '32px 32px', backgroundColor: PALETTE[idx] };
            }
        }

        let SIZE = 8, grid = [], solutionGrid = []; 
        let chronoTimer = null, secondesEcoulees = 0;

        function demarrerChrono() {
            arreterChrono();
            secondesEcoulees = 0;
            mettreAJourAffichageChrono();
            chronoTimer = setInterval(() => { secondesEcoulees++; mettreAJourAffichageChrono(); }, 1000);
        }
        function arreterChrono() { if (chronoTimer) { clearInterval(chronoTimer); chronoTimer = null; } }
        function mettreAJourAffichageChrono() {
            const minutes = Math.floor(secondesEcoulees / 60);
            const secondes = secondesEcoulees % 60;
            document.getElementById('chrono').textContent = `${String(minutes).padStart(2, '0')}:${String(secondes).padStart(2, '0')}`;
        }

        function initGrid(size) {
            return Array.from({length: size}, () =>
                Array.from({length: size}, () => ({ colorId: -1, hasCar: false, hasX: false }))
            );
        }
        function peutPlacerGen(g, r, c, size) {
            for (let i = 0; i < r; i++)
                for (let j = 0; j < size; j++)
                    if (g[i][j].hasCar && (j === c || (Math.abs(i-r) <= 1 && Math.abs(j-c) <= 1))) return false;
            return true;
        }
        function placerReinesAlea(g, r, size) {
            if (r === size) return true;
            let cols = Array.from({length: size}, (_, i) => i);
            for (let i = size-1; i > 0; i--) { let j = Math.floor(Math.random()*(i+1)); [cols[i],cols[j]]=[cols[j],cols[i]]; }
            for (let c of cols) {
                if (peutPlacerGen(g, r, c, size)) {
                    g[r][c].hasCar = true; g[r][c].colorId = r;
                    if (placerReinesAlea(g, r+1, size)) return true;
                    g[r][c].hasCar = false; g[r][c].colorId = -1;
                }
            }
            return false;
        }
        
        function genererNiveau(size) {
            let g = initGrid(size);
            while (!placerReinesAlea(g, 0, size)) g = initGrid(size);
            
            // SAUVEGARDE STRICTE DES EMPLACEMENTS DES VOITURES GÉNÉRÉES
            solutionGrid = [];
            for (let i = 0; i < size; i++) {
                for (let j = 0; j < size; j++) {
                    if (g[i][j].hasCar) {
                        solutionGrid.push({ r: i, c: j });
                    }
                }
            }

            let rest = size*size - size;
            const dr=[-1,1,0,0], dc=[0,0,-1,1];
            let iter = 0;
            while (rest > 0 && iter < 100000) {
                iter++;
                let r = Math.floor(Math.random()*size), c = Math.floor(Math.random()*size);
                if (g[r][c].colorId !== -1) {
                    let d = Math.floor(Math.random()*4), nr=r+dr[d], nc=c+dc[d];
                    if (nr>=0&&nr<size&&nc>=0&&nc<size&&g[nr][nc].colorId===-1) { g[nr][nc].colorId=g[r][c].colorId; rest--; }
                }
            }

            for (let i=0;i<size;i++) for(let j=0;j<size;j++) g[i][j].hasCar=false;
            return g;
        }

        function safeligne(g,r,c,size){for(let j=0;j<size;j++)if(g[r][j].hasCar)return false;for(let i=0;i<size;i++)if(g[i][c].hasCar)return false;return true;}
        function emptyregion(g,r,c,size){let col=g[r][c].colorId;for(let i=0;i<size;i++)for(let j=0;j<size;j++)if(!(i===r&&j===c)&&g[i][j].colorId===col&&g[i][j].hasCar)return false;return true;}
        function safearound(g,r,c,size){
            for(let di=-1;di<=1;di++)for(let dj=-1;dj<=1;dj++){if(!di&&!dj)continue;let ni=r+di,nj=c+dj;if(ni>=0&&ni<size&&nj>=0&&nj<size&&g[ni][nj].hasCar)return false;}return true;}
        function estPlacable(g,r,c,size){return safeligne(g,r,c,size)&&emptyregion(g,r,c,size)&&safearound(g,r,c,size);}
        function verifierVictoire(g,size){let n=0;for(let i=0;i<size;i++)for(let j=0;j<size;j++)if(g[i][j].hasCar)n++;return n===size;}

        function renderGrid() {
            const cellPx = Math.min(52, Math.floor(560/SIZE));
            document.documentElement.style.setProperty('--cell', cellPx+'px');
            const table = document.getElementById('grid');
            table.innerHTML = '';
    
            let lignesAvecVoiture = new Set();
            let colonnesAvecVoiture = new Set();

            for (let i = 0; i < SIZE; i++) {
                for (let j = 0; j < SIZE; j++) {
                    if (grid[i][j].hasCar) {
                        lignesAvecVoiture.add(i);
                        colonnesAvecVoiture.add(j);
                    }
                }
            }
    
            for (let i = 0; i < SIZE; i++) {
                const tr = document.createElement('tr');
                for (let j = 0; j < SIZE; j++) {
                    const td = document.createElement('td');
                    td.className = 'cell';
                    const regionId = grid[i][j].colorId % TEXTURE_URLS.length;
            
                    const style = getCellStyle(regionId);
                    td.style.backgroundImage = style.backgroundImage;
                    td.style.backgroundSize = style.backgroundSize;
                    td.style.backgroundColor = style.backgroundColor;

                    if (lignesAvecVoiture.has(i) || colonnesAvecVoiture.has(j)) {
                        const pneuImg = PNEU_IMAGES[regionId];
                        if (pneuImg) {
                            td.style.setProperty('--pneu-url', `url('${pneuImg}')`);
                            if (lignesAvecVoiture.has(i)) td.classList.add('track-h');
                            if (colonnesAvecVoiture.has(j)) td.classList.add('track-v');
                        }
                    }

                    if (grid[i][j].hasCar) {
                        td.classList.add('has-car');
                        const img = document.createElement('img');
                        img.src = CAR_IMAGES[regionId] || '../image/gris_voiture.png';
                        img.className = 'car-img';
                        img.alt = 'Voiture';
                        td.appendChild(img);
                    } else if (grid[i][j].hasX) {
                        td.classList.add('has-x');
                    }
            
                    td.dataset.r = i; td.dataset.c = j;
                    td.addEventListener('click', onCellClick);
                    tr.appendChild(td);
                }
                table.appendChild(tr);
            }
        }

        function onCellClick(e) {
            const r=+e.currentTarget.dataset.r, c=+e.currentTarget.dataset.c;
            const tile=grid[r][c], msg=document.getElementById('msg');
            msg.className='';
            if (tile.hasCar) { tile.hasCar=false; tile.hasX=true; }
            else if (tile.hasX) { tile.hasX=false; }
            else {
                if (estPlacable(grid,r,c,SIZE)) { tile.hasCar=true; }
                else {
                    e.currentTarget.classList.add('error-flash');
                    setTimeout(()=>e.currentTarget.classList.remove('error-flash'),400);
                    msg.textContent = 'Placement invalide !';
                    return;
                }
            }
            renderGrid();
            if (verifierVictoire(grid,SIZE)) { 
                arreterChrono();
                msg.textContent='🎉 Bravo, niveau résolu !'; 
                msg.className='win'; 
            } else { msg.textContent=''; }
        }
        
        function newGame() { 
            SIZE=+document.getElementById('size-select').value; 
            grid=genererNiveau(SIZE); 
            document.getElementById('msg').textContent=''; 
            document.getElementById('msg').className=''; 
            renderGrid(); 
            demarrerChrono();
        }
        
        function resetGame() { 
            for(let i=0; i<SIZE; i++) {
                for(let j=0; j<SIZE; j++) { grid[i][j].hasCar = false; grid[i][j].hasX = false; }
            } 
            document.getElementById('msg').textContent=''; 
            document.getElementById('msg').className=''; 
            renderGrid(); 
            demarrerChrono();
        }

        // APPLICATION DE LA VRAIE SOLUTION ENREGISTRÉE ÉTAPE PAR ÉTAPE
        function afficherSolution() {
            arreterChrono();
            
            // Étape 1 : On nettoie le plateau complet
            for (let i = 0; i < SIZE; i++) {
                for (let j = 0; j < SIZE; j++) {
                    grid[i][j].hasCar = false;
                    grid[i][j].hasX = false;
                }
            }
            
            // Étape 2 : On charge uniquement les positions gagnantes mémorisées
            solutionGrid.forEach(pos => {
                if (pos.r < SIZE && pos.c < SIZE) {
                    grid[pos.r][pos.c].hasCar = true;
                }
            });
            
            renderGrid();
            const msg = document.getElementById('msg');
            msg.textContent = 'Solution affichée ! (Chrono arrêté)';
            msg.className = '';
        }

        document.getElementById('btn-new').addEventListener('click', newGame);
        document.getElementById('btn-reset').addEventListener('click', resetGame);
        document.getElementById('size-select').addEventListener('change', newGame);
        
        document.getElementById('btn-hint')?.addEventListener('click', () => { alert('Indice demandé !'); });
        document.getElementById('btn-solution')?.addEventListener('click', afficherSolution);

        newGame();
    </script>
</body>
</html>