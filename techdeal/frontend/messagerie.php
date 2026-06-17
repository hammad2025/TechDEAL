<?php
session_start();
include('bdd.php');
include('fonctions.php');

if (!isset($_SESSION['id_profile'])) {
    header("Location: connexion.php");
    exit();
}

$id_annonce = isset($_GET['id_annonce']) ? intval($_GET['id_annonce']) : 0;
$id_destinataire = isset($_GET['id_destinataire']) ? intval($_GET['id_destinataire']) : 0;
$id_profile = $_SESSION['id_profile'];

// Fonction pour récupérer les messages
function recupererMessagesParAnnonce($id_annonce, $bdd) {
    $req = $bdd->prepare('SELECT m.*, p.prenom AS prenom_envoyeur
                          FROM messages m
                          JOIN profile p ON m.id_profile_envoyeur = p.id_profile
                          WHERE m.id_annonce = :id_annonce
                          ORDER BY m.date_d_envoie ASC');
    $req->execute(['id_annonce' => $id_annonce]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

$messages = recupererMessagesParAnnonce($id_annonce, $bdd);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messagerie</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .message-box { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
        .timestamp { font-size: 0.8em; color: #777; }
    </style>
</head>
<body>

<h2>Messagerie concernant l’annonce #<?= htmlspecialchars($id_annonce) ?></h2>

<div id="messages">
    <?php foreach ($messages as $message): ?>
        <div class="message-box">
            <p><strong><?= htmlspecialchars($message['prenom_envoyeur']) ?> :</strong></p>
            <p><?= nl2br(htmlspecialchars($message['contenu_messages'])) ?></p>
            <p class="timestamp"><?= $message['date_d_envoie'] ?></p>
        </div>
    <?php endforeach; ?>
</div>

<div class="message-form">
    <textarea id="contenu" rows="4" cols="50" placeholder="Écrire un message..."></textarea><br>
    <input type="hidden" id="id_annonce" value="<?= $id_annonce ?>">
    <input type="hidden" id="id_destinataire" value="<?= $id_destinataire ?>">
    <button onclick="envoyerMessage()">Envoyer</button>
</div>

<script>
function envoyerMessage() {
    const contenu = document.getElementById('contenu').value;
    const id_annonce = document.getElementById('id_annonce').value;
    const id_destinataire = document.getElementById('id_destinataire').value;

    if (contenu.trim() === '') {
        alert('Message vide.');
        return;
    }

    const formData = new FormData();
    formData.append('contenu', contenu);
    formData.append('id_annonce', id_annonce);
    formData.append('id_destinataire', id_destinataire);

    fetch('envoyer_message.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('contenu').value = '';
            location.reload();
        } else {
            alert(data.error);
        }
    });
}
</script>
</body>
</html>
