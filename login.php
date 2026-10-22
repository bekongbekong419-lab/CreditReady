<?php
require_once __DIR__ . '/includes/functions.php';
if (current_user()) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? '')); $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT id,password_hash FROM users WHERE email=?'); $stmt->execute([$email]); $row = $stmt->fetch();
    if ($row && password_verify($password, $row['password_hash'])) {
        session_regenerate_id(true); $_SESSION['user_id'] = (int)$row['id']; redirect('dashboard.php');
    } else $error = t('Email or password is incorrect.','Adresse e-mail ou mot de passe incorrect.');
}
$pageTitle = t('Sign in','Connexion'); require __DIR__ . '/includes/header.php';
?>
<div class="auth-layout auth-single"><div class="auth-aside"><span class="eyebrow">CREDITREADY</span><h1><?= e(t('Make your next credit conversation a stronger one.','Préparez votre prochain échange avec un prêteur.')) ?></h1><p><?= e(t('A practical workspace for small businesses and lenders in Cameroon.','Un espace pratique pour les petites entreprises et les prêteurs au Cameroun.')) ?></p></div><form class="form-card" method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><h2><?= e(t('Welcome back','Bon retour')) ?></h2><?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?><label><?= e(t('Email address','Adresse e-mail')) ?><input type="email" name="email" autocomplete="email" required value="<?= e($_POST['email'] ?? '') ?>"></label><label><?= e(t('Password','Mot de passe')) ?><input type="password" name="password" autocomplete="current-password" required></label><button class="btn btn-primary btn-large full-width" type="submit"><?= e(t('Sign in','Se connecter')) ?> →</button><p class="form-foot"><?= e(t('New to CreditReady?','Nouveau sur CreditReady ?')) ?> <a href="<?= e(url('register.php')) ?>"><?= e(t('Create an account','Créer un compte')) ?></a></p></form></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
