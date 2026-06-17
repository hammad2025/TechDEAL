<?php
session_start();
include('../backend/bdd.php');
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $mail=trim($_POST["email"]);
    $mdp=$_POST["mot_de_passe"];

    $req=$bdd->prepare('SELECT * FROM profile WHERE e_mail=:e_mail');
    $req->execute(['e_mail'=>$mail]);
    $user=$req->fetch();
    if($user and password_verify($mdp, $user["mot_de_passe"])){
        $_SESSION["id_profile"] = $user["id_profile"];
        $_SESSION["e_mail"] = $user["e_mail"];
        $_SESSION["prenom"] = $user["prenom"];
        header('Location: index.php');
        exit();
    }else{
        echo "Identifiants incorrects.<br>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion - TechDeal</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <div class="form-container">
    <h2>Connexion</h2>
    <form method="POST" action="">
      <div class="form-group">
        <label>Email :</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>Mot de passe :</label>
        <input type="password" name="mot_de_passe" required>
      </div>
      <input type="submit" href="" value="Se connecter">
    </form>

    <p class="link">Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
  </div>

  <div style="margin-top: 15px; text-align: center;">
    <a href="index.php" style="color: #0077b6; font-weight: bold; text-decoration: none;">
      ⬅️ Retour à l'accueil
    </a>
  </div>
  

</body>
</html>
