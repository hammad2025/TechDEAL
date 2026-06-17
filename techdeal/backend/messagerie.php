<?php
session_start();
include('bdd.php');

if (!isset($_SESSION['id_profile'])) {
    echo "Vous devez être connecté pour voir la messagerie.";
    exit();
}

$id_annonce = isset($_GET['id_annonce']) ? intval($_GET['id_annonce']) : 0;

// Récupérer le destinataire de l’annonce (l’auteur)
$annonceReq = $bdd->prepare('SELECT id_profile FROM annonces WHERE id_annonce = :id_annonce');
$annonceReq->execute(['id_annonce' => $id_annonce]);
$annonce = $annonceReq->fetch();

$id_destinataire = $annonce ? intval($annonce['id_profile']) : 0;
$id_envoyeur = $_SESSION['id_profile'];

$messages = [];
if ($id_annonce) {
    $msgReq = $bdd->prepare('SELECT m.*, p.nom AS nom_envoyeur 
                             FROM messages m
                             JOIN profile p ON m.id_profile_envoyeur = p.id_profile
                             WHERE m.id_annonce = :id_annonce
                             ORDER BY m.date_d_envoie ASC');
    $msgReq->execute(['id_annonce' => $id_annonce]);
    $messages = $msgReq->fetchAll(PDO::FETCH_ASSOC);
}
?>
<h2>Messages pour l'annonce #<?= $id_annonce ?></h2>
<div id="messageContainer">
    <?php if (empty($messages)): ?>
        <p>Aucun message pour cette annonce.</p>
    <?php else: ?>
        <?php foreach ($messages as $message): ?>
            <div class="message">
                <strong><?= htmlspecialchars($message['nom_envoyeur']) ?>:</strong><br>
                <?= nl2br(htmlspecialchars($message['contenu_messages'])) ?><br>
                <small><?= htmlspecialchars($message['date_d_envoie']) ?></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h3>Envoyer un message</h3>
<form id="formMessage">
    <textarea name="contenu" id="contenu" placeholder="Votre message..."></textarea>
    <input type="hidden" name="id_annonce" value="<?= $id_annonce ?>">
    <input type="hidden" name="id_destinataire" value="<?= $id_destinataire ?>">
    <button type="submit">Envoyer</button>
    <a href="../frontend/index.php">Retour aux annonces</a>
</form>

<div id="feedback"></div>

<script>
document.getElementById('formMessage').addEventListener('submit', function (e) {
    e.preventDefault();

    const contenu = document.getElementById('contenu').value.trim();
    const id_annonce = document.querySelector('[name="id_annonce"]').value;
    const id_destinataire = document.querySelector('[name="id_destinataire"]').value;

    if (!contenu) {
        document.getElementById('feedback').innerText = "Le message est vide.";
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
    .then(resp => resp.json())
    .then(data => {
        document.getElementById('feedback').innerText = data.success || data.error;
        if (data.success) location.reload();
    })
    .catch(err => {
        console.error("Erreur réseau :", err);
        document.getElementById('feedback').innerText = "Erreur réseau.";
    });
});
</script>

<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: 0;
    padding: 0;
    min-height: 100vh;
}

h2, h3 {
    text-align: center;
    color: #fff;
    letter-spacing: 1px;
    margin-top: 30px;
    text-shadow: 0 2px 16px #6c63ff, 0 0px 2px #fff;
}

#messageContainer {
    max-width: 600px;
    margin: 30px auto 20px auto;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(76, 201, 240, 0.18);
    padding: 25px 30px;
}

.message {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    border-radius: 14px;
    margin-bottom: 18px;
    padding: 15px 18px;
    box-shadow: 0 2px 12px #6c63ff33;
    color: #222;
    font-size: 16px;
    position: relative;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 2px solid #fff;
}
.message:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 24px #43e97b55;
    border-color: #6c63ff;
}

.message strong {
    color: #6c63ff;
    font-size: 17px;
    text-shadow: 0 1px 4px #fff;
}

.message small {
    color: #636e72;
    font-size: 13px;
    position: absolute;
    right: 18px;
    bottom: 10px;
}

#formMessage {
    max-width: 600px;
    margin: 30px auto 0 auto;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(76, 201, 240, 0.12);
    padding: 25px 30px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

#formMessage textarea {
    border-radius: 10px;
    border: 1.5px solid #74b9ff;
    padding: 12px;
    font-size: 16px;
    resize: vertical;
    min-height: 70px;
    transition: border 0.2s;
    background: #f4f8fb;
}
#formMessage textarea:focus {
    border: 2px solid #6c63ff;
    outline: none;
    background: #e3f0ff;
}

#formMessage button {
    background: linear-gradient(90deg, #667eea 0%, #43e97b 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 12px 0;
    font-size: 17px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
    box-shadow: 0 2px 8px #6c63ff33;
}
#formMessage button:hover {
    background: linear-gradient(90deg, #43e97b 0%, #667eea 100%);
    transform: translateY(-2px) scale(1.04);
}

#feedback {
    text-align: center;
    margin-top: 18px;
    font-weight: bold;
    color: #6c63ff;
    font-size: 16px;
    min-height: 20px;
}
#formMessage a {
    display: block;
    background: linear-gradient(90deg, #667eea 0%, #43e97b 100%);
    color: #fff;
    border-radius: 10px;
    padding: 12px 0;
    font-size: 17px;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    margin-top: 12px;
    box-shadow: 0 2px 8px #6c63ff33;
    transition: background 0.2s, transform 0.2s;
}
#formMessage a:hover {
    background: linear-gradient(90deg, #43e97b 0%, #667eea 100%);
    color: #fff;
    transform: translateY(-2px) scale(1.04);
}
</style>