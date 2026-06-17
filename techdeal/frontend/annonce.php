<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    session_start();
    include('../backend/bdd.php');

    // Récupération des filtres
    $statut = isset($_GET['statut']) ? htmlspecialchars($_GET['statut']) : '';
    $marque = isset($_GET['marque']) ? htmlspecialchars($_GET['marque']) : '';
    
    // Requête de base
    $sql = '
        SELECT A.id_annonce, A.nom_annonce, A.marque, A.prix, A.id_profile, 
               COUNT(P.id_photo) AS nb_photos, 
               MAX(P.url_photo) AS url_photo
        FROM annonces A
        LEFT JOIN photo P ON A.id_annonce = P.id_annonce
        WHERE 1=1
    ';

    $params = [];

    // Ajout des filtres
    if(!empty($statut)){
        $sql .= ' AND A.statut = ?';
        $params[] = $statut;
    }
    if(!empty($marque)){
        $sql .= ' AND A.marque LIKE ?';
        $params[] = "%$marque%";
    }

    // Groupement par annonce pour compter les photos
    $sql .= ' GROUP BY A.id_annonce, A.nom_annonce, A.marque, A.prix, A.id_profile';

    // Exécution de la requête
    $req = $bdd->prepare($sql);
    $req->execute($params);
    $annonces = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des annonces</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <h1>Liste des annonces</h1>

    <!-- Formulaire de filtrage -->
    <form method="GET" action="annonce.php">
        <label>Marque :</label>
        <input type="text" name="marque" value="<?= htmlspecialchars($marque) ?>">

        <label>État :</label>
        <select name="statut">
            <option value="" disabled <?= empty($statut) ? 'selected' : '' ?>>FILTRER</option>
            <?php
            $req = $bdd->query("SELECT DISTINCT statut FROM annonces");
            $statuts = $req->fetchAll();
            foreach ($statuts as $statutOption) {
                $selected = ($statutOption['statut'] == $statut) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($statutOption['statut']) . "' $selected>" . htmlspecialchars($statutOption['statut']) . "</option>";
            }
            ?>
        </select>

        <input type="submit" value="Filtrer">
    </form>

    <!-- Affichage des annonces -->
    <?php if (count($annonces) > 0): ?>
        <?php foreach ($annonces as $annonce): ?>
            <div class="annonce">
                <a href="detail.php?id_annonce=<?= $annonce['id_annonce'] ?>" style="text-decoration: none; color: inherit;">
                    <?php if (!empty($annonce['url_photo'])): ?>
                        <img src="images/<?= htmlspecialchars($annonce['url_photo']) ?>" width="200" alt="Image de l'annonce">
                    <?php else: ?>
                        <img src="images/default.jpg" width="200" alt="Image par défaut">
                    <?php endif; ?>
                    <h3>Marque : <?= htmlspecialchars($annonce['marque']) ?></h3>
                    <p>Nom : <?= htmlspecialchars($annonce['nom_annonce']) ?></p>
                    <p>Prix : <?= htmlspecialchars($annonce['prix']) ?> €</p>
                    <p>Nombre de photos : <?= htmlspecialchars($annonce['nb_photos']) ?></p>
                </a>
                <a href="detail.php?id_annonce=<?= $annonce['id_annonce'] ?>">Voir les détails</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune annonce ne correspond à vos critères.</p>
    <?php endif; ?>
</body>
</html>