<?php
include('../backend/bdd.php');

// Récupérer la requête de recherche
$query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';

// Si une recherche est effectuée
if (!empty($query)) {
    $sql = 'SELECT * FROM annonces WHERE nom_annonce LIKE :query OR marque LIKE :query';
    $stmt = $bdd->prepare($sql);
    $stmt->execute(['query' => "%$query%"]);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $resultats = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Résultats de recherche</h1>
    <?php if (!empty($resultats)): ?>
        <ul>
            <?php foreach ($resultats as $resultat): ?>
                <li>
                    <a href="detail.php?id_annonce=<?= $resultat['id_annonce'] ?>">
                        <?= htmlspecialchars($resultat['nom_annonce']) ?> - <?= htmlspecialchars($resultat['marque']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun résultat trouvé pour "<?= htmlspecialchars($query) ?>".</p>
    <?php endif; ?>
</body>
</html>