<?php
session_start();
include('../backend/bdd.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_profile'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour ajouter des favoris.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_annonce = isset($_GET['id_annonce']) ? intval($_GET['id_annonce']) : 0;

    if ($id_annonce > 0) {
        try {
            $id_user = $_SESSION['id_profile']; // ID de l'utilisateur connecté

            // Vérifiez si l'annonce est déjà dans les favoris
            $checkStmt = $bdd->prepare("SELECT COUNT(*) FROM favoris WHERE id_annonce = :id_annonce AND id_profile = :id_profile");
            $checkStmt->execute([
                ':id_annonce' => $id_annonce,
                ':id_profile' => $id_user
            ]);
            $exists = $checkStmt->fetchColumn();

            if ($exists > 0) {
                // L'annonce est déjà dans les favoris
                echo json_encode(['success' => false, 'message' => 'Cette annonce est déjà dans vos favoris.']);
                exit;
            }

            // Ajouter l'annonce aux favoris
            $stmt = $bdd->prepare("INSERT INTO favoris (id_annonce, id_profile) VALUES (:id_annonce, :id_profile)");
            $stmt->execute([
                ':id_annonce' => $id_annonce,
                ':id_profile' => $id_user
            ]);

            echo json_encode(['success' => true, 'message' => 'Annonce ajoutée aux favoris.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout aux favoris : ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID annonce invalide']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide']);
}