<?php
// admin/save_news

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login");
    exit();
}

require_once "../config.php";

// Vérifier que la requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index");
    exit();
}

// Vérification du token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['feedback'] = [
        'type' => 'error',
        'title' => 'Erreur de sécurité',
        'message' => 'Token CSRF invalide.'
    ];
    header("Location: index");
    exit();
}

$uploadDir = "../uploads/";

// Récupération et sécurisation des données
$action       = $_POST['action']; // "create" ou "update"
$title        = strip_tags(trim($_POST['title']));
$content      = $_POST['content'];
$category_id  = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : NULL;
$publish_date = !empty($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d H:i:s');
$is_pinned    = isset($_POST['is_pinned']) ? 1 : 0;

// Au lieu de forcer "published"/"scheduled", on récupère le statut choisi
$allowedStatuses = ['draft', 'published', 'scheduled'];
if (!empty($_POST['status']) && in_array($_POST['status'], $allowedStatuses)) {
    $status = $_POST['status'];
} else {
    // Valeur par défaut
    $status = 'draft';
}

// (Optionnel) si vous souhaitez automatiser partiellement en fonction de la date
// if ($status === 'scheduled' && strtotime($publish_date) <= time()) {
//     $status = 'published';
// }

$imagePath = null;

// Traitement de l'upload d'image
if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageName = basename($_FILES['image']['name']);
    $uniqueName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $imageName);
    $targetFile = $uploadDir . $uniqueName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imagePath = "uploads/" . $uniqueName;
    }
}

try {
    if ($action === "create") {
        $stmt = $pdo->prepare("INSERT INTO news (title, content, image, category_id, publish_date, status, is_pinned) 
                               VALUES (:title, :content, :image, :category_id, :publish_date, :status, :is_pinned)");
        $stmt->execute([
            'title'        => $title,
            'content'      => $content,
            'image'        => $imagePath,
            'category_id'  => $category_id,
            'publish_date' => $publish_date,
            'status'       => $status,
            'is_pinned'    => $is_pinned
        ]);

        $_SESSION['feedback'] = [
            'type' => 'success',
            'title' => 'Succès',
            'message' => 'La news a été publiée avec succès.'
        ];

    } elseif ($action === "update" && isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        if ($imagePath) {
            $stmt = $pdo->prepare("UPDATE news
                                   SET title = :title,
                                       content = :content,
                                       image = :image,
                                       category_id = :category_id,
                                       publish_date = :publish_date,
                                       status = :status,
                                       is_pinned = :is_pinned
                                   WHERE id = :id");
            $stmt->execute([
                'title'        => $title,
                'content'      => $content,
                'image'        => $imagePath,
                'category_id'  => $category_id,
                'publish_date' => $publish_date,
                'status'       => $status,
                'is_pinned'    => $is_pinned,
                'id'           => $id
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE news
                                   SET title = :title,
                                       content = :content,
                                       category_id = :category_id,
                                       publish_date = :publish_date,
                                       status = :status,
                                       is_pinned = :is_pinned
                                   WHERE id = :id");
            $stmt->execute([
                'title'        => $title,
                'content'      => $content,
                'category_id'  => $category_id,
                'publish_date' => $publish_date,
                'status'       => $status,
                'is_pinned'    => $is_pinned,
                'id'           => $id
            ]);
        }

        $_SESSION['feedback'] = [
            'type' => 'success',
            'title' => 'Succès',
            'message' => 'La news a été mise à jour avec succès.'
        ];

    } else {
        throw new Exception('Action non reconnue ou données manquantes.');
    }

} catch (Exception $e) {
    $_SESSION['feedback'] = [
        'type' => 'error',
        'title' => 'Erreur',
        'message' => 'Erreur : ' . $e->getMessage()
    ];
}

header("Location: index");
exit();
