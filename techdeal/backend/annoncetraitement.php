<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    session_start();
    include('bdd.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Vérification de la connexion utilisateur
        if (!isset($_SESSION["id_profile"])) {
            echo "Erreur : utilisateur non connecté.";
            exit();
        }

    $idu = $_SESSION["id_profile"];

    // Récupération et sécurisation des données du formulaire
    $nom_annonce = htmlspecialchars(trim($_POST["nom_annonce"]));
    $marque_annonce = htmlspecialchars(trim($_POST["marque"]));
    $prix = floatval($_POST["prix"]);
    $date_creation = htmlspecialchars(trim($_POST["date_creation"]));
    $statut = htmlspecialchars(trim($_POST["etat"]));
    $description = htmlspecialchars(trim($_POST["description"]));

    // Insertion de l'annonce dans la base
    $req = $bdd->prepare('INSERT INTO annonces(nom_annonce, marque, prix, date_creation, statut, description, id_profile)
                          VALUES(:nom_annonce, :marque, :prix, :date_creation, :statut, :description, :id_profile)');
    if (!$req->execute([
        ':nom_annonce' => $nom_annonce,
        ':marque' => $marque_annonce,
        ':prix' => $prix,
        ':date_creation' => $date_creation,
        ':statut' => $statut,
        ':description' => $description,
        ':id_profile' => $idu
    ])) {
        echo "Erreur : Impossible d'insérer l'annonce.<br>";
        print_r($req->errorInfo());
        exit();
    }

    // Récupération de l'ID de l'annonce insérée
    $id_annonce = $bdd->lastInsertId();

    // Gestion des images
    $images = $_FILES["images"];
    $nbImages = count($images["name"]);

    // Vérification du nombre d'images
    if ($nbImages > 5) {
        echo "Erreur : Vous ne pouvez télécharger que 5 images maximum.";
        exit();
    }

    $id_profile = $_SESSION["id_profile"];

    for ($i = 0; $i < $nbImages; $i++) {
        // Vérification des erreurs de téléchargement
        if ($images["error"][$i] !== UPLOAD_ERR_OK) {
            echo "Erreur lors du téléchargement de l'image " . htmlspecialchars($images["name"][$i]) . ": Code d'erreur " . $images["error"][$i] . "<br>";
            continue;
        }

        // Vérification de la taille de l'image
        if ($images["size"][$i] > 2 * 1024 * 1024) { // Limite de 2 Mo
            echo "Erreur : la taille de l'image " . htmlspecialchars($images["name"][$i]) . " dépasse 2 Mo.<br>";
            continue;
        }

        // Vérification du type MIME
        $allowedTypes = ['image/jpeg', 'image/png'];
        if (!in_array($images["type"][$i], $allowedTypes)) {
            echo "Erreur : Le fichier " . htmlspecialchars($images["name"][$i]) . " n'est pas un type d'image valide.<br>";
            continue;
        }

        // Génération d'un nom unique pour chaque image
        $nomImage = uniqid() . '_' . basename($images["name"][$i]);

        // Définir le chemin où enregistrer l'image
        $destination = '../images/' . $nomImage;

        // Déplacement de l'image vers le dossier images
        if (move_uploaded_file($images["tmp_name"][$i], $destination)) {
            echo "Fichier déplacé avec succès : $destination<br>";

            // Insertion de l'image dans la base
            $req = $bdd->prepare('INSERT INTO photo(url_photo, id_annonce, id_profile)
                                  VALUES(:url_photo, :id_annonce, :id_profile)');
            if (!$req->execute([
                ':url_photo' => $nomImage,
                ':id_annonce' => $id_annonce,
                ':id_profile' => $id_profile

            ])) {
                echo "Erreur : Impossible d'insérer l'image " . htmlspecialchars($images["name"][$i]) . " dans la base.<br>";
                print_r($req->errorInfo());
            } else {
                echo "Image insérée dans la base : $nomImage<br>";
            }
        } else {
            echo "Erreur lors du déplacement de l'image " . htmlspecialchars($images["name"][$i]) . ".<br>";
        }
    }

    echo "Annonce ajoutée avec succès.<br>";
    header('Location: ../frontend/index.php');
    exit();
}
?>