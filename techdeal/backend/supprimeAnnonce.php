<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    session_start();
    require("bdd.php");
    
    // Vérification de l'authentification
    if(!isset($_SESSION["id_profile"])){
        echo "Vous devez être connecté pour effectuer cette action.";
        exit();
    }
    
    // Récupération de l'ID de l'annonce
    $ida = isset($_GET["id_annonce"]) ? (int)$_GET["id_annonce"] : null;
    
    if(!$ida || $ida <= 0){
        echo "Cette annonce n'existe pas.";
        exit();
    }
    
    // Vérification de l'existence de l'annonce
    $verif = $bdd->prepare('SELECT id_profile FROM annonces WHERE id_annonce = :id_annonce');
    $verif->execute(['id_annonce' => $ida]);
    $annonce = $verif->fetch();
    
    if(!$annonce){
         echo "Cette annonce n'existe pas.";
        exit();
    }
    
    // Vérification que l'utilisateur connecté est l'auteur de l'annonce
    if($_SESSION["id_profile"] != $annonce['id_profile']){
        echo "Vous n'êtes pas autorisé à supprimer cette annonce.";
        exit();
    }
    
    // Suppression des images associées
    $photos = $bdd->prepare('SELECT url_photo FROM photo WHERE id_annonce = :id_annonce');
    $photos->execute(['id_annonce' => $ida]);
    $images = $photos->fetchAll();
    
    foreach ($images as $image){
        $filePath = 'images/' . $image['url_photo'];
        if(file_exists($filePath)){
            unlink($filePath); // Suppression du fichier physique
        }
    }
    
    // Suppression des entrées dans la table photo
    $deletePhotos = $bdd->prepare('DELETE FROM photo WHERE id_annonce = :id_annonce');
    $deletePhotos->execute(['id_annonce' => $ida]);
    
    // Suppression de l'annonce
    $supprimeAnnonce = $bdd->prepare('DELETE FROM annonces WHERE id_annonce = :id_annonce');
    if($supprimeAnnonce->execute(['id_annonce' => $ida])) {
        echo "Annonce supprimée avec succès.";
        header('Location: index.php?message=Annonce supprimée avec succès');
        exit();
    }else{
        echo "Erreur lors de la suppression de l'annonce.";
        exit();
    }
?>