<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('bdd.php');

if (!isset($_SESSION['id_profile'])) {
    echo "Vous devez être connecté pour voir vos messages.";
    exit();
}

$id_profile = $_SESSION['id_profile'];

$req = $bdd->prepare('
    SELECT 
        M.id_annonce,
        M.id_profile_envoyeur,
        p.prenom AS prenom_envoyeur,
        A.nom_annonce AS titre_annonce,
        MAX(M.date_d_envoie) AS dernier_message,
        COUNT(*) AS total_messages,
        MIN(PH.url_photo) AS photo_annonce
    FROM messages M
    JOIN profile p ON M.id_profile_envoyeur = p.id_profile
    JOIN annonces A ON M.id_annonce = A.id_annonce
    LEFT JOIN photo PH ON M.id_annonce = PH.id_annonce
    WHERE M.id_profile_destinataire = :id_profile
    GROUP BY M.id_annonce, M.id_profile_envoyeur, A.nom_annonce, p.nom
    ORDER BY dernier_message DESC
');

$req->execute(['id_profile' => $id_profile]);
$conversations = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>📥 Messages reçus</h2>

<?php if (empty($conversations)): ?>
    <p>Vous n'avez reçu aucun message.</p>
<?php else: ?>
    <ul>
        <?php foreach ($conversations as $conv): ?>
            <li style="margin-bottom: 20px;">
                <?php if (!empty($conv['photo_annonce'])): ?>
                    <img src="../images/<?= htmlspecialchars($conv['photo_annonce']) ?>" alt="Photo de l'annonce" style="width: 100px; height: auto; border-radius: 8px;">
                <?php endif; ?>

                <strong><?= htmlspecialchars($conv['prenom_envoyeur']) ?></strong> vous a écrit à propos de :
                <em>"<?= htmlspecialchars($conv['titre_annonce']) ?>"</em> 
                (<?= $conv['total_messages'] ?> message(s))<br>
                <a href="messagerie.php?id_annonce=<?= $conv['id_annonce'] ?>&id_destinataire=<?= $conv['id_profile_envoyeur'] ?>">
                    ➤ Voir la conversation
                </a>

            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
    <a href="../frontend/index.php" class="btn">Retour aux annonces</a>

<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: 0;
    padding: 0;
    min-height: 100vh;
}

h2 {
    text-align: center;
    color: #fff;
    letter-spacing: 1px;
    margin-top: 30px;
    font-size: 2.2em;
    text-shadow: 0 2px 16px #6c63ff, 0 0px 2px #fff;
}

ul {
    list-style: none;
    padding: 0;
    max-width: 700px;
    margin: 40px auto 0 auto;
}

li {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(76, 201, 240, 0.18);
    padding: 22px 30px 22px 120px;
    margin-bottom: 30px;
    position: relative;
    font-size: 1.15em;
    color: #222;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 2px solid #fff;
}

li:hover {
    transform: scale(1.03);
    box-shadow: 0 12px 36px rgba(76, 201, 240, 0.28);
    border-color: #6c63ff;
}

li img {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 14px;
    box-shadow: 0 2px 12px #6c63ff55;
    border: 3px solid #fff;
    background: #fff;
}

li strong {
    color: #6c63ff;
    font-size: 1.1em;
    text-shadow: 0 1px 4px #fff;
}

li em {
    color: #222;
    font-style: normal;
    font-weight: bold;
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    padding: 2px 8px;
    border-radius: 8px;
    margin-left: 4px;
}

li a {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 22px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1em;
    letter-spacing: 1px;
    box-shadow: 0 2px 8px #6c63ff33;
    transition: background 0.2s, transform 0.2s;
    border: none;
}
li a:hover {
    background: linear-gradient(90deg, #38f9d7 0%, #43e97b 100%);
    color: #222;
    transform: translateY(-2px) scale(1.04);
}
a.btn {
    display: block;
    width: fit-content;
    margin: 40px auto 0 auto;
    padding: 14px 36px;
    background: linear-gradient(90deg, #667eea 0%, #43e97b 100%);
    color: #fff;
    font-size: 18px;
    font-weight: bold;
    border-radius: 25px;
    text-decoration: none;
    box-shadow: 0 4px 16px #6c63ff33;
    letter-spacing: 1px;
    text-align: center;
    transition: background 0.3s, transform 0.2s;
}
a.btn:hover {
    background: linear-gradient(90deg, #43e97b 0%, #667eea 100%);
    color: #fff;
    transform: translateY(-2px) scale(1.04);
}
</style>