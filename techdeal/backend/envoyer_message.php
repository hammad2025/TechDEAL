<?php
header('Content-Type: application/json');
session_start();
include('bdd.php');

if (!isset($_SESSION['id_profile'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Non autorisé.']);
    exit();
}

// Traitement uniquement si méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_envoyeur = $_SESSION['id_profile'];
    $id_destinataire = isset($_POST['id_destinataire']) ? intval($_POST['id_destinataire']) : 0;
    $id_annonce = isset($_POST['id_annonce']) ? intval($_POST['id_annonce']) : 0;
    $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';

    if (!$id_destinataire || !$id_annonce || $contenu === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Données invalides.']);
        exit();
    }

    try {
        $req = $bdd->prepare('INSERT INTO messages (contenu_messages, id_profile_envoyeur, id_profile_destinataire, id_annonce, date_d_envoie) 
                              VALUES (:contenu, :id_envoyeur, :id_destinataire, :id_annonce, NOW())');
        $req->execute([
            'contenu' => $contenu,
            'id_envoyeur' => $id_envoyeur,
            'id_destinataire' => $id_destinataire,
            'id_annonce' => $id_annonce
        ]);

        echo json_encode(['success' => 'Message envoyé avec succès.']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur SQL : ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée.']);
}
