<?php
    session_start();
    session_unset();
    session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion</title>
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

        .message {
            text-align: center;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .message h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .message p {
            color: #555;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="message">
        <h1>Déconnexion réussie</h1>
        <p>Vous allez être redirigé vers la page de connexion...</p>
    </div>

    <script>
        // Redirection après 3 secondes
        setTimeout(() => {
            window.location.href = "connexion.php";
        }, 3000);
    </script>
</body>
</html>