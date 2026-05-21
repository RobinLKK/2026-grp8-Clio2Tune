// ================================================================
//  2FAST4U — game.js
//  Tout le code du jeu Queens Game
// ================================================================

// --- ASSETS ---
const TEXTURE_URLS = [
    '../image/bleu.png',
    '../image/vert.png',
    '../image/rouge.png',
    '../image/jaune.png',
    '../image/violet.png',
    '../image/rose.png',
    '../image/bleu_ciel.png',
    '../image/marron.png',
    '../image/orange.png',
    '../image/gris.png',
];
const CAR_IMAGES = [
    '../image/bleu_voiture.png',
    '../image/vert_voiture.png',
    '../image/rouge_voiture.png',
    '../image/jaune_voiture.png',
    '../image/violet_voiture.png',
    '../image/rose_voiture.png',
    '../image/bleu_ciel_voiture.png',
    '../image/marron_voiture.png',
    '../image/orange_voiture.png',
    '../image/gris_voiture.png',
];
const PNEU_IMAGES = [
    '../image/bleu_pneux.png',
    '../image/vert_pneux.png',
    '../image/rouge_pneux.png',
    '../image/jaune_pneux.png',
    '../image/violet_pneux.png',
    '../image/rose_pneux.png',
    '../image/bleu_ciel_pneux.png',
    '../image/marron_pneux.png',
    '../image/orange_pneux.png',
    '../image/gris_pneux.png',
];
const PALETTE = [
    '#E57373','#81C784','#64B5F6','#FFD54F',
    '#BA68C8','#4DB6AC','#FF8A65','#90A4AE',
    '#A5D6A7','#F48FB1','#80DEEA','#FFCC80'
];

// --- ÉTAT ---
let SIZE = 8, grid = [], solution = [];
let chronoInterval = null, secondes = 0;

// niveau prédef 

const PREDEFINED_LEVELS = [
    {
        size: 5,
        map: [
            [0, 0, 1, 1, 1],
            [0, 2, 2, 1, 1],
            [0, 2, 3, 3, 3],
            [4, 2, 4, 4, 3],
            [4, 4, 4, 3, 3]
        ],
        solution: [{r: 0, c: 0}, {r: 1, c: 3}, {r: 2, c: 1}, {r: 3, c: 4}, {r: 4, c: 2}]
    },
];



// ================================================================
//  GÉNÉRATION DE NIVEAU
// ================================================================
function initGrid(size) {
    return Array.from({length: size}, () =>
        Array.from({length: size}, () => ({ colorId: -1, hasCar: false, hasX: false }))
    );
}

function peutPlacerGen(g, r, c, size) {
    for (let i = 0; i < r; i++)
        for (let j = 0; j < size; j++)
            if (g[i][j].hasCar && (j === c || (Math.abs(i-r) <= 1 && Math.abs(j-c) <= 1)))
                return false;
    return true;
}

function placerReinesAlea(g, r, size) {
    if (r === size) return true;
    let cols = Array.from({length: size}, (_, i) => i);
    for (let i = size-1; i > 0; i--) {
        let j = Math.floor(Math.random()*(i+1));
        [cols[i], cols[j]] = [cols[j], cols[i]];
    }
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
            if (nr>=0&&nr<size&&nc>=0&&nc<size&&g[nr][nc].colorId===-1) {
                g[nr][nc].colorId = g[r][c].colorId;
                rest--;
            }
        }
    }

    // Sauvegarde solution avant d'effacer les voitures
    solution = [];
    for (let i = 0; i < size; i++)
        for (let j = 0; j < size; j++)
            if (g[i][j].hasCar) solution.push({r: i, c: j});

    for (let i=0; i<size; i++)
        for (let j=0; j<size; j++)
            g[i][j].hasCar = false;

    return g;
}

// ================================================================
//  RENDU
// ================================================================
function shadeColor(hex, pct) {
    let n = parseInt(hex.slice(1), 16);
    let r = Math.min(255, Math.max(0, (n >> 16) + pct));
    let g = Math.min(255, Math.max(0, ((n >> 8) & 0xff) + pct));
    let b = Math.min(255, Math.max(0, (n & 0xff) + pct));
    return '#' + [r, g, b].map(v => v.toString(16).padStart(2,'0')).join('');
}

function getPatternDataURL(colorHex, patternIndex) {
    const c = colorHex, dark = shadeColor(colorHex, -25);
    const patterns = [
        `<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16'><rect width='16' height='16' fill='${c}'/><line x1='0' y1='0' x2='16' y2='16' stroke='${dark}' stroke-width='1.5'/></svg>`,
        `<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12'><rect width='12' height='12' fill='${c}'/><circle cx='6' cy='6' r='2.5' fill='${dark}'/></svg>`,
        `<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16'><rect width='16' height='16' fill='${c}'/><rect x='0' y='0' width='8' height='8' fill='${dark}' opacity='0.3'/><rect x='8' y='8' width='8' height='8' fill='${dark}' opacity='0.3'/></svg>`,
        `<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16'><rect width='16' height='16' fill='${c}'/><line x1='8' y1='2' x2='8' y2='14' stroke='${dark}' stroke-width='1.5'/><line x1='2' y1='8' x2='14' y2='8' stroke='${dark}' stroke-width='1.5'/></svg>`,
    ];
    const svg = patterns[patternIndex % patterns.length];
    return `url("data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}")`;
}

function getCellStyle(colorId, pneuImg = null) {
    const idx = colorId % PALETTE.length;
    const userPng = TEXTURE_URLS[idx];
    if (pneuImg && userPng) {
        return { backgroundImage: `url('${pneuImg}'), url('${userPng}')`, backgroundSize: 'cover, cover', backgroundColor: PALETTE[idx] };
    } else if (userPng) {
        return { backgroundImage: `url('${userPng}')`, backgroundSize: 'cover', backgroundColor: PALETTE[idx] };
    } else {
        return { backgroundImage: getPatternDataURL(PALETTE[idx], idx), backgroundSize: '32px 32px', backgroundColor: PALETTE[idx] };
    }
}

function renderGrid() {
    const cellPx = Math.min(52, Math.floor(560/SIZE));
    document.documentElement.style.setProperty('--cell', cellPx+'px');

    const table = document.getElementById('grid');
    table.innerHTML = '';

    let lignesAvecVoiture = new Set();
    let colonnesAvecVoiture = new Set();
    for (let i = 0; i < SIZE; i++)
        for (let j = 0; j < SIZE; j++)
            if (grid[i][j].hasCar) { lignesAvecVoiture.add(i); colonnesAvecVoiture.add(j); }

    for (let i = 0; i < SIZE; i++) {
        const tr = document.createElement('tr');
        for (let j = 0; j < SIZE; j++) {
            const td = document.createElement('td');
            td.className = 'cell';

            const regionId = grid[i][j].colorId % TEXTURE_URLS.length;
            let pneuImg = null;
            if (lignesAvecVoiture.has(i) || colonnesAvecVoiture.has(j))
                pneuImg = PNEU_IMAGES[regionId];

            const style = getCellStyle(regionId, pneuImg);
            td.style.backgroundImage  = style.backgroundImage;
            td.style.backgroundSize   = style.backgroundSize;
            td.style.backgroundColor  = style.backgroundColor;

            if (grid[i][j].hasCar) {
                td.classList.add('has-car');
                const img = document.createElement('img');
                img.src = CAR_IMAGES[regionId] || '../image/gris_voiture.png';
                img.className = 'car-img';
                img.alt = 'Voiture';
                img.onerror = function() { this.style.border = "2px dashed red"; };
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

// ================================================================
//  VALIDATION
// ================================================================
function safeligne(g,r,c,size)   { for(let j=0;j<size;j++) if(g[r][j].hasCar) return false; for(let i=0;i<size;i++) if(g[i][c].hasCar) return false; return true; }
function emptyregion(g,r,c,size) { let col=g[r][c].colorId; for(let i=0;i<size;i++) for(let j=0;j<size;j++) if(!(i===r&&j===c)&&g[i][j].colorId===col&&g[i][j].hasCar) return false; return true; }
function safearound(g,r,c,size)  { for(let di=-1;di<=1;di++) for(let dj=-1;dj<=1;dj++) { if(!di&&!dj) continue; let ni=r+di,nj=c+dj; if(ni>=0&&ni<size&&nj>=0&&nj<size&&g[ni][nj].hasCar) return false; } return true; }
function estPlacable(g,r,c,size) { return safeligne(g,r,c,size) && emptyregion(g,r,c,size) && safearound(g,r,c,size); }
function verifierVictoire(g,size){ let n=0; for(let i=0;i<size;i++) for(let j=0;j<size;j++) if(g[i][j].hasCar) n++; return n===size; }

// ================================================================
//  INTERACTIONS
// ================================================================
function onCellClick(e) {
    const r = +e.currentTarget.dataset.r, c = +e.currentTarget.dataset.c;
    const tile = grid[r][c];
    const msg  = document.getElementById('msg');
    msg.className = '';

    if (tile.hasCar) {
        tile.hasCar = false; tile.hasX = true;
    } else if (tile.hasX) {
        tile.hasX = false;
    } else {
        if (estPlacable(grid, r, c, SIZE)) {
            tile.hasCar = true;
        } else {
            e.currentTarget.classList.add('error-flash');
            setTimeout(() => e.currentTarget.classList.remove('error-flash'), 400);
            msg.textContent = 'Placement invalide !';
            return;
        }
    }

    renderGrid();

    if (verifierVictoire(grid, SIZE)) {
        stopChrono();
        msg.className = 'win';
    } else {
        msg.textContent = 'Cliquez pour placer une voiture';
    }
}

// ================================================================
//  CHRONO
// ================================================================
function startChrono() {
    clearInterval(chronoInterval);
    secondes = 0;
    const el = document.getElementById('chrono');
    if (el) el.textContent = '00:00';
    chronoInterval = setInterval(() => {
        secondes++;
        const m = String(Math.floor(secondes/60)).padStart(2,'0');
        const s = String(secondes%60).padStart(2,'0');
        if (el) el.textContent = `${m}:${s}`;
    }, 1000);
}

function stopChrono() {
    clearInterval(chronoInterval);

    // Calcul difficulté selon taille de grille
    const difficulte = SIZE <= 5 ? 1 : SIZE <= 6 ? 2 : SIZE <= 7 ? 3 : SIZE <= 8 ? 4 : 5;

    fetch('../pages/save_score.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=save_score&difficulte=${difficulte}&chrono=${secondes}&id_niveau=0`
    })
    .then(r => r.json())
    .then(data => {
        const msg = document.getElementById('msg');
        if (data.ok) {
            msg.textContent = `🎉 Bravo ! +${data.points} pts (base ${data.base} + bonus ${data.bonus})`;
        } else {
            msg.textContent = '🎉 Bravo, niveau résolu !';
        }
        msg.className = 'win';
    })
    .catch(() => {
        const msg = document.getElementById('msg');
        msg.textContent = '🎉 Bravo, niveau résolu !';
        msg.className = 'win';
    });
}

// ================================================================
//  ACTIONS BOUTONS
// ================================================================
function newGame() {
    SIZE = +document.getElementById('size-select').value;
    grid = genererNiveau(SIZE);
    startChrono();
    const msg = document.getElementById('msg');
    if (msg) { msg.textContent = 'Cliquez pour placer une voiture'; msg.className = ''; }
    renderGrid();
}

function resetGame() {
    for (let i=0; i<SIZE; i++)
        for (let j=0; j<SIZE; j++) {
            grid[i][j].hasCar = false;
            grid[i][j].hasX   = false;
        }
    const msg = document.getElementById('msg');
    if (msg) { msg.textContent = 'Cliquez pour placer une voiture'; msg.className = ''; }
    renderGrid();
}
