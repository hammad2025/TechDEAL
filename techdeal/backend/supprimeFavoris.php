<?php
// filepath: c:\MAMP\htdocs\php\projetFusion\supprimeFavoris.php

// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_profile'])) {
    header('Location: connexion.php');
    exit;
}

// Inclure la connexion à la base de données
include('bdd.php');

// Vérifier si l'ID de l'annonce est passé en paramètre
if (isset($_GET['id_annonce']) && !empty($_GET['id_annonce'])) {
    $id_annonce = intval($_GET['id_annonce']);
    $id_profile = $_SESSION['id_profile'];

    // Supprimer l'annonce des favoris de l'utilisateur
    $sql = 'DELETE FROM favoris WHERE id_annonce = :id_annonce AND id_profile = :id_profile';
    $stmt = $bdd->prepare($sql);
    $stmt->bindParam(':id_annonce', $id_annonce, PDO::PARAM_INT);
    $stmt->bindParam(':id_profile', $id_profile, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Rediriger vers la page des favoris avec un message de succès
        header('Location: liste_favoris.php?message=success');
        exit;
    } else {
        // Rediriger avec un message d'erreur en cas d'échec
        header('Location: liste_favoris.php?message=error');
        exit;
    }
} else {
    // Rediriger si aucun ID d'annonce n'est fourni
    header('Location: liste_favoris.php?message=invalid');
    exit;
}
?>