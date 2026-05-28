// --- ASSETS ET CONFIGURATION ---
const CONFIG_COULEURS = [
    { txt: '../image/bleu.png', car: '../image/bleu_voiture.png', pneu: '../image/bleu_pneux.png', cross: '../image/bleu_croisement_pneux.png' },      // Index 0
    { txt: '../image/vert.png', car: '../image/vert_voiture.png', pneu: '../image/vert_pneux.png', cross: '../image/vert_croisement_pneux.png' },      // Index 1
    { txt: '../image/rouge.png', car: '../image/rouge_voiture.png', pneu: '../image/rouge_pneux.png', cross: '../image/rouge_croisement_pneux.png' },     // Index 2
    { txt: '../image/jaune.png', car: '../image/jaune_voiture.png', pneu: '../image/jaune_pneux.png', cross: '../image/jaune_croisement_pneux.png' },     // Index 3
    { txt: '../image/violet.png', car: '../image/violet_voiture.png', pneu: '../image/violet_pneux.png', cross: '../image/violet_croisement_pneux.png' },    // Index 4
    { txt: '../image/rose.png', car: '../image/rose_voiture.png', pneu: '../image/rose_pneux.png', cross: '../image/rose_croisement_pneux.png' },      // Index 5
    { txt: '../image/bleu_ciel.png', car: '../image/bleu_ciel_voiture.png', pneu: '../image/bleu_ciel_pneux.png', cross: '../image/bleu_ciel_croisement_pneux.png' }, // Index 6
    { txt: '../image/marron.png', car: '../image/marron_voiture.png', pneu: '../image/marron_pneux.png', cross: '../image/marron_croisement_pneux.png' },    // Index 7
    { txt: '../image/orange.png', car: '../image/orange_voiture.png', pneu: '../image/orange_pneux.png', cross: '../image/orange_croisement_pneux.png' },    // Index 8
    { txt: '../image/gris.png', car: '../image/gris_voiture.png', pneu: '../image/gris_pneux.png', cross: '../image/gris_croisement_pneux.png' },      // Index 9
    { txt: '../image/blanc.png', car: '../image/blanc_blanc.png', pneu: '../image/blanc_pneux.png', cross: '../image/blanc_croisement.png' } ,      // Index 10
    { txt: '../image/beige.png', car: '../image/beige_voiture.png', pneu: '../image/beige_pneux.png', cross: '../image/beige_croisement.png' },       // Index 11  
];

// --- PREDEFINED LEVELS ---
const PREDEFINED_LEVELS = [
    {
        size: 5,
        map: [
            [0, 0, 1, 1, 1],
            [0, 0, 1, 2, 2],
            [4, 4, 1, 2, 2],
            [4, 4, 3, 3, 3],
            [4, 4, 3, 3, 3]
        ],
        solution: [{ r: 0, c: 0 }, { r: 1, c: 2 }, { r: 2, c: 4 }, { r: 3, c: 1 }, { r: 4, c: 3 }]
    },
    {
        size: 5,
        map: [
            [0, 0, 1, 1, 1],
            [0, 2, 2, 1, 1],
            [0, 2, 3, 3, 3],
            [4, 2, 4, 4, 3],
            [4, 4, 4, 3, 3]
        ],
        solution: [{ r: 0, c: 0 }, { r: 1, c: 3 }, { r: 2, c: 1 }, { r: 3, c: 4 }, { r: 4, c: 2 }]
    },
    {
        size: 6,
        map: [
            [0, 0, 0, 1, 1, 1],
            [0, 0, 0, 1, 1, 1],
            [2, 2, 2, 3, 3, 1],
            [2, 4, 4, 3, 3, 3],
            [4, 4, 4, 4, 3, 5],
            [4, 4, 5, 5, 5, 5]
        ],
        solution: [{ r: 0, c: 2 }, { r: 1, c: 4 }, { r: 2, c: 0 }, { r: 3, c: 3 }, { r: 4, c: 1 }, { r: 5, c: 5 }]
    },
    {
        size: 6,
        map: [
            [0, 0, 1, 1, 2, 2],
            [0, 0, 1, 1, 1, 2],
            [3, 4, 4, 4, 4, 2],
            [3, 3, 4, 4, 5, 5],
            [3, 3, 4, 5, 5, 5],
            [3, 3, 5, 5, 5, 5]
        ],
        solution: [{ r: 0, c: 1 }, { r: 1, c: 3 }, { r: 2, c: 5 }, { r: 3, c: 0 }, { r: 4, c: 2 }, { r: 5, c: 4 }]
    },
    {
        size: 7,
        map: [
            [0, 0, 0, 0, 1, 1, 1],
            [2, 2, 0, 0, 1, 1, 1],
            [2, 2, 2, 3, 3, 1, 1],
            [2, 3, 3, 3, 3, 4, 4],
            [5, 5, 3, 3, 6, 6, 4],
            [5, 5, 5, 6, 6, 6, 4],
            [5, 5, 6, 6, 6, 4, 4]
        ],
        solution: [{ r: 0, c: 2 }, { r: 1, c: 5 }, { r: 2, c: 0 }, { r: 3, c: 3 }, { r: 4, c: 6 }, { r: 5, c: 1 }, { r: 6, c: 4 }]
    },
    {
        size: 8,
        map: [
            [0, 0, 0, 0, 1, 1, 1, 1],
            [2, 0, 0, 1, 1, 1, 1, 5],
            [2, 2, 0, 3, 3, 1, 5, 5],
            [2, 2, 3, 3, 3, 5, 5, 5],
            [4, 4, 3, 3, 6, 6, 5, 5],
            [4, 4, 4, 6, 6, 6, 6, 5],
            [4, 4, 7, 6, 6, 6, 7, 7],
            [4, 7, 7, 7, 7, 7, 7, 7]
        ],
        solution: [{ r: 0, c: 2 }, { r: 1, c: 5 }, { r: 2, c: 0 }, { r: 3, c: 3 }, { r: 4, c: 1 }, { r: 5, c: 7 }, { r: 6, c: 4 }, { r: 7, c: 6 }]
    },
    {
        size: 9,
        map: [
            [5, 5, 5, 5, 8, 8, 8, 8, 8],
            [5, 5, 5, 5, 8, 8, 8, 8, 8],
            [0, 0, 5, 5, 2, 2, 2, 8, 8],
            [0, 0, 0, 2, 2, 2, 2, 7, 7],
            [0, 0, 1, 2, 2, 2, 7, 7, 7],
            [1, 1, 1, 2, 2, 4, 4, 4, 7],
            [1, 1, 1, 3, 3, 4, 4, 4, 4],
            [1, 1, 3, 3, 3, 3, 6, 6, 6],
            [1, 3, 3, 3, 3, 6, 6, 6, 6]
        ],
        solution: [{ r: 0, c: 2 }, { r: 1, c: 5 }, { r: 2, c: 0 }, { r: 3, c: 7 }, { r: 4, c: 4 }, { r: 5, c: 1 }, { r: 6, c: 6 }, { r: 7, c: 3 }, { r: 8, c: 8 }]
    },
    {
        size: 10,
        map: [
            [3, 3, 3, 8, 8, 8, 2, 2, 2, 2],
            [3, 3, 3, 8, 8, 2, 2, 2, 2, 0],
            [3, 5, 5, 8, 8, 7, 7, 2, 0, 0],
            [5, 5, 5, 9, 9, 7, 7, 0, 0, 0],
            [3, 9, 9, 9, 9, 7, 7, 0, 0, 0],
            [3, 9, 9, 1, 1, 7, 4, 4, 4, 6],
            [3, 3, 9, 1, 1, 1, 4, 4, 4, 6],
            [3, 3, 3, 1, 1, 4, 4, 4, 6, 6],
            [3, 3, 3, 1, 4, 4, 4, 6, 6, 6],
            [3, 3, 3, 1, 4, 6, 6, 6, 6, 6]
        ],
        solution: [{ r: 0, c: 3 }, { r: 1, c: 7 }, { r: 2, c: 1 }, { r: 3, c: 9 }, { r: 4, c: 5 }, { r: 5, c: 0 }, { r: 6, c: 2 }, { r: 7, c: 4 }, { r: 8, c: 6 }, { r: 9, c: 8 }]
    },
    {
        size: 11,
        map: [
            [7, 7, 2, 2, 2, 2, 5, 5, 5, 5, 5],
            [7, 7, 2, 2, 2, 5, 5, 5, 5, 5, 8],
            [0, 0, 2, 2, 3, 3, 6, 6, 8, 8, 8],
            [0, 0, 0, 3, 3, 3, 6, 6, 8, 8, 8],
            [0, 1, 1, 3, 3, 3, 6, 6, 9, 9, 9],
            [1, 1, 1, 3, 4, 4, 6, 6, 9, 9, 9],
            [1, 1, 1, 4, 4, 4, 6, 6, 9, 9, 9],
            [1, 1, 4, 4, 4, 4, 7, 7, 7, 9, 10],
            [1, 1, 4, 4, 4, 4, 7, 7, 7, 10, 10],
            [1, 1, 4, 4, 7, 7, 7, 7, 7, 10, 10],
            [1, 1, 4, 4, 7, 7, 7, 7, 10, 10, 10]
        ],
        solution: [{ r: 0, c: 2 }, { r: 1, c: 5 }, { r: 2, c: 8 }, { r: 3, c: 0 }, { r: 4, c: 3 }, { r: 5, c: 6 }, { r: 6, c: 9 }, { r: 7, c: 1 }, { r: 8, c: 4 }, { r: 9, c: 7 }, { r: 10, c: 10 }]
    },
    {
        size: 12,
        map: [
            [4, 4, 0, 0, 0, 1, 1, 1, 2, 2, 2, 2],
            [4, 4, 4, 0, 0, 1, 1, 1, 2, 2, 2, 2],
            [4, 4, 4, 5, 5, 5, 1, 2, 2, 2, 3, 3],
            [8, 4, 4, 5, 5, 5, 6, 6, 2, 2, 3, 3],
            [8, 4, 4, 5, 5, 5, 6, 6, 7, 7, 3, 3],
            [8, 8, 5, 5, 5, 6, 6, 6, 7, 7, 3, 3],
            [8, 8, 9, 9, 9, 6, 6, 6, 7, 7, 7, 3],
            [8, 8, 9, 9, 9, 10, 10, 7, 7, 7, 7, 7],
            [8, 8, 9, 9, 9, 10, 10, 10, 11, 11, 7, 7],
            [8, 8, 9, 9, 9, 10, 10, 10, 11, 11, 11, 11],
            [8, 8, 9, 10, 10, 10, 10, 10, 11, 11, 11, 11],
            [8, 8, 10, 10, 10, 10, 10, 11, 11, 11, 11, 11]
        ],
        solution: [{ r: 0, c: 2 }, { r: 1, c: 5 }, { r: 2, c: 8 }, { r: 3, c: 11 }, { r: 4, c: 1 }, { r: 5, c: 4 }, { r: 6, c: 7 }, { r: 7, c: 10 }, { r: 8, c: 0 }, { r: 9, c: 3 }, { r: 10, c: 6 }, { r: 11, c: 9 }]
    }
];

let SIZE = 8, grid = [], solution = [], chronoInterval = null, secondes = 0;

// =============================================================================
// --- SOLVEUR LOCAL (BACKUP / SECURITE) ---
// =============================================================================
function extraireCartesCouleurs(g, size) {
    return Array.from({ length: size }, (_, i) =>
        Array.from({ length: size }, (_, j) => g[i][j].colorId)
    );
}

function _countSolutions(colorMap, size, row, colUsed, colorUsed, carPositions, maxSolutions) {
    if (row === size) return 1;
    let count = 0;

    for (let col = 0; col < size; col++) {
        if (colUsed[col]) continue;
        const colorId = colorMap[row][col];
        if (colorUsed[colorId]) continue;

        let adjacent = false;
        for (const [pr, pc] of carPositions) {
            if (Math.abs(pr - row) <= 1 && Math.abs(pc - col) <= 1) {
                adjacent = true;
                break;
            }
        }
        if (adjacent) continue;

        colUsed[col] = true;
        colorUsed[colorId] = true;
        carPositions.push([row, col]);

        count += _countSolutions(colorMap, size, row + 1, colUsed, colorUsed, carPositions, maxSolutions);

        carPositions.pop();
        colUsed[col] = false;
        colorUsed[colorId] = false;

        if (count >= maxSolutions) return count;
    }
    return count;
}

function aUneSolutionUnique(g, size) {
    const colorMap = extraireCartesCouleurs(g, size);
    for (let i = 0; i < size; i++)
        for (let j = 0; j < size; j++)
            if (colorMap[i][j] === -1) return false;

    const colUsed = new Array(size).fill(false);
    const colorUsed = new Array(size).fill(false);
    const carPositions = [];

    const count = _countSolutions(colorMap, size, 0, colUsed, colorUsed, carPositions, 2);
    return count === 1;
}

// =============================================================================
// --- RENDU DE LA GRILLE ---
// =============================================================================
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

            td.dataset.r = i;
            td.dataset.c = j;
            td.addEventListener('click', onCellClick);
            tr.appendChild(td);
        }
        table.appendChild(tr);
    }
}

// =============================================================================
// --- MOTEUR DE GÉNÉRATION (Appel Externe C) ---
// =============================================================================
function lancerAleatoire() {
    const msg = document.getElementById('msg');
    const sizeSelect = document.getElementById('size-select');
    const chosenSize = sizeSelect ? sizeSelect.value : 8;

    if (msg) msg.textContent = "Le moteur C génère un circuit unique... 🏎️";

    fetch(`get_random_level.php?size=${chosenSize}`)
        .then(r => r.json())
        .then(data => {
            console.log("Données reçues du C:", data);

            SIZE = parseInt(data.size);
            grid = [];
            for (let i = 0; i < SIZE; i++) {
                grid[i] = [];
                for (let j = 0; j < SIZE; j++) {
                    let char = data.map_data[i * SIZE + j];
                    let colorId;
                    if (char >= '0' && char <= '9') {
                        colorId = parseInt(char);
                    } else {
                        colorId = char.charCodeAt(0) - 55; // A=10, B=11...
                    }
                    grid[i][j] = { colorId: colorId, hasCar: false, hasX: false };
                }
            }
            renderGrid();
            startChrono();
        })
        .catch(err => {
            console.error("Generation error:", err);
            if (msg) msg.textContent = "⚠️ Generation engine error.";
        });
}

// Optionnel: Gardé en backup JS au cas où
function initGrid(size) {
    return Array.from({ length: size }, () =>
        Array.from({ length: size }, () => ({ colorId: -1, hasCar: false, hasX: false }))
    );
}

// =============================================================================
// --- LOGIQUE DE JEU ---
// =============================================================================
function safeligne(g, r, c, size) {
    for (let j = 0; j < size; j++) if (g[r][j].hasCar) return false;
    for (let i = 0; i < size; i++) if (g[i][c].hasCar) return false;
    return true;
}

// Ajout pour le mode de récupération DB du collègue
function loadLevelFromDB(id) {
    console.log("Chargement du niveau depuis la BDD ID:", id);
    // Logique à implémenter si nécessaire pour fetch la bdd
}

function emptyregion(g, r, c, size) {
    const col = g[r][c].colorId;
    for (let i = 0; i < size; i++)
        for (let j = 0; j < size; j++)
            if (!(i === r && j === c) && g[i][j].colorId === col && g[i][j].hasCar) return false;
    return true;
}

function safearound(g, r, c, size) {
    for (let di = -1; di <= 1; di++)
        for (let dj = -1; dj <= 1; dj++) {
            if (!di && !dj) continue;
            const ni = r + di, nj = c + dj;
            if (ni >= 0 && ni < size && nj >= 0 && nj < size && g[ni][nj].hasCar) return false;
        }
    return true;
}

function estPlacable(g, r, c, size) {
    return safeligne(g, r, c, size) && emptyregion(g, r, c, size) && safearound(g, r, c, size);
}

function verifierVictoire(g, size) {
    let n = 0;
    for (let i = 0; i < size; i++)
        for (let j = 0; j < size; j++)
            if (g[i][j].hasCar) n++;
    return n === size;
}

function afficherVictory(points, dejaFait = false) {
    const overlay  = document.getElementById('victoryOverlay');
    const timeEl   = document.getElementById('victoryTime');
    const pointsEl = document.getElementById('victoryPoints');

    const minutes = String(Math.floor(secondes / 60)).padStart(2, '0');
    const secs    = String(secondes % 60).padStart(2, '0');

    timeEl.textContent   = `Time : ${minutes}:${secs}`;
    if (dejaFait) {
        pointsEl.textContent = '⚑ Already completed — no bonus points';
        pointsEl.classList.add('already-done');
        pointsEl.classList.remove('new-points');
    } else {
        pointsEl.textContent = `+${points} points`;
        pointsEl.classList.add('new-points');
        pointsEl.classList.remove('already-done');
    }
        overlay.classList.add('active');
    }

function gererFinDePartie() {
    clearInterval(chronoInterval);
    const msg = document.getElementById('msg');

    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get('type');
    const idJS = parseInt(urlParams.get('id') ?? '-1');
    const id_niveau = (type === 'fixed' && idJS >= 0) ? idJS + 1 : 0;
    const difficulte = id_niveau > 0
        ? [1, 2, 3, 4, 5][idJS] ?? 1
        : (SIZE <= 5 ? 1 : SIZE <= 6 ? 2 : SIZE <= 7 ? 3 : SIZE <= 8 ? 4 : 5);

    // Sending global scores
    fetch('../pages/save_score.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=save_score&difficulte=${difficulte}&chrono=${secondes}&id_niveau=${id_niveau}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            afficherVictory(data.points ?? data.base ?? 0);
        } else {
            afficherVictory(0, data.deja_fait ?? false);
        }
        const msg = document.getElementById('msg');
        msg.className = 'win';
    })
    .catch(() => {
        afficherVictory(0, false);
    });

    // Envoi des chronos mode histoire
    if (type === 'fixed' && idJS >= 0) {
        fetch('../pages/save_chrono.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id_niveau=${id_niveau}&chrono=${secondes}`
        })
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('.leaderboard-table tbody');
            if (!tbody) return;

            if (tbody.innerHTML.includes('colspan="3"')) {
                tbody.innerHTML = '';
            }

            const rows = Array.from(tbody.querySelectorAll('tr'));
            const scoresExistants = rows.map(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length < 3) return null;
                const tParts = cells[2].textContent.split(':');
                return {
                    element: row,
                    username: cells[1].textContent,
                    chrono: parseInt(tParts[0]) * 60 + parseInt(tParts[1])
                };
            }).filter(x => x !== null);

            scoresExistants.push({ username: "Me", chrono: secondes, nouveau: true });
            scoresExistants.sort((a, b) => a.chrono - b.chrono);

            tbody.innerHTML = '';
            scoresExistants.slice(0, 5).forEach((item, index) => {
                const rowMin = String(Math.floor(item.chrono / 60)).padStart(2, '0');
                const rowSec = String(Math.floor(item.chrono % 60)).padStart(2, '0');
                
                const tr = document.createElement('tr');
                if (item.nouveau) {
                    tr.style.backgroundColor = 'rgba(255, 69, 0, 0.2)';
                    tr.style.fontWeight = 'bold';
                }
                
                tr.innerHTML = `
                    <td>#${index + 1}</td>
                    <td>${item.nouveau ? 'Me' : item.pseudo}</td>
                    <td>${rowMin}:${rowSec}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => console.error("Error saving leaderboard:", err));
    }
}

function onCellClick(e) {
    const r = +e.currentTarget.dataset.r;
    const c = +e.currentTarget.dataset.c;
    const tile = grid[r][c];

    if (tile.hasCar) {
        tile.hasCar = false;
        tile.hasX = true;
    } else if (tile.hasX) {
        tile.hasX = false;
    } else {
        if (estPlacable(grid, r, c, SIZE)) {
            tile.hasCar = true;
        } else {
            const exclamation = document.createElement('span');
            exclamation.textContent = '!';
            exclamation.style.color = '#ff3333';
            exclamation.style.fontSize = '28px';
            exclamation.style.fontWeight = 'bold';
            exclamation.style.position = 'absolute';
            exclamation.style.top = '50%';
            exclamation.style.left = '50%';
            exclamation.style.transform = 'translate(-50%, -50%)';
            exclamation.style.zIndex = '10';

            e.currentTarget.appendChild(exclamation);
            e.currentTarget.classList.add('error-flash');

            setTimeout(() => e.currentTarget.classList.remove('error-flash'), 400);
            setTimeout(() => exclamation.remove(), 1000);
            return;
        }
    }

    renderGrid();

    if (verifierVictoire(grid, SIZE)) {
        gererFinDePartie();
    }
}

function startChrono() {
    clearInterval(chronoInterval);
    secondes = 0;
    chronoInterval = setInterval(() => {
        secondes++;
        const m = String(Math.floor(secondes / 60)).padStart(2, '0');
        const s = String(secondes % 60).padStart(2, '0');
        document.getElementById('chrono').textContent = `${m}:${s}`;
    }, 1000);
}

// =============================================================================
// --- HINT MANAGEMENT ---
// =============================================================================
document.addEventListener('DOMContentLoaded', () => {
    const btnHint = document.getElementById('btn-hint');
    if (!btnHint) return;

    btnHint.addEventListener('click', () => {
        const msg = document.getElementById('msg');
        msg.textContent = "Searching for a clue...🔍";

        let mapString = "";
        let carsString = "";

        for (let i = 0; i < SIZE; i++) {
            for (let j = 0; j < SIZE; j++) {
                let id = grid[i][j].colorId;
                mapString += (id < 10) ? id.toString() : String.fromCharCode(65 + (id - 10));
                carsString += grid[i][j].hasCar ? "1" : "0";
            }
        }

        let fd = new FormData();
        fd.append('size', SIZE);
        fd.append('map', mapString);
        fd.append('cars', carsString);

        fetch('hint.php', { method: 'POST', body: fd })
            .then(response => {
                if (!response.ok) throw new Error("Server error");
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    msg.textContent = "💡 " + data.error;
                    return;
                }

                if (data.r !== undefined && data.c !== undefined) {
                    const hR = parseInt(data.r);
                    const hC = parseInt(data.c);
                    const hColor = grid[hR][hC].colorId;

                    for (let i = 0; i < SIZE; i++) {
                        for (let j = 0; j < SIZE; j++) {
                            if (i === hR && j === hC) continue;
                            if (grid[i][j].hasCar) {
                                let conflit = false;
                                if (i === hR || j === hC) conflit = true;
                                if (grid[i][j].colorId === hColor) conflit = true;
                                if (Math.abs(i - hR) <= 1 && Math.abs(j - hC) <= 1) conflit = true;

                                if (conflit) {
                                    grid[i][j].hasCar = false;
                                    grid[i][j].hasX = true;
                                }
                            }
                        }
                    }

                    grid[hR][hC].hasCar = true;
                    grid[hR][hC].hasX = false;

                    renderGrid();
                    msg.innerHTML = `💡 <span style="color: #ff8c00;">Hint:</span> Car placed at <b>Row ${hR}, Column ${hC}</b> !`;
                    
                    if (verifierVictoire(grid, SIZE)) {
                        gererFinDePartie();
                    }
                }
            })
            .catch(err => {
                console.error("Error fetching hint:", err);
                msg.textContent = "Le solveur est indisponible.";
            });
    });
});