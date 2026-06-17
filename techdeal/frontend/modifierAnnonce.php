<?php
    session_start();
    include('../backend/bdd.php');
     // Vérification de l'authentification
    if(!isset($_SESSION["id_profile"])){
        echo "Vous devez être auteur et connecté pour modifier une annonce.";
        exit();
    }
    
    $ida = isset($_GET["id_annonce"]) ? (int)$_GET["id_annonce"] : null;
    
    // Vérification de l'existence de l'annonce
    $verif = $bdd->prepare('SELECT * FROM annonces WHERE id_annonce = :id_annonce');
    $verif->execute(['id_annonce' => $ida]);
    $annonce = $verif->fetch();
    
    if(!$annonce){
        echo "Cette annonce n'existe pas.";
        exit();
    }
    
    // Vérification que l'utilisateur connecté est l'auteur de l'annonce
    if($_SESSION["id_profile"] != $annonce['id_profile']){
        echo "Vous n'êtes pas autorisé à modifier cette annonce.";
        exit();
    }
    
    // Modification de l'annonce
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom_annonce = htmlspecialchars(trim($_POST["nom_annonce"]));
        $prix = (float)$_POST["prix"];
        $date_creation = htmlspecialchars(trim($_POST["date_creation"]));
        $statut = htmlspecialchars(trim($_POST["statut"]));
        $description = htmlspecialchars(trim($_POST["description"]));
    
        $req = $bdd->prepare('UPDATE annonces
                              SET nom_annonce = :nom_annonce, prix = :prix, date_creation = :date_creation, 
                             statut = :statut, description = :description 
                             WHERE id_annonce = :id_annonce');
        
        $res = $req->execute([
            ':nom_annonce' => $nom_annonce,
            ':prix' => $prix,
            ':date_creation' => $date_creation,
            ':statut' => $statut,
            ':description' => $description,
            ':id_annonce' => $ida
        ]);
    
        if($res){
            header('Location: index.php');
            exit();
        }else{
            echo "Erreur : impossible de modifier cette annonce.<br>";
            print_r($req->errorInfo());
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'annonce</title>
    <link rel="stylesheet" href="modifier.css">
</head>
<body>
    <h1>Modification de l'annonce</h1>
    <form action="" method="post">
        <label for="nom_annonce">Titre de l'annonce :</label><br>
        <input type="text" id="nom_annonce" name="nom_annonce" value="<?= htmlspecialchars($annonce['nom_annonce']) ?>" required><br><br>

        <label for="prix">Prix (€) :</label><br>
        <input type="number" id="prix" name="prix" step="0.01" value="<?= htmlspecialchars($annonce['prix']) ?>" required><br><br>

        <label for="date_creation">Date de création :</label><br>
        <input type="date" id="date_creation" name="date_creation" value="<?= htmlspecialchars($annonce['date_creation']) ?>" required><br><br>

        <label for="statut">État de votre produit :</label><br>
        <select name="statut" required>
            <option value="tres_bon" <?= $annonce['statut'] == 'tres_bon' ? 'selected' : '' ?>>Très bon état</option>
            <option value="bon" <?= $annonce['statut'] == 'bon' ? 'selected' : '' ?>>Bon état</option>
            <option value="correct" <?= $annonce['statut'] == 'correct' ? 'selected' : '' ?>>État correct</option>
        </select><br><br>

        <label for="description">Description :</label><br>
        <textarea id="description" name="description" rows="5" cols="40" required><?= htmlspecialchars($annonce['description']) ?></textarea><br><br>

        <button type="submit">Enregistrer les modifications</button>
        <a href="index.php"  class="retour-annonces">Retour aux annonces</a>
    </form>
</body>
</html>
<style>
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