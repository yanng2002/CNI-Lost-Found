<?php
session_start(); // Démarrer une session pour stocker les informations de l'utilisateur

try {
    // Connexion à la base de données
    $dsn = 'pgsql:host=localhost;port=5432;dbname=cni_lost_found;';
    $username = 'postgres';
    $password = 'GPYV2002#';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);

    // Vérification des données du formulaire
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if ($email && $password) {
        // Vérification de l'utilisateur dans la base de données
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Vérifier le mot de passe
            if (password_verify($password, $user['password'])) {
                // Connexion réussie : stocker les informations dans la session
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['user_firstname'] = $user['user_fisrtname'];

                echo "Connexion réussie ! Bienvenue, " . htmlspecialchars($user['user_name']) . " " . htmlspecialchars($user['user_fisrtname']);
                 // Redirection vers la page d'accueil après connexion réussie
                    header("Location: home.html"); // Changer l'URL vers la page de destination souhaitée
                     exit(); // N'oubliez pas de sortir après la redirection pour éviter l'exécution du code suivant
            } else {
                echo "Mot de passe incorrect.";
            }
        } else {
            echo "Aucun utilisateur trouvé avec cet e-mail.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
} catch (PDOException $e) {
    echo 'Erreur PDO : ' . $e->getMessage();
}
?>
