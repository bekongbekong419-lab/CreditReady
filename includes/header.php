<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? APP_NAME;
$user = current_user();
$flashMessage = get_flash();
?>
<!doctype html>
<html lang="<?= e($_SESSION['lang'] ?? 'en') ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> · CreditReady</title>

<link rel="stylesheet" href="<?= e(url('assets/style.css')) ?>">
<script src="<?= e(url('speech.js')) ?>" defer></script>
</head>

<body>
<header class="site-header">
  <div class="nav-wrap">
    <a class="brand" href="<?= e(url('index.php')) ?>"><span class="brand-mark">CR</span><span>Credit<span class="brand-accent">Ready</span><small><?= e(t('SME credit readiness','Préparation au crédit des PME')) ?></small></span></a>
    <nav class="main-nav">
      <?php if ($user): ?>
        <a href="<?= e(url('dashboard.php')) ?>"><?= e(t('Dashboard','Tableau de bord')) ?></a>
        <?php if ($user['role']==='business_owner'): ?><a href="<?= e(url('activity.php')) ?>"><?= e(t('Activity','Activité')) ?></a><a href="<?= e(url('lenders.php')) ?>"><?= e(t('Lenders','Prêteurs')) ?></a><?php endif; ?>
        <?php if ($user['role']==='lender'): ?><a href="<?= e(url('lender_criteria.php')) ?>"><?= e(t('Criteria','Critères')) ?></a><a href="<?= e(url('lender_matches.php')) ?>"><?= e(t('Matched businesses','Entreprises correspondantes')) ?></a><?php endif; ?>
      <?php else: ?>
        <a href="<?= e(url('index.php#how-it-works')) ?>"><?= e(t('How it works','Comment ça marche')) ?></a>
      <?php endif; ?>
    </nav>
    <div class="nav-actions">
      <form method="post" action="<?= e(url('language.php')) ?>" class="lang-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="return" value="<?= e($_SERVER['REQUEST_URI'] ?? url('index.php')) ?>">
        <button class="language-btn" type="submit" name="lang" value="<?= ($_SESSION['lang'] ?? 'en')==='en'?'fr':'en' ?>"><?= ($_SESSION['lang'] ?? 'en')==='en'?'FR':'EN' ?></button>
      </form>
      <?php if ($user): ?>
        <span class="user-chip"><?= e($user['full_name']) ?></span><a class="btn btn-small btn-outline" href="<?= e(url('logout.php')) ?>"><?= e(t('Sign out','Déconnexion')) ?></a>
      <?php else: ?>
        <a class="btn btn-small btn-outline" href="<?= e(url('login.php')) ?>"><?= e(t('Sign in','Connexion')) ?></a><a class="btn btn-small btn-primary" href="<?= e(url('register.php')) ?>"><?= e(t('Get started','Commencer')) ?></a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="page-shell">
<?php if ($flashMessage): ?><div class="alert alert-<?= e($flashMessage['type']) ?>"><?= e($flashMessage['message']) ?></div><?php endif; ?>
