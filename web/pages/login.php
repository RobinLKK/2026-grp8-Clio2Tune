<?php
session_start();
require_once '../includes/db.php';

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pseudo = trim($_POST["pseudo"]);
    $mdp    = trim($_POST["mot_de_passe"]);

    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE Pseudo = ?");
    $stmt->execute([$pseudo]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user["Mot_de_passe"])) {
        $_SESSION["user_id"] = $user["ID"];
        $_SESSION["pseudo"]  = $user["Pseudo"];
        header("Location: index.php");
        exit;
    } else {
        $erreur = "Pseudo ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>2Fast4U – Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../media/favicon.ico">
</head>
<body>
    <header>
    <a href="index.php" class="logo">2Fast4U</a>
    <nav>
        <a href="index.php">Home</a>
        <a href="leaderboard.php">Leaderboard</a>
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
    <div style="
      background: rgba(10,15,30,0.82);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(226,185,111,0.2);
      padding: 2.5rem 2.5rem 2rem;
      width: 100%;
      max-width: 380px;
      font-family: 'Barlow', sans-serif;
    ">
      <h2 style="
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.8rem;
        letter-spacing: 0.15em;
        color: #e2b96f;
        margin-bottom: 0.3rem;
      ">Connexion</h2>
      <p style="font-size:0.75rem; letter-spacing:0.1em; color:rgba(240,236,227,0.4); text-transform:uppercase; margin-bottom:1.8rem;">
        Bienvenue sur 2Fast4U
      </p>

      <?php if ($erreur): ?>
        <p style="
          font-size:0.82rem; color:#fca5a5;
          background:rgba(239,68,68,0.1);
          border:1px solid rgba(239,68,68,0.25);
          padding:0.6rem 0.9rem;
          margin-bottom:1rem;
        "><?= htmlspecialchars($erreur) ?></p>
      <?php endif; ?>

      <form method="POST" style="display:flex; flex-direction:column; gap:1rem;">
        <div style="display:flex; flex-direction:column; gap:0.3rem;">
          <label style="font-size:0.7rem; font-weight:600; letter-spacing:0.2em; text-transform:uppercase; color:rgba(240,236,227,0.5);">
            Username
          </label>
          <input type="text" name="pseudo" required style="
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(226,185,111,0.2);
            color: #f0ece3;
            padding: 0.7rem 0.9rem;
            font-family: 'Barlow', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
          " onfocus="this.style.borderColor='rgba(226,185,111,0.5)'" onblur="this.style.borderColor='rgba(226,185,111,0.2)'">
        </div>

        <div style="display:flex; flex-direction:column; gap:0.3rem;">
          <label style="font-size:0.7rem; font-weight:600; letter-spacing:0.2em; text-transform:uppercase; color:rgba(240,236,227,0.5);">
            Password
          </label>
          <input type="password" name="mot_de_passe" required style="
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(226,185,111,0.2);
            color: #f0ece3;
            padding: 0.7rem 0.9rem;
            font-family: 'Barlow', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
          " onfocus="this.style.borderColor='rgba(226,185,111,0.5)'" onblur="this.style.borderColor='rgba(226,185,111,0.2)'">
        </div>

        <button type="submit" style="
          font-family: 'Bebas Neue', sans-serif;
          font-size: 1rem;
          letter-spacing: 0.2em;
          color: #1a1a2e;
          background: #e2b96f;
          border: none;
          padding: 0.75rem;
          cursor: pointer;
          margin-top: 0.5rem;
          transition: background 0.2s, transform 0.15s;
        " onmouseover="this.style.background='#c9a050'" onmouseout="this.style.background='#e2b96f'">
          Login
        </button>
      </form>

      <p style="margin-top:1.2rem; text-align:center; font-size:0.8rem; color:rgba(240,236,227,0.4);">
        No account ?
        <a href="register.php" style="color:#e2b96f; font-weight:600; text-decoration:none;">Register</a>
      </p>
    </div>
  </main>
  <footer>
    © <?= date('Y') ?> 2Fast4U - Login
  </footer>
</body>
</html>