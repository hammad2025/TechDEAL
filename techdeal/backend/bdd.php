<?php
    $conn="mysql:host=localhost; dbname=projet1; charset=utf8";
    $user="root";
    $pass="root";
    try{
            $bdd= new PDO($conn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            // echo "Connexion réussie à la BDD projet <br>";
        }catch(PDOException $e){
            die ("Echec de la connexion à la BDD projet : " .$e->getMessage());
        }
?>