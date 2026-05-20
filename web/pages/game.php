<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jouer - Queen's Game</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="icon" href="../media/car-icon.ico" type="image/x-icon">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&display=swap');

    :root { --cell: 52px; }

    #game-wrap {
        padding: 1.5rem 2rem;
        font-family: 'Barlow', sans-serif;
        background: rgba(10, 15, 30, 0.72);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(226, 185, 111, 0.15);
        border-radius: 4px;
        display: inline-block;
        color: #f0ece3;
    }

    #game-subtitle {
        font-size: 0.75rem;
        color: rgba(240, 236, 227, 0.5);
        margin: 4px 0 0;
        letter-spacing: 0.05em;
    }

    #controls { 
        display: flex; 
        gap: 8px; 
    }

    #controls button {
        font-family: 'Barlow', sans-serif;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        padding: 6px 16px;
        border: 1px solid rgba(226, 185, 111, 0.35);
        background: transparent;
        color: rgba(240, 236, 227, 0.7);
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 0;
    }

    #controls button:hover {
        background: rgba(226, 185, 111, 0.12);
        color: #e2b96f;
        border-color: #e2b96f;
    }

    #size-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 1.2rem;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgba(240, 236, 227, 0.45);
    }

    #size-row select {
        font-family: 'Barlow', sans-serif;
        font-size: 0.75rem;
        padding: 4px 8px;
        border: 1px solid rgba(226, 185, 111, 0.3);
        background: rgba(10, 15, 30, 0.8);
        color: #f0ece3;
        cursor: pointer;
        border-radius: 0;
    }

    #grid-container { display: flex; margin-bottom: 1rem; }

    #grid {
        border-collapse: collapse;
        border: 2px solid rgba(226, 185, 111, 0.4);
        table-layout: fixed;
        background-color: #111;
        box-shadow: 0 0 40px rgba(0,0,0,0.6);
    }

    .cell {
        width: var(--cell);
        height: var(--cell);
        min-width: var(--cell); max-width: var(--cell);
        min-height: var(--cell); max-height: var(--cell);
        cursor: pointer;
        text-align: center;
        vertical-align: middle;
        user-select: none;
        transition: filter 0.1s;
        border: none;
        box-shadow: inset 0 0 0 0.5px rgba(0,0,0,0.3);
        background-size: 101% 101%;
        background-repeat: no-repeat;
        background-position: center;
        position: relative;
        padding: 0;
        box-sizing: border-box;
    }
    .cell:hover { filter: brightness(1.2); }

    .car-img {
        width: 85%; height: 85%;
        object-fit: contain;
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        z-index: 2;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.6));
    }

    .cell.has-x::after {
        content: '';
        display: block;
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 45%; height: 45%;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><line x1="8" y1="8" x2="56" y2="56" stroke="%23333" stroke-width="10" stroke-linecap="round"/><line x1="56" y1="8" x2="8" y2="56" stroke="%23333" stroke-width="10" stroke-linecap="round"/></svg>');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        opacity: 0.45;
        pointer-events: none;
        z-index: 3;
    }

    .cell.error-flash { animation: flash 0.4s; }
    @keyframes flash {
        0%,100% { filter: brightness(1); }
        50% { filter: brightness(0.5) saturate(2); }
    }

    #msg {
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.08em;
        min-height: 20px;
        color: rgba(240, 236, 227, 0.4);
        margin: 0.5rem 0 0;
        text-align: center;
        background: none !important;
        text-shadow: none !important;
    }
    #msg.win {
        color: #86efac;
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.1rem;
        letter-spacing: 0.15em;
    }

    #btn-hint, #btn-solution {
    font-family: 'Barlow', sans-serif;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    padding: 6px 16px;
    border: 1px solid rgba(226, 185, 111, 0.35);
    background: transparent;
    color: rgba(240, 236, 227, 0.7);
    cursor: pointer;
    transition: all 0.2s;
    }

    #btn-hint:hover, #btn-solution:hover {
        background: rgba(226, 185, 111, 0.12);
        color: #e2b96f;
        border-color: #e2b96f;
    }
</style>
</head>
<body>
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

    <main style="display:flex; align-items:center; justify-content:center; min-height:calc(100vh - 64px);">
        <div id="game-wrap">

            <div id="controls" style="display:flex; justify-content:center; gap:8px; margin-bottom:1.2rem;">
                <button id="btn-reset">Réinitialiser</button>
                <button id="btn-new">Nouveau niveau</button>
            </div>

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

            <div style="display:flex; justify-content:center; gap:8px; margin-top:1rem;">
                <button id="btn-hint">Un indice</button>
                <button id="btn-solution">Solution finale</button>
            </div>

            <p id="msg"></p>

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

        function getCellStyle(colorId, pneuImg = null) {
            const idx = colorId % PALETTE.length;
            const userPng = TEXTURE_URLS[idx];
            
            // Si une trace de pneu doit être affichée, on la superpose à la texture de fond
            if (pneuImg && userPng) {
                return { 
                    backgroundImage: `url('${pneuImg}'), url('${userPng}')`, 
                    backgroundSize: 'cover, cover', 
                    backgroundColor: PALETTE[idx] 
                };
            } else if (userPng) {
                return { backgroundImage: `url('${userPng}')`, backgroundSize: 'cover', backgroundColor: PALETTE[idx] };
            } else {
                return { backgroundImage: getPatternDataURL(PALETTE[idx], idx), backgroundSize: '32px 32px', backgroundColor: PALETTE[idx] };
            }
        }

        let SIZE = 8, grid = [];

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

            // Sauvegarde ICI avant d'effacer
            solution = [];
            for (let i = 0; i < size; i++)
                for (let j = 0; j < size; j++)
                    if (g[i][j].hasCar) solution.push({r: i, c: j});

            for (let i=0;i<size;i++) for(let j=0;j<size;j++) g[i][j].hasCar=false;
            return g;
        }
        function safeligne(g,r,c,size){for(let j=0;j<size;j++)if(g[r][j].hasCar)return false;for(let i=0;i<size;i++)if(g[i][c].hasCar)return false;return true;}
        function emptyregion(g,r,c,size){let col=g[r][c].colorId;for(let i=0;i<size;i++)for(let j=0;j<size;j++)if(!(i===r&&j===c)&&g[i][j].colorId===col&&g[i][j].hasCar)return false;return true;}
        function safearound(g,r,c,size){for(let di=-1;di<=1;di++)for(let dj=-1;dj<=1;dj++){if(!di&&!dj)continue;let ni=r+di,nj=c+dj;if(ni>=0&&ni<size&&nj>=0&&nj<size&&g[ni][nj].hasCar)return false;}return true;}
        function estPlacable(g,r,c,size){return safeligne(g,r,c,size)&&emptyregion(g,r,c,size)&&safearound(g,r,c,size);}
        function verifierVictoire(g,size){let n=0;for(let i=0;i<size;i++)for(let j=0;j<size;j++)if(g[i][j].hasCar)n++;return n===size;}

        function renderGrid() {
            const cellPx = Math.min(52, Math.floor(560/SIZE));
            document.documentElement.style.setProperty('--cell', cellPx+'px');
            const table = document.getElementById('grid');
            table.innerHTML = '';
            
            // 1. On liste uniquement les index des lignes et des colonnes qui ont une voiture
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
            
            // 2. On dessine la grille
            for (let i = 0; i < SIZE; i++) {
                const tr = document.createElement('tr');
                for (let j = 0; j < SIZE; j++) {
                    const td = document.createElement('td');
                    td.className = 'cell';

                    // L'ID de la région propre à la case actuelle
                    const regionId = grid[i][j].colorId % TEXTURE_URLS.length;
                    
                    // Si la case est sur l'axe d'une voiture, elle prend le pneu DE SA PROPRE COULEUR
                    let pneuImg = null;
                    if (lignesAvecVoiture.has(i) || colonnesAvecVoiture.has(j)) {
                        pneuImg = PNEU_IMAGES[regionId]; // Changement ici : on prend la couleur locale de la case !
                    }

                    const style = getCellStyle(regionId, pneuImg);
                    td.style.backgroundImage = style.backgroundImage;
                    td.style.backgroundSize = style.backgroundSize;
                    td.style.backgroundColor = style.backgroundColor;

                    if (grid[i][j].hasCar) {
                        td.classList.add('has-car');
                        
                        const img = document.createElement('img');
                        img.src = CAR_IMAGES[regionId] || '../image/gris_voiture.png';
                        img.className = 'car-img';
                        img.alt = 'Voiture';
                        
                        img.onerror = function() {
                            this.style.border = "2px dashed red";
                        };
                        
                        td.appendChild(img);
                    }
                    else if (grid[i][j].hasX) {
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
                    msg.textContent='Placement invalide !';
                    return;
                }
            }
            renderGrid();
            if (verifierVictoire(grid,SIZE)) { msg.textContent='🎉 Bravo, niveau résolu !'; msg.className='win'; }
            else { msg.textContent='Cliquez pour placer une voiture · recliquez pour une croix · encore pour effacer'; }
        }
        
        let solution = []; // stocke la solution

        function newGame() {
            SIZE = +document.getElementById('size-select').value;
            grid = genererNiveau(SIZE);
            document.getElementById('msg').textContent = 'Cliquez pour placer une voiture';
            document.getElementById('msg').className = '';
            renderGrid();
        }
        function resetGame() { 
            for(let i=0; i<SIZE; i++) {
                for(let j=0; j<SIZE; j++) {
                    grid[i][j].hasCar = false;
                    grid[i][j].hasX = false;
                }
            } 
            document.getElementById('msg').textContent = 'Cliquez pour placer une voiture';
            document.getElementById('msg').className=''; 
            renderGrid(); 
        }

        document.getElementById('btn-new').addEventListener('click', newGame);
        document.getElementById('btn-reset').addEventListener('click', resetGame);
        document.getElementById('size-select').addEventListener('change', newGame);
        newGame();

        document.getElementById('btn-hint').addEventListener('click', function() {
        // Trouve une case vide où une voiture peut être placée
        for (let i = 0; i < SIZE; i++) {
            for (let j = 0; j < SIZE; j++) {
                if (!grid[i][j].hasCar && !grid[i][j].hasX && estPlacable(grid, i, j, SIZE)) {
                    grid[i][j].hasCar = true;
                    renderGrid();
                    setTimeout(() => {
                        if (!verifierVictoire(grid, SIZE)) {
                            grid[i][j].hasCar = false;
                            renderGrid();
                        }
                    }, 800);
                    return;
                }
            }
        }
    });

    document.getElementById('btn-solution').addEventListener('click', function() {
        // Reset
        for (let i = 0; i < SIZE; i++)
            for (let j = 0; j < SIZE; j++) {
                grid[i][j].hasCar = false;
                grid[i][j].hasX = false;
            }
        // Place la solution sauvegardée
        solution.forEach(pos => { grid[pos.r][pos.c].hasCar = true; });
        renderGrid();
        document.getElementById('msg').textContent = '✓ Solution affichée';
        document.getElementById('msg').className = 'win';
    });
    </script>
</body>
</html>