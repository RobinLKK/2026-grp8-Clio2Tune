<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2Fast4U - Level Editor</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/editor.css">
    <link rel="icon" href="../media/ico-car.ico" type="image/x-icon">
</head>

<body>

<header>
    <a href="index.php" class="logo">
        <img src="../media/2fast.png" alt="2Fast4U" style="height: 40px;">
    </a> 

    <nav>
        <a href="index.php">Home</a>
        <a href="leaderboard.php">Leaderboard</a>

        <?php if (isset($_SESSION["pseudo"])): ?>
            <a href="profile.php"><?= htmlspecialchars($_SESSION["pseudo"]) ?></a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<main class="editor-container">

    <section class="editor-left">

        <h2>LEVEL EDITOR</h2>

        <p class="subtitle">
            Build your own racing puzzle track.
        </p>

        <div class="editor-options">

            <div class="option-card">
                <h3>Grid Size</h3>

                <select id="gridSize">
                    <option value="5">5 x 5</option>
                    <option value="6">6 x 6</option>
                    <option value="7">7 x 7</option>
                    <option value="8">8 x 8</option>
                    <option value="9">9 x 9</option>
                    <option value="10">10 x 10</option>
                </select>
            </div>

            <div class="option-card">
                <h3>Difficulty</h3>

                <select>
                    <option>Easy</option>
                    <option>Medium</option>
                    <option>Hard</option>
                </select>
            </div>
            <div class="option-card">
                <h3>Cell Color</h3>
                <select id="cellColor">
                    <option value="#243b67">Blue</option>
                    <option value="#eeff00">yellow</option>
                    <option value="#2e8b57">Green</option>
                    <option value="#b22222">Red</option>
                    <option value="#555555">Gray</option>
                    <option value="#9f02befb">Purple</option>
                    <option value="#00ffff">Cyan</option>
                </select>
            </div>

        </div>

        <div class="editor-buttons">
            <button class="btn-outline">Save</button>
            <button class="btn-reset" id="resetGrid">Reset</button>
        </div>

    </section>

    <section class="editor-right">

        <div class="grid-preview" id="gridPreview"></div>
            <script>
                const gridPreview = document.getElementById("gridPreview");
                const gridSize = document.getElementById("gridSize");
                const cellColor = document.getElementById("cellColor");

                function createGrid(size)
                {
                    gridPreview.innerHTML = "";

                    gridPreview.style.gridTemplateColumns = `repeat(${size}, 1fr)`;

                    for(let i = 0; i < size * size; i++)
                    {
                        const cell = document.createElement("div");

                        cell.classList.add("cell");
                        cell.addEventListener("click", () =>
                        {
                            cell.style.background = cellColor.value;
                        });
                        gridPreview.appendChild(cell);
                    }
                }
                gridSize.addEventListener("change", () =>
                {
                    createGrid(gridSize.value);
                });
                const resetGrid = document.getElementById("resetGrid");
                resetGrid.addEventListener("click", () =>
                {
                    createGrid(gridSize.value);
                });
                createGrid(5);

            </script>

    </section>

</main>
<footer>
    <p>© 2Fast4U • Racing Queen's Game</p>
</footer>
</body>
</html>