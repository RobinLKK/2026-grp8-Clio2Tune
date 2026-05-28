<?php 
session_start();
require_once '../includes/db.php';

// --- LEADERBOARD DYNAMIQUE PAR NIVEAU (mode histoire uniquement) ---
$id_niveau_param = null;
$top5 = [];

$type_param = $_GET['type'] ?? '';
if ($type_param === 'fixed' && isset($_GET['id'])) {
    // JS est 0-indexé (0, 1, 2...), la BDD commence à 1
    $id_niveau_param = intval($_GET['id']) + 1; 

    $stmt = $pdo->prepare("
        SELECT u.Pseudo, s.Chrono
        FROM score s
        JOIN utilisateur u ON u.ID = s.ID_Utilisateur
        WHERE s.ID_Niveau = :niv
        ORDER BY s.Chrono ASC
        LIMIT 5
    ");
    $stmt->execute([':niv' => $id_niveau_param]);
    $top5 = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Play - 2Fast4U</title>
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
            align-items: flex-start;
            padding: 20px 0;
        }

        #grid {
            border-collapse: collapse;
            table-layout: fixed;
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
            background-size: 100% 100% !important;
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
            background-size: 100% 100% !important;
            z-index: 1;
            pointer-events: none;
            opacity: 0.8;
        }
        .cell.track-h::before { transform: rotate(90deg); }
        .cell.track-v::before { transform: rotate(0deg); }

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

        /* ----- GAME MIDDLE ----- */
        .game-middle {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 40px;
            flex-wrap: nowrap; 
            margin-top: 20px;
        }

        /* ----- LEADERBOARD BOX ----- */
        .leaderboard-box {
            background-color: #222;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            font-family: 'Barlow', sans-serif;
            color: white;
            min-width: 280px;
        }

        .leaderboard-box h3 {
            font-family: 'Bebas Neue', sans-serif;
            text-align: center;
            color: #ff4500; 
            font-size: 24px;
            letter-spacing: 2px;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
        }

        .leaderboard-table th, .leaderboard-table td {
            padding: 10px 12px;
            text-align: center;
            border-bottom: 1px solid #333;
        }

        .leaderboard-table th {
            color: #bbb;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
        }

        .leaderboard-table tr:nth-child(even) td {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .leaderboard-table tr:first-of-type td {
            color: #ffd700;
            font-weight: bold;
        }

        @media (max-width: 900px) {
            .game-middle {
                flex-wrap: wrap; 
                flex-direction: column;
                align-items: center;
            }
        }
        /* ── OVERLAY DE VICTOIRE ── */
.victory-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 120vw; height: 120vh;
    background: rgba(6, 10, 20, 0.55);
    backdrop-filter: blur(3px);
    display: none;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 99999;
}
.victory-overlay.active {
    display: flex !important;
    opacity: 1;
}
.victory-box {
    position: relative;
    background: linear-gradient(160deg, #0d1525 0%, #1a2a4a 50%, #0d1525 100%);
    border: 1px solid rgba(226,185,111,0.5);
    border-radius: 16px;
    padding: 50px 70px;
    text-align: center;
    box-shadow:
        0 0 0 1px rgba(226,185,111,0.1),
        0 0 40px rgba(226,185,111,0.08),
        0 30px 80px rgba(0,0,0,0.7);
    animation: popVictory 0.4s ease;
}
.victory-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 5rem;
    letter-spacing: 0.18em;
    color: #e2b96f;
    margin-bottom: 20px;
    text-shadow:
        0 0 20px rgba(226,185,111,0.6),
        0 0 60px rgba(226,185,111,0.2),
        0 4px 10px rgba(0,0,0,0.8);
}
.victory-time {
    font-family: 'Barlow', sans-serif;
    font-size: 1.4rem;
    color: #f0ece3;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(226,185,111,0.15);
}
.victory-points {
    font-family: 'Barlow', sans-serif;
    font-weight: bold;
    margin-bottom: 35px;
}

.victory-points.new-points {
    font-size: 2rem;
    color: #4dff88;
    text-shadow: 0 0 12px rgba(77,255,136,0.3);
}

.victory-points.already-done {
    font-size: 1rem;
    color: rgba(240,236,227,0.45);
    font-style: italic;
    letter-spacing: 0.05em;
}
.victory-btn {
    font-family: 'Barlow', sans-serif;
    padding: 14px 34px;
    border: none;
    border-radius: 12px;
    background: #e2b96f;
    color: #111;
    font-weight: bold;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.2s;
}
.victory-close {
    position: absolute;
    top: 14px; right: 18px;
    background: none;
    border: none;
    color: rgba(240,236,227,0.4);
    font-size: 1.2rem;
    cursor: pointer;
    transition: color 0.2s;
    line-height: 1;
}
.victory-close:hover { color: #e2b96f; }

.victory-btn:hover { transform: scale(1.05); background: #c9a050; }
@keyframes popVictory {
    0%   { transform: scale(0.7); opacity: 0; }
    100% { transform: scale(1);   opacity: 1; }
}
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
            <button id="btn-reset">Reset</button>
            <button id="btn-new">New level</button>
            <button id="btn-hint">Need a hint ?</button>
        </div>

        <div class="game-middle">
            <div id="game-wrap">
                <div id="size-row">
                    SELECT YOUR SIZE:
                    <select id="size-select">
                        <option value="5">5×5</option>
                        <option value="6">6×6</option>
                        <option value="7">7×7</option>
                        <option value="8" selected>8×8</option>
                        <option value="9">9×9</option>
                        <option value="10">10×10</option>
                        <option value="11">11×11</option>
                        
                    </select>
                </div>
                <div id="grid-container">
                    <table id="grid"></table>
                </div>
                <p id="msg"></p>
            </div>

            <?php if ($id_niveau_param !== null): ?>
            <div class="leaderboard-box">
                <h3>Best Time</h3>
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Username</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($top5)): ?>
                        <tr>
                            <td colspan="3" style="color:#888; font-style:italic;">
                                No scores yet... Be the first to set a record! 🚀
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $rang = 1; foreach ($top5 as $score):
                            $min = str_pad(floor($score['Chrono'] / 60), 2, '0', STR_PAD_LEFT);
                            $sec = str_pad($score['Chrono'] % 60, 2, '0', STR_PAD_LEFT);
                        ?>
                        <tr>
                            <td>#<?= $rang++ ?></td>
                            <td><?= htmlspecialchars($score['Pseudo']) ?></td>
                            <td><?= $min ?>:<?= $sec ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

        </div>
    </main>

    <script>
        function newGame() {
            const urlParams = new URLSearchParams(window.location.search);
            const type = urlParams.get('type');
            const id = urlParams.get('id');
            const btnNew = document.getElementById('btn-new');
            const sizeRow = document.getElementById('size-row');

            if (typeof lancerAleatoire !== 'function') {
                console.warn("game.js n'est pas encore prêt...");
                return;
            }

            if (type === 'db' && id !== null) {
                if(btnNew) btnNew.classList.add('hidden-mode');
                if(sizeRow) sizeRow.classList.add('hidden-mode');
                loadLevelFromDB(id); 
            } 
            else if (type === 'fixed' && id !== null) {
                if(btnNew) btnNew.classList.add('hidden-mode');
                if(sizeRow) sizeRow.classList.add('hidden-mode');
                
                if (typeof PREDEFINED_LEVELS !== 'undefined' && PREDEFINED_LEVELS[id]) {
                    const data = PREDEFINED_LEVELS[parseInt(id)];
                    SIZE = data.size;
                    grid = data.map.map(row => row.map(cid => ({ colorId: cid, hasCar: false, hasX: false })));

                    renderGrid();
                    startChrono();
                }
            } 
            else {
                if(btnNew) btnNew.classList.remove('hidden-mode');
                if(sizeRow) sizeRow.classList.remove('hidden-mode');
                lancerAleatoire(); 
            }
        }

        // On initialise les écouteurs une fois que tout le DOM et game.js sont chargés
        window.addEventListener('load', () => {
            console.log("Initialisation des contrôles de jeu...");

            const btnNew = document.getElementById('btn-new');
            const sizeSelect = document.getElementById('size-select');
            const btnReset = document.getElementById('btn-reset');

            if (btnNew) btnNew.addEventListener('click', lancerAleatoire);
            if (sizeSelect) sizeSelect.addEventListener('change', lancerAleatoire);

            if (btnReset) {
                btnReset.addEventListener('click', () => {
                    for(let i=0; i<SIZE; i++) {
                        for(let j=0; j<SIZE; j++) { 
                            grid[i][j].hasCar = false; 
                            grid[i][j].hasX = false; 
                        }
                    }
                    const msg = document.getElementById('msg');
                    if(msg) msg.textContent = "";
                    renderGrid();
                });
            }

            newGame();
        });

        window.addEventListener('resize', () => {
            if (typeof renderGrid === 'function') renderGrid();
            document.getElementById('victoryOverlay').addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('active');
        });
        });
    </script>
    <div id="victoryOverlay" class="victory-overlay">
    <div class="victory-box">
        <button class="victory-close" onclick="document.getElementById('victoryOverlay').classList.remove('active')">✕</button>
        <div class="victory-title">🏆 VICTORY 🏆</div>
        <div class="victory-time" id="victoryTime">Time : 00:00</div>
        <div class="victory-points" id="victoryPoints">+0 points</div>
        <button class="victory-btn" onclick="location.reload()">REPLAY</button>
    </div>
    </div>

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