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
            flex-wrap: nowrap; /* Force l'alignement côte à côte sur PC */
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

        /* Version Mobile / Écrans fins */
        @media (max-width: 900px) {
            .game-middle {
                flex-wrap: wrap; /* Aligné en dessous si pas assez de place */
                flex-direction: column;
                align-items: center;
            }
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
            <button id="btn-reset">Réinitialiser</button>
            <button id="btn-new">Nouveau niveau</button>
            <button id="btn-hint">Besoin d'un indice ?</button>
        </div>

        <div class="game-middle">
            <div id="game-wrap">
                <div id="size-row">
                    SÉLECTIONNE TA TAILLE :
                    <select id="size-select">
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

            <?php if ($id_niveau_param !== null): ?>
            <div class="leaderboard-box">
                <h3>Meilleurs Temps</h3>
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Pseudo</th>
                            <th>Temps</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($top5)): ?>
                        <tr>
                            <td colspan="3" style="color:#888; font-style:italic;">
                                Sois le premier à finir ce niveau !
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