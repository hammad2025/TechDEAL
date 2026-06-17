<?php
include('../backend/bdd.php');
session_start();

// Récupération de l'ID de l'annonce depuis l'URL
$ida = isset($_GET["id_annonce"]) ? (int)$_GET["id_annonce"] : null;

if (!$ida) {
    echo "Erreur : annonce introuvable.";
    exit();
}

// Requête SQL pour récupérer l'annonce et ses images
$req = $bdd->prepare('
    SELECT A.id_annonce, A.nom_annonce, A.marque, A.description, A.prix, A.statut, A.date_creation, A.id_profile, P.url_photo
    FROM annonces A
    LEFT JOIN photo P ON A.id_annonce = P.id_annonce
    WHERE A.id_annonce = :id_annonce
');
$req->execute(['id_annonce' => $ida]);
$annonces = $req->fetchAll(PDO::FETCH_ASSOC);

if (!$annonces) {
    echo "Erreur : annonce introuvable dans la base.";
    exit();
}

// Récupération des détails de l'annonce (première ligne)
$annonce = $annonces[0];

// Vérification que l'utilisateur connecté est l'auteur de l'annonce
$auteur = isset($_SESSION["id_profile"]) && $_SESSION["id_profile"] == $annonce['id_profile'];

// Vérification que l'utilisateur connecté n'est pas l'auteur
$auteur1 = isset($_SESSION["id_profile"]) && $_SESSION["id_profile"] != $annonce['id_profile'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="detail.css">
    <title>Détail de l'annonce</title>
</head>
<body>
<div class="container">
    <h2><?= htmlspecialchars($annonce['nom_annonce']) ?></h2>

    <!-- Affichage de la photo principale -->
    <?php
    $photoPrincipale = null;
    foreach ($annonces as $photo) {
        if (!empty($photo['url_photo'])) {
            $photoPrincipale = $photo['url_photo'];
            break; // On prend la première photo trouvée
        }
    }
    ?>
    <?php if ($photoPrincipale): ?>
        <div class="main-image-container">
            <img src="../images/<?= htmlspecialchars($photoPrincipale) ?>" alt="Photo principale de l'annonce" class="main-annonce-image">
        </div>
    <?php endif; ?>

    <!-- Affichage des autres images -->
    <div class="images-container">
        <?php foreach ($annonces as $photo): ?>
            <?php if (!empty($photo['url_photo']) && $photo['url_photo'] !== $photoPrincipale): ?>
                <img src="../images/<?= htmlspecialchars($photo['url_photo']) ?>" alt="Photo de l'annonce" class="annonce-image">
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <!-- <img src="../images/<?= htmlspecialchars($photo['url_photo']) ?>" alt="Photo de l'annonce" class="annonce-image"> -->
    <p><b>Date de publication :</b> <?= htmlspecialchars($annonce['date_creation']) ?></p>
    <p><b>Marque :</b> <?= htmlspecialchars($annonce['marque']) ?></p>
    <p><b>Description :</b> <?= nl2br(htmlspecialchars($annonce['description'])) ?></p>
    <p><b>État :</b> <?= htmlspecialchars($annonce['statut']) ?></p>
    <p><b>Prix :</b> <?= htmlspecialchars($annonce['prix']) ?> €</p>
    <a href="index.php" class="btn">Retour aux annonces</a>

<!-- Afficher le lien pour envoyer un message uniquement si ce n'est pas l'auteur de l'annonce -->
<?php if ($auteur1): ?>
    <a href="../backend/messagerie.php?id_annonce=<?= $annonce['id_annonce'] ?>&id_destinataire=<?= $annonce['id_profile'] ?>" class="btn">
        Envoyer un message concernant cette annonce
    </a>
<?php endif; ?>


    <!-- Afficher les liens de modification et suppression uniquement si l'utilisateur est l'auteur -->
    <?php if ($auteur): ?>
        <a href="modifierAnnonce.php?id_annonce=<?= $annonce['id_annonce'] ?>" class="btn">Modifier cette annonce</a>
        <a href="supprimeAnnonce.php?id_annonce=<?= $annonce['id_annonce'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">Supprimer cette annonce</a>
    <?php endif; ?>
</div>
</body>
</html>