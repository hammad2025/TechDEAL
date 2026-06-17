<?php
    session_start();
    include('../backend/bdd.php');

    // Vérification de l'authentification
    if(!isset($_SESSION["id_profile"])){
        // Redirection vers la page de connexion ou d'inscription
        header('Location: inscription.php');
        exit();
    }

    // Vérification de l'authentification
    if(!isset($_SESSION["id_profile"]) || !isset($_SESSION["e_mail"])){
        header('location:inscription.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleajouter_annonce.css">
    <title>Ajouter une annonce</title>

</head>
<body>
    <form action="../backend/annoncetraitement.php" method="post" enctype="multipart/form-data">

        <label for="marque">Marque:</label>
        <input type="text" id="marque" name="marque" required>

        <label for="nom_annonce">Série:</label>
        <input type="text" id="nom_annonce" name="nom_annonce" required>
        
        <label for="etat">État :</label>
        <select id="etat" name="etat" required>
            <option value="" selected disabled>État</option>
            <option value="Très bon état">Très bon état</option>
            <option value="Bon état">Bon état</option>
            <option value="État correct">État correct</option>
        </select>
        
        <label for="prix">Prix (€)</label>
        <input type="number" id="prix" name="prix" step="0.01" min="0" placeholder="Prix" required>

        <label for="description">Description de votre annonce</label>
        <textarea id="description" name="description" cols="40" rows="5" required></textarea>

        <label for="date_creation">Entrez la date de création de votre annonce</label>
        <input type="date" id="date_creation" name="date_creation" required>


        <label for="image">Image de votre annonce</label>
        <input type="file" id="images" name="images[]" multiple accept="image/*" required>
        <p id="image-error" style="color: red; display: none;">Vous ne pouvez sélectionner que 5 images maximum.</p>

        <script>
            document.getElementById('images').addEventListener('change', function () {
            const maxFiles = 5;
            const fileInput = this;
            const errorElement = document.getElementById('image-error');

            if (fileInput.files.length > maxFiles) {
                errorElement.style.display = 'block';
                fileInput.value = ''; // Réinitialise le champ
            } else {
                errorElement.style.display = 'none';
            }
            });
        </script>
        <input type="submit" value="Publier l'annonce">
        <a href="index.php" class="btn">Retour aux annonces</a>

    </form>
</body>
</html>
<style>
.btn {
    display: inline-block;
    background: linear-gradient(90deg, #667eea 0%, #43e97b 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 12px 0;
    font-size: 17px;
    font-weight: bold;
    cursor: pointer;
    text-align: center;
    width: 100%;
    margin-top: 12px;
    text-decoration: none;
    box-shadow: 0 2px 8px #6c63ff33;
    transition: background 0.2s, transform 0.2s;
}
.btn:hover {
    background: linear-gradient(90deg, #43e97b 0%, #667eea 100%);
    color: #fff;
    transform: translateY(-2px) scale(1.04);
}
</style>