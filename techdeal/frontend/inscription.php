<?php
    session_start();
    include('../backend/bdd.php');

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $nom=trim($_POST["nom"]);
        $prenom=trim($_POST["prenom"]);
        $mail=$_POST["mail"];
        $mdp=$_POST["mdp"];
        $c_mdp=$_POST["c_mdp"];
        $adresse=$_POST["adresse"];
        $contact=$_POST["contact"];
        $date_inscription = date('Y-m-d H:i:s');

        if($mdp!==$c_mdp){
            echo "Les mots de passe ne corrrespondent pas. <br>";
            exit();
        }
        if(strlen($mdp) < 10){
            echo "Le mot de passe doit contenir au moins 10 caractères.<br>";
            exit();
        }
        if(!empty($nom) && !empty($prenom) && !empty($mail) && !empty($mdp) && !empty($c_mdp) && !empty($adresse) && !empty($contact)){
            $req=$bdd->prepare('SELECT * FROM profile WHERE e_mail=:e_mail');
            $req->execute(['e_mail'=>$mail]);
            $user=$req->fetch();
            if($user){
                echo "Une erreur est survenue veuillez réessayer ! <br>";
            }else{
                $pass_hash=password_hash($mdp, PASSWORD_BCRYPT);
                
                $req1=$bdd->prepare('INSERT INTO profile(nom, prenom, e_mail, mot_de_passe, date_inscription, adresse, contact)
                                     VALUES(:nom, :prenom, :e_mail, :mot_de_passe, :date_inscription, :adresse, :contact)');
                $req1->execute([
                    ':nom'=>$nom,
                    ':prenom'=>$prenom,
                    ':e_mail'=>$mail,
                    ':mot_de_passe'=>$pass_hash,
                    ':date_inscription'=>$date_inscription,
                    ':adresse'=>$adresse,
                    ':contact'=>$contact
                ]);

                $req2=$bdd->prepare('SELECT * FROM profile WHERE e_mail=:e_mail');
                $req2->execute(['e_mail'=>$mail]);
                $NouvelUser=$req2->fetch();
                $_SESSION["id_profile"]=$NouvelUser["id_profile"];
                $_SESSION["e_mail"]=$NouvelUser["e_mail"];
                header('Location: index.php');
                exit();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        form input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <form action="" method="post">
        <h1>Le BonCoin</h1>
        <input type="text" name="nom" placeholder="Entrez votre nom" required>
        <input type="text" name="prenom" placeholder="Entrez votre prénom" required>
        <input type="email" name="mail" placeholder="Entrez votre mail" required>
        <input type="password" name="mdp" placeholder="Entrez votre mot de passe" required>
        <input type="password" name="c_mdp" placeholder="Confirmez votre mot de passe" required>
        <input type="text" name="adresse" placeholder="Entrez votre adresse" required>
        <input type="text" name="contact" placeholder="Entrez votre contact" required>
        <input type="submit" href="" value="S'inscrire">
        <a href="connexion.php">Se connecter</a>
    </form>
</body>
</html>