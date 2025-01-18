<?php
try {
    // Connexion à la base de données
    $dsn = 'pgsql:host=localhost;port=5432;dbname=cni_lost_found;';
    $username = 'postgres';
    $password = 'GPYV2002#';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Activer les exceptions pour PDO
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);

    // Vérification de l'existence des clés
    $user_name = isset($_POST['user_name']) ? trim($_POST['user_name']) : null;
    $user_fisrtname = isset($_POST['user_firstname']) ? trim($_POST['user_firstname']) : null; // Correction : user_fisrtname
    $kit_number = isset($_POST['kit_number']) ? trim($_POST['kit_number']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    // Vérifier si les valeurs ne sont pas nulles
    if ($user_name && $user_fisrtname && $kit_number && $email && $phone_number && $password) {
        // Validation simple des données (vous pouvez améliorer avec des regex)
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Adresse e-mail invalide.");
        }

        // Hachage du mot de passe
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Préparation et exécution de la requête d'insertion
        $stmt = $pdo->prepare(
    "INSERT INTO users (kit_number, user_name, user_fisrtname, email, phone_number, password, date_inscription) VALUES (?, ?, ?, ?, ?, ?, NOW())"
);
        $stmt->execute([$kit_number, $user_name, $user_fisrtname, $email, $phone_number, $hashed_password]);
           // Redirection vers la page d'accueil après connexion réussie
           header("Location: home.html"); // Changer l'URL vers la page de destination souhaitée
           exit(); // N'oubliez pas de sortir après la redirection pour éviter l'exécution du code suivant
        echo "Inscription réussie !";
    } else {
        echo "Veuillez remplir tous les champs.";
    }
} catch (PDOException $e) {
    echo 'Erreur PDO : ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}
?>
