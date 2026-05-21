// --- ASSETS ET CONFIGURATION ---
const TEXTURE_URLS = ['../image/bleu.png','../image/vert.png','../image/rouge.png','../image/jaune.png','../image/violet.png','../image/rose.png','../image/bleu_ciel.png','../image/marron.png','../image/orange.png','../image/gris.png'];
const CAR_IMAGES = ['../image/bleu_voiture.png','../image/vert_voiture.png','../image/rouge_voiture.png','../image/jaune_voiture.png','../image/violet_voiture.png','../image/rose_voiture.png','../image/bleu_ciel_voiture.png','../image/marron_voiture.png','../image/orange_voiture.png','../image/gris_voiture.png'];
const PNEU_IMAGES = ['../image/bleu_pneux.png','../image/vert_pneux.png','../image/rouge_pneux.png','../image/jaune_pneux.png','../image/violet_pneux.png','../image/rose_pneux.png','../image/bleu_ciel_pneux.png','../image/marron_pneux.png','../image/orange_pneux.png','../image/gris_pneux.png'];
const PALETTE = ['#E57373','#81C784','#64B5F6','#FFD54F','#BA68C8','#4DB6AC','#FF8A65','#90A4AE','#A5D6A7','#F48FB1','#80DEEA','#FFCC80'];

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
    // Ajoute tes autres niveaux ici...
];

let SIZE = 8, grid = [], solution = [], chronoInterval = null, secondes = 0;

// --- ALGORITHME DE GÉNÉRATION ALÉATOIRE (C'est ce qui manquait !) ---
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