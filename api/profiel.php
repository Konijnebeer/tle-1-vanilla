<?php
session_start();

require_once './includes/Database.php';

// Controleer of gebruiker is ingelogd
if (!isset($_SESSION['gebruiker_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION)) {
//    $_SESSION['gebruiker_id'] = 8;
    $gebruiker_id = $_SESSION['gebruiker_id'];

    // Haal PDO via de Database singleton
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $profiel = $db->fetch('SELECT email, username, phone_number, created_at, updated_at FROM users WHERE id = ?', [$gebruiker_id]);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Profielpagina</title>
</head>
<body>
<h1>Welkom op je profielpagina</h1>
<script src="profiel.js"></script>
<?php if ($profiel): ?>
    <p><strong>Gebruikersnaam:</strong> <?= htmlspecialchars($profiel['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($profiel['email']) ?></p>
    <p><strong>Telefoonnummer:</strong> <?= htmlspecialchars($profiel['phone_number']) ?></p>
    <p><strong>Aangemaakt op:</strong> <?= htmlspecialchars($profiel['created_at']) ?></p>
    <p><strong>Bijgewerkt op:</strong> <?= htmlspecialchars($profiel['updated_at']) ?></p>
    <p><a href="logout.php">Uitloggen</a></p>
<?php else: ?>
    <p>Geen profielgegevens gevonden.</p>
<?php endif; ?>
</body>
</html>