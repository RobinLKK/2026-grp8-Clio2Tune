// --- ASSETS ET CONFIGURATION ---
// --- ASSETS ET CONFIGURATION BLINDÉE ---
const CONFIG_COULEURS = [
    { txt: '../image/bleu.png',      car: '../image/bleu_voiture.png',      pneu: '../image/bleu_pneux.png',      cross: '../image/bleu_croisement_pneux.png' },      // Index 0
    { txt: '../image/vert.png',      car: '../image/vert_voiture.png',      pneu: '../image/vert_pneux.png',      cross: '../image/vert_croisement_pneux.png' },      // Index 1
    { txt: '../image/rouge.png',     car: '../image/rouge_voiture.png',     pneu: '../image/rouge_pneux.png',     cross: '../image/rouge_croisement_pneux.png' },     // Index 2
    { txt: '../image/jaune.png',     car: '../image/jaune_voiture.png',     pneu: '../image/jaune_pneux.png',     cross: '../image/jaune_croisement_pneux.png' },     // Index 3
    { txt: '../image/violet.png',    car: '../image/violet_voiture.png',    pneu: '../image/violet_pneux.png',    cross: '../image/violet_croisement_pneux.png' },    // Index 4
    { txt: '../image/rose.png',      car: '../image/rose_voiture.png',      pneu: '../image/rose_pneux.png',      cross: '../image/rose_croisement_pneux.png' },      // Index 5
    { txt: '../image/bleu_ciel.png', car: '../image/bleu_ciel_voiture.png', pneu: '../image/bleu_ciel_pneux.png', cross: '../image/bleu_ciel_croisement_pneux.png' }, // Index 6
    { txt: '../image/marron.png',    car: '../image/marron_voiture.png',    pneu: '../image/marron_pneux.png',    cross: '../image/marron_croisement_pneux.png' },    // Index 7
    { txt: '../image/orange.png',    car: '../image/orange_voiture.png',    pneu: '../image/orange_pneux.png',    cross: '../image/orange_croisement_pneux.png' },    // Index 8
    { txt: '../image/gris.png',      car: '../image/gris_voiture.png',      pneu: '../image/gris_pneux.png',      cross: '../image/gris_croisement_pneux.png' }       // Index 9
];
// --- TES NIVEAUX PRÉDÉFINIS ---
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

let SIZE = 8, grid = [], solution = [], chronoInterval = null, secondes = 0;

// --- DÉFINITION DE RENDERGRID() SANS PALETTE ---
function renderGrid() {
    if (typeof SIZE === 'undefined' || !grid.length) return;

    const availableWidth = Math.min(600, window.innerWidth - 40);
    const cellPx = availableWidth / SIZE;
    
    document.documentElement.style.setProperty('--cell-size', cellPx + 'px');
    
    const table = document.getElementById('grid');
    table.innerHTML = '';
    
    let positionsVoitures = [];
    for (let i = 0; i < SIZE; i++) {
        for (let j = 0; j < SIZE; j++) {
            if (grid[i][j] && grid[i][j].hasCar) {
                positionsVoitures.push({ r: i, c: j });
            }
        }
    }

    for (let i = 0; i < SIZE; i++) {
        const tr = document.createElement('tr');
        for (let j = 0; j < SIZE; j++) {
            const td = document.createElement('td');
            td.className = 'cell';
            
            const colorId = grid[i][j] ? grid[i][j].colorId : 0;
            const rid = Math.abs(colorId) % CONFIG_COULEURS.length;
            const cfg = CONFIG_COULEURS[rid];

            let traceH = false, traceV = false;
            for (let voiture of positionsVoitures) {
                if (voiture.r === i && voiture.c !== j) traceH = true;
                if (voiture.c === j && voiture.r !== i) traceV = true;
            }

            // RENDU DES TEXTURES VIA NOTRE CONFIG UNIQUE
            if (traceH && traceV) {
                if (cfg.cross) td.style.backgroundImage = `url('${cfg.cross}')`;
            } else {
                if (cfg.txt) td.style.backgroundImage = `url('${cfg.txt}')`;

                if (traceH || traceV) {
                    if (cfg.pneu) {
                        td.style.setProperty('--pneu-url', `url('${cfg.pneu}')`);
                        if (traceH) td.classList.add('track-h');
                        if (traceV) td.classList.add('track-v');
                    }
                }
            }

            if (grid[i][j] && grid[i][j].hasCar) {
                const img = document.createElement('img');
                img.src = cfg.car;
                img.className = 'car-img';
                td.appendChild(img);
            } else if (grid[i][j] && grid[i][j].hasX) {
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

// --- ALGORITHME DE GÉNÉRATION ALÉATOIRE ---
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
    for (let i = size-1; i > 0; i--) { 
        let j = Math.floor(Math.random()*(i+1)); 
        [cols[i],cols[j]]=[cols[j],cols[i]]; 
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
    for (let i=0;i<size;i++) for(let j=0;j<size;j++) g[i][j].hasCar=false;
    return g;
}

// --- LOGIQUE DE JEU ---
function safeligne(g,r,c,size){for(let j=0;j<size;j++)if(g[r][j].hasCar)return false;for(let i=0;i<size;i++)if(g[i][c].hasCar)return false;return true;}
function emptyregion(g,r,c,size){let col=g[r][c].colorId;for(let i=0;i<size;i++)for(let j=0;j<size;j++)if(!(i===r&&j===c)&&g[i][j].colorId===col&&g[i][j].hasCar)return false;return true;}
function safearound(g,r,c,size){for(let di=-1;di<=1;di++)for(let dj=-1;dj<=1;dj++){if(!di&&!dj)continue;let ni=r+di,nj=c+dj;if(ni>=0&&ni<size&&nj>=0&&nj<size&&g[ni][nj].hasCar)return false;}return true;}
function estPlacable(g,r,c,size){return safeligne(g,r,c,size)&&emptyregion(g,r,c,size)&&safearound(g,r,c,size);}
function verifierVictoire(g,size){let n=0;for(let i=0;i<size;i++)for(let j=0;j<size;j++)if(g[i][j].hasCar)n++;return n===size;}

function onCellClick(e) {
    const r=+e.currentTarget.dataset.r, c=+e.currentTarget.dataset.c;
    const tile=grid[r][c], msg=document.getElementById('msg');
    if (tile.hasCar) { tile.hasCar=false; tile.hasX=true; }
    else if (tile.hasX) { tile.hasX=false; }
    else {
        if (estPlacable(grid,r,c,SIZE)) { tile.hasCar=true; }
        else {
            e.currentTarget.classList.add('error-flash');
            setTimeout(()=>e.currentTarget.classList.remove('error-flash'),400);
            return;
        }
    }
    renderGrid();
    if (verifierVictoire(grid,SIZE)) {
        clearInterval(chronoInterval);
        msg.textContent='🎉 Bravo !';
        msg.className='win';
    }
}

function startChrono() {
    clearInterval(chronoInterval); secondes = 0;
    chronoInterval = setInterval(() => {
        secondes++;
        const m = String(Math.floor(secondes/60)).padStart(2,'0');
        const s = String(secondes%60).padStart(2,'0');
        document.getElementById('chrono').textContent = `${m}:${s}`;
    }, 1000);
}