<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('../backend/bdd.php');

// Récupère et sécurise la chaîne de recherche depuis l'URL
$recherche = isset($_GET['query']) ? htmlspecialchars(trim($_GET['query'])) : '';

// Requête SQL de base
$sql = '
    SELECT 
        A.id_annonce, 
        A.nom_annonce, 
        A.marque, 
        A.prix, 
        A.statut, 
        A.id_profile,
        MIN(P.url_photo) AS url_photo
    FROM annonces A
    LEFT JOIN photo P ON A.id_annonce = P.id_annonce
    WHERE 1=1
';

$params = [];

// Ajout d’un filtre si une recherche est effectuée
if (!empty($recherche)) {
    $sql .= ' AND (A.nom_annonce LIKE ? OR A.marque LIKE ?)';
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

// Regroupement des résultats par annonce
$sql .= '
    GROUP BY 
        A.id_annonce, 
        A.nom_annonce, 
        A.marque, 
        A.prix, 
        A.statut,
        A.id_profile
';

// Préparation et exécution de la requête
$requete = $bdd->prepare($sql);
$requete->execute($params);
$annonces = $requete->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechDeal</title>
  <link rel="stylesheet" href="styles.css">
  <script defer src="jali.js"></script>
  <link rel="icon" href="favicon.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<header class="header">
  <div class="header-left">
    <img src="raw.png" alt="Logo TechDEAL" class="logo-img">
  </div>
  <div class="header-center">
    <form action="index.php" method="GET" class="search-bar-modern">
      <input type="text" name="query" placeholder="Que cherchez-vous ?" value="<?= htmlspecialchars($recherche) ?>" />
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
  </div>
</header>

<main class="ads-section">
  <h2>Dernières annonces</h2>

  <div id="productGrid" class="product-grid">
    <?php if (count($annonces) > 0): ?>
      <?php foreach ($annonces as $annonce): ?>
        <div class="annonce">
          <a href="detail.php?id_annonce=<?= $annonce['id_annonce'] ?>" style="text-decoration: none; color: inherit;">
            <img src="../images/<?= !empty($annonce['url_photo']) ? htmlspecialchars($annonce['url_photo']) : 'default.jpg' ?>" width="200" alt="Image de l'annonce" class="product-img-custom">
            <div class="product-info-custom">
              <p class="product-brand">Marque : <?= htmlspecialchars($annonce['marque']) ?></p>
              <p class="product-model">Série : <?= htmlspecialchars($annonce['nom_annonce']) ?></p>
              <p class="product-condition">Prix : <?= htmlspecialchars($annonce['prix']) ?> €</p>
              <p class="product-condition">Statut : <?= htmlspecialchars($annonce['statut'] ?? 'Non défini') ?></p>
            </div>
          </a>
<?php $auteur1 = isset($_SESSION["id_profile"]) && $_SESSION["id_profile"] != $annonce['id_profile']; ?>

<div class="product-actions">
  <?php if ($auteur1): ?>
    <a href="../backend/messagerie.php?id_annonce=<?= $annonce['id_annonce'] ?>&id_destinataire=<?= $annonce['id_profile'] ?>" class="btn">
        Contacter
    </a>
  <?php endif; ?>
  <button class="btn-heart" data-id="<?= $annonce['id_annonce'] ?>">
    <i class="fas fa-heart"></i>
  </button>
</div>

        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Aucune annonce ne correspond à vos critères.</p>
    <?php endif; ?>
  </div>
</main>

<footer>
  <div class="footer-top">
    <a href="../backend/reception.php" class="footer-btn" id="footerSearchBtn">Mes messages</a>
    <a href="liste_favoris.php" class="footer-btn" id="favorisBtn">Favoris</a>
    <a href="ajouter_annonce.php" class="footer-btn">Publier</a>
    <!-- <button class="footer-btn">Messages</button> -->
  </div>
</footer>

<div class="floating-menu-btn" id="menuBtn"><i class="fas fa-bars"></i></div>

<div id="dropdownMenu" class="dropdown-menu hidden">
  <ul>
    <li id="darkModeToggle">🌙 Mode sombre</li>
    <li id="helpOption">❓ Aide</li>
  </ul>
</div>

<div class="floating-user-btn" id="userMenu">
  <i class="fas fa-user"></i>
  <div id="userDropdown" class="dropdown-menu hidden">
    <a href="ajouter_annonce.php">Ajouter une annonce</a>
    <?php if (!isset($_SESSION['id_profile'])): ?>
      <a href="connexion.php">Se connecter</a>
      <a href="inscription.php">Créer un compte</a>
    <?php else: ?>
      <a href="deconnexion.php">Déconnexion</a>
    <?php endif; ?>
  </div>
</div>
<script>
document.querySelectorAll('.btn-heart').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const id_annonce = this.getAttribute('data-id');
        fetch('favoris.php?id_annonce=' + id_annonce)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.querySelector('i').style.color = "#e74c3c";
                }
            });
    });
});
</script>
</body>
</html>
<style>
  .btn {
    display: inline-block;
    background: linear-gradient(90deg, #667eea 0%, #43e97b 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 12px 28px;
    font-size: 17px;
    font-weight: bold;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    box-shadow: 0 2px 8px #6c63ff33;
    transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
    margin: 8px 0;
}
.btn:hover {
    background: linear-gradient(90deg, #43e97b 0%, #667eea 100%);
    color: #fff;
    transform: translateY(-2px) scale(1.04);
    box-shadow: 0 4px 16px #43e97b55;
}
</style>