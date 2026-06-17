<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    session_start();
    include('../backend/bdd.php');
    
    // Vérification de l'authentification
    if(!isset($_SESSION["id_profile"])){
        echo "Vous devez être connecté ou inscrit pour voir vos favoris.<br>";
        exit();
    }
    
    // Récupération des favoris de l'utilisateur avec les photos associées
    $req = $bdd->prepare('SELECT A.id_annonce, A.nom_annonce, A.prix, P.url_photo
                          FROM annonces A, favoris F, photo P
                          WHERE F.id_annonce = A.id_annonce
                          AND A.id_annonce = P.id_annonce
                          AND F.id_profile = :id_profile
    ');

    $req->execute([':id_profile' => $_SESSION['id_profile']]);
    $favoris = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Mes Favoris</h1>

    <?php if(count($favoris) > 0): ?>
        <div class="favoris-container">
            <?php foreach ($favoris as $favori): ?>
                <div class="favori-card">
                    <a href="detail.php?id_annonce=<?= $favori['id_annonce'] ?>" style="text-decoration: none; color: inherit;">
                        
                        <img src="../images/<?= htmlspecialchars($favori['url_photo']) ?>" alt="Image de l'annonce">
                        <h2><?= htmlspecialchars($favori['nom_annonce']) ?></h2>
                        <p>Prix : <?= htmlspecialchars($favori['prix']) ?> €</p>
                    </a>
                    <div class="actions">
                        <a href="supprimeFavoris.php?id_annonce=<?= $favori['id_annonce'] ?>">Supprimer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
            <a href="index.php"  class="retour-annonces">Retour aux annonces</a>

    <?php else: ?>
        <p class="empty-message">Vous n'avez aucun favori pour le moment.</p>
    <?php endif; ?>
</body>
</html>

<style>
/* Styles généraux */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
}

/* Titre principal */
h1 {
    font-size: 28px;
    color: #2c3e50;
    text-align: center;
    margin: 20px 0;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: bold;
    background: linear-gradient(90deg, #3498db, #2ecc71);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Conteneur des favoris */
.favoris-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Colonnes flexibles */
    gap: 20px;
    width: 90%;
    max-width: 1200px;
    margin: 20px auto;
}

/* Carte de favori */
.favori-card {
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    text-align: center;
    padding: 15px;
}

.favori-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

/* Image de l'annonce */
/* Image de l'annonce */
.favori-card img {
    width: 200px; /* Largeur fixe */
    height: auto; /* Hauteur proportionnelle */
    border-bottom: 1px solid #ddd;
    margin-bottom: 10px;
    border-radius: 8px; /* Coins arrondis pour un style uniforme */
    object-fit: cover; /* Assure que l'image s'adapte bien */
}
/* Titre de l'annonce */
.favori-card h2 {
    font-size: 18px;
    color: #2c3e50;
    margin: 10px 0;
}

/* Prix */
.favori-card p {
    font-size: 16px;
    color: #16a085;
    font-weight: bold;
    margin: 5px 0;
}

/* Bouton de suppression ou autres actions */
.favori-card .actions {
    margin-top: 10px;
}

.favori-card .actions a {
    text-decoration: none;
    color: #e74c3c;
    font-size: 14px;
    font-weight: bold;
    transition: color 0.3s ease;
}

.favori-card .actions a:hover {
    color: #c0392b;
}

/* Message vide */
.empty-message {
    font-size: 18px;
    color: #7f8c8d;
    text-align: center;
    margin-top: 50px;
}

/* Responsive design */
@media (max-width: 768px) {
    .favoris-container {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    h1 {
        font-size: 24px;
    }
}
.retour-annonces {
    display: block;
    margin: 40px auto 0 auto;
    width: fit-content;
    padding: 12px 28px;
    background: linear-gradient(90deg, #3498db, #2ecc71);
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    border-radius: 25px;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(52,152,219,0.08);
    transition: background 0.3s, transform 0.2s;
    text-align: center;
    letter-spacing: 1px;
}
.retour-annonces:hover {
    background: linear-gradient(90deg, #2ecc71, #3498db);
    transform: translateY(-2px) scale(1.04);
    color: #fff;
}
</style>