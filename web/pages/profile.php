<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$erreur = "";
$succes = "";

/* ── Fetch user EN PREMIER ── */
$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE ID = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();

/* ── Traitement formulaire ── */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nouveau_pseudo = trim($_POST["pseudo"]);
    $email          = trim($_POST["email"]);
    $nouveau_mdp    = trim($_POST["mot_de_passe"]);
    $confirmer      = trim($_POST["confirmer"]);

    /* ── Upload avatar ── */
    $avatar_path = $user['avatar'] ?? null;

    if (!empty($_FILES['avatar']['name'])) {
        $ext     = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed) && $_FILES['avatar']['size'] < 2000000) {
            if (!is_dir('../media/avatars/')) {
                mkdir('../media/avatars/', 0755, true);
            }
            $filename    = 'avatar_' . $_SESSION['user_id'] . '.' . $ext;
            $destination = '../media/avatars/' . $filename;
            move_uploaded_file($_FILES['avatar']['tmp_name'], $destination);
            $avatar_path = 'media/avatars/' . $filename;
        } else {
            $erreur = "Image invalide (JPG/PNG/GIF, max 2Mo).";
        }
    }

    /* ── Validations ── */
    if (!$erreur) {
        $chk = $pdo->prepare("SELECT ID FROM utilisateur WHERE Pseudo = ? AND ID != ?");
        $chk->execute([$nouveau_pseudo, $_SESSION["user_id"]]);
        if ($chk->fetch()) {
            $erreur = "Ce pseudo est déjà pris.";
        } elseif ($nouveau_mdp && $nouveau_mdp !== $confirmer) {
            $erreur = "Les mots de passe ne correspondent pas.";
        }
    }

    /* ── Update BDD ── */
    if (!$erreur) {
        if ($nouveau_mdp) {
            $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $upd  = $pdo->prepare("UPDATE utilisateur SET Pseudo = ?, Email = ?, Mot_de_passe = ?, avatar = ? WHERE ID = ?");
            $upd->execute([$nouveau_pseudo, $email, $hash, $avatar_path, $_SESSION["user_id"]]);
        } else {
            $upd = $pdo->prepare("UPDATE utilisateur SET Pseudo = ?, Email = ?, avatar = ? WHERE ID = ?");
            $upd->execute([$nouveau_pseudo, $email, $avatar_path, $_SESSION["user_id"]]);
        }
        $_SESSION["pseudo"] = $nouveau_pseudo;
        $succes = "Profil mis à jour !";

        /* Refresh user après update */
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE ID = ?");
        $stmt->execute([$_SESSION["user_id"]]);
        $user = $stmt->fetch();
    }
}

/* ── Scores ── */
$stmt = $pdo->prepare("SELECT Points, Date FROM classement WHERE ID_utilisateur = ? ORDER BY Points DESC LIMIT 5");
$stmt->execute([$_SESSION["user_id"]]);
$scores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>2Fast4U – Profil</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="icon" href="../media/ico-car.ico">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>
            <a href="index.php" class="logo">
                <img src="../media/2fast.png" alt="2Fast4U" style="height: 40px;">
            </a>   
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

    <main class="auth-wrap">
        <div class="auth-box">

            <!-- Avatar affiché en haut -->
            <div class="profile-avatar-wrap">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="../<?= htmlspecialchars($user['avatar']) ?>"
                         alt="Avatar" class="profile-avatar-img">
                <?php else: ?>
                    <div class="profile-avatar-placeholder">👤</div>
                <?php endif; ?>
            </div>

            <h2 class="auth-box-title"><?= htmlspecialchars($user["Pseudo"]) ?></h2>

            <?php if ($erreur): ?>
                <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>
            <?php if ($succes): ?>
                <p class="succes"><?= htmlspecialchars($succes) ?></p>
            <?php endif; ?>

            <!-- Un seul form avec enctype -->
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Pseudo</label>
                    <input type="text" name="pseudo"
                           value="<?= htmlspecialchars($user["Pseudo"]) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email"
                           value="<?= htmlspecialchars($user["Email"] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Photo de profil</label>
                    <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif">
                </div>
                <div class="form-group">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="mot_de_passe"
                           placeholder="Laisser vide = inchangé">
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="confirmer">
                </div>
                <button type="submit" class="btn-submit">Sauvegarder</button>
            </form>

            <!-- Scores -->
            <div class="profile-scores">
                <h3 class="profile-scores-title">Meilleurs scores</h3>
                <?php if ($scores): ?>
                    <table>
                        <thead>
                            <tr><th>Points</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scores as $s): ?>
                                <tr>
                                    <td><?= $s["Points"] ?></td>
                                    <td><?= $s["Date"] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="profile-no-score">Aucun score pour l'instant.</p>
                <?php endif; ?>
            </div>

            <div class="profile-logout">
                <a href="logout.php" class="btn-submit btn-submit-outline">
                    Se déconnecter
                </a>
            </div>

        </div>
    </main>
</body>
</html>