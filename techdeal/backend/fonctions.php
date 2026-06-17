<?php

function recupererMessagesparannnoces($id_annonce, $bdd) {
    $req = $bdd->prepare('SELECT m.*, p.prenom AS prenom_envoyeur
                          FROM messages m
                          JOIN profile p ON m.id_profile_envoyeur = p.id_profile
                          WHERE m.id_annonce = :id_annonce
                          ORDER BY m.date_d_envoie ASC');
    $req->execute(['id_annonce' => $id_annonce]);
    return $req->fetchAll(PDO::FETCH_ASSOC); // Fetch messages by recipient
}

function afficherMessages($messages) {
    foreach ($messages as $message) {
        echo '<div class="message">';
        echo '<p><strong>' . htmlspecialchars($message['prenom_envoyeur']) . ' :</strong></p>';
        echo '<p>' . nl2br(htmlspecialchars($message['contenu_messages'])) . '</p>';
        echo '<p class="timestamp">' . htmlspecialchars($message['date_d_envoie']) . '</p>';
        echo '</div>';
    }
}

// // function redirigerAvecMessage($url, $message) {
// //     $_SESSION['flash_message'] = $message;
// //     header("Location: $url");
// //     exit();
// }

// Removed duplicate function declaration to avoid redeclaration error.

function nettoyerFichiersOrphelins($bdd) {
    $directory = "images/";
    $files = scandir($directory);

    $req = $bdd->query('SELECT url_photo FROM photo');
    $photos_in_db = $req->fetchAll(PDO::FETCH_COLUMN);

    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $file_path = $directory . $file;
            if (!in_array($file, $photos_in_db) && file_exists($file_path)) {
                unlink($file_path); // Supprime le fichier orphelin
            }
        }
    }
}
    function recupererMessagesParDestinataire($id_profile, $id_destinataire, $bdd) {
        $req = $bdd->prepare('SELECT m.*, p.prenom AS prenom_envoyeur
                              FROM messages m
                              JOIN profile p ON m.id_profile_envoyeur = p.id_profile
                              WHERE m.id_profile_destinataire = :id_profile_destinataire AND m.id_profile_envoyeur = :id_profile_envoyeur
                              ORDER BY m.date_d_envoie ASC');
        $req->execute(['id_profile_destinataire' => $id_destinataire, 'id_profile_envoyeur' => $id_profile]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }