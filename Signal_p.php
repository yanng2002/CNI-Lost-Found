<?php
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

    // Récupération des données du formulaire
    $kit_number = isset($_POST['kit_number']) ? trim($_POST['kit_number']) : null;
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : null;
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : null;
    $date_naissance = isset($_POST['date_naissance']) ? trim($_POST['date_naissance']) : null;
    $lieux_naissance = isset($_POST['lieux_naissance']) ? trim($_POST['lieux_naissance']) : null;
    $ville = isset($_POST['ville']) ? trim($_POST['ville']) : null;
    $nom_commissariat = isset($_POST['nom_commissariat']) ? trim($_POST['nom_commissariat']) : null;
    $statut_cni = isset($_POST['statut_cni']) ? trim($_POST['statut_cni']) : null;

    // Vérification des données
    if ($kit_number && $nom && $prenom && $date_naissance && $lieux_naissance && $ville && $nom_commissariat && $statut_cni) {
        
        // 1. Récupérer ou insérer `id_commissariat`
        $stmt_commissariat = $pdo->prepare(
            "SELECT id_commissariat FROM commissariat WHERE nom_commissariat = ? AND ville = ?"
        );
        $stmt_commissariat->execute([$nom_commissariat, $ville]);
        $id_commissariat = $stmt_commissariat->fetchColumn();

        if (!$id_commissariat) {
            $stmt_insert_commissariat = $pdo->prepare(
                "INSERT INTO commissariat (nom_commissariat, ville) VALUES (?, ?) RETURNING id_commissariat"
            );
            $stmt_insert_commissariat->execute([$nom_commissariat, $ville]);
            $id_commissariat = $stmt_insert_commissariat->fetchColumn();
        }

        // 2. Récupérer `id_user` à partir de la table `users` (par exemple via le prénom ou une autre donnée unique)
        $stmt_user = $pdo->prepare(
            "SELECT id_user FROM users WHERE user_name = ?"
        );
        $stmt_user->execute([$prenom]);
        $id_user = $stmt_user->fetchColumn();

        if (!$id_user) {
            throw new Exception("Utilisateur non trouvé dans la table 'users'.");
        }

        // 3. Insertion dans la table `cni`
        $stmt_cni = $pdo->prepare(
            "INSERT INTO cni (kit_number, nom, prenom, date_naissance, lieux_naissance, date_declaration, statut_cni, id_user, id_commissariat) 
            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)"
        );
        $stmt_cni->execute([$kit_number, $nom, $prenom, $date_naissance, $lieux_naissance, $statut_cni, $id_user, $id_commissariat]);

        // Redirection avec message
        session_start();
        $_SESSION['message'] = "Votre formulaire a été envoyé avec succès !";
        header("Location: home.html");
        exit;
    } else {
        echo "Veuillez remplir tous les champs !";
    }
} catch (PDOException $e) {
    echo 'Erreur PDO : ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}
?>
