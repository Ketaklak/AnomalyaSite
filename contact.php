<?php
// Vérifier que le formulaire est soumis
if (isset($_POST['name'], $_POST['email'], $_POST['subject'], $_POST['message'])) {
    // Récupération et nettoyage des données
    $name    = strip_tags($_POST['name']);
    $email   = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = strip_tags($_POST['subject']);
    $message = strip_tags($_POST['message']);

    // Adresse de réception (remplacez par votre adresse réelle)
    $to = "contact@anomalya.fr";
    $headers = "From: $name <$email>\r\n";
    $mailSubject = "Nouveau message depuis le site : $subject";
    $body = "Nom : $name\nEmail : $email\n\nMessage :\n$message\n";

    // Envoi de l'email
    mail($to, $mailSubject, $body, $headers);

    // Redirection vers la page d'accueil avec le paramètre ?sent=1
    header("Location: /?sent=1");
    exit();
} else {
    // En cas d'accès direct ou d'erreur, rediriger vers l'accueil
    header("Location: /");
    exit();
}
