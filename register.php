<?php
require_once __DIR__ . '/includes/functions.php';
if (current_user()) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'business_owner';
    $business = trim($_POST['business_name'] ?? '');
    $sector = $_POST['sector'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $lang = $_SESSION['lang'] ?? 'en';
    $allowedSectors = ['trade','agriculture','crafts','transport','services','food','other'];
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) $error = t('Enter your name, a valid email, and a password of at least 8 characters.','Saisissez votre nom, une adresse e-mail valide et un mot de passe d’au moins 8 caractères.');
    elseif (!in_array($role, ['business_owner','lender'], true)) $error = t('Choose a valid account type.','Choisissez un type de compte valide.');
    elseif ($role === 'business_owner' && ($business === '' || !in_array($sector, $allowedSectors, true))) $error = t('Enter your business name and choose a sector.','Saisissez le nom de votre entreprise et choisissez un secteur.');
    else {
        try {
            $stmt = db()->prepare('INSERT INTO users (full_name,email,password_hash,role,preferred_language,business_name,sector,phone) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([$name,$email,password_hash($password, PASSWORD_DEFAULT),$role,$lang,$business ?: null,$role==='business_owner'?$sector:null,$phone ?: null]);
            $_SESSION['user_id'] = (int)db()->lastInsertId();
            session_regenerate_id(true);
            flash('success', t('Welcome to CreditReady. Let’s set up your profile.','Bienvenue sur CreditReady. Configurons votre profil.'));
            redirect('dashboard.php');
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000' ? t('An account with this email already exists.','Un compte utilisant cet e-mail existe déjà.') : t('Unable to create your account. Check the database setup.','Impossible de créer le compte. Vérifiez la configuration de la base de données.');
        }
    }
}
$pageTitle = t('Create account','Créer un compte');
require __DIR__ . '/includes/header.php';
?>
<div class="auth-layout"><div class="auth-aside"><span class="eyebrow"><?= e(t('A STRONGER FILE STARTS HERE','UN DOSSIER PLUS SOLIDE COMMENCE ICI')) ?></span><h1><?= e(t('Your business story deserves to be seen.','Votre activité mérite d’être reconnue.')) ?></h1><p><?= e(t('Build a clearer picture of your business, one record at a time.','Présentez une image plus claire de votre entreprise, un enregistrement à la fois.')) ?></p><ul class="feature-list"><li><?= e(t('Plain-language guidance','Conseils simples et clairs')) ?></li><li><?= e(t('Control over sharing','Contrôle du partage')) ?></li><li><?= e(t('Lender requirements in one place','Exigences des prêteurs au même endroit')) ?></li></ul></div>
<form class="form-card" method="post" novalidate><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><h2><?= e(t('Create your account','Créer votre compte')) ?></h2><p class="muted"><?= e(t('Choose the workspace that fits you.','Choisissez l’espace qui vous correspond.')) ?></p>
<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<label><?= e(t('I am a','Je suis')) ?><select name="role" id="role" required><option value="business_owner" <?= ($_POST['role'] ?? 'business_owner') === 'business_owner' ? 'selected' : '' ?>><?= e(t('Business owner / SME','Entrepreneur / PME')) ?></option><option value="lender" <?= ($_POST['role'] ?? '') === 'lender' ? 'selected' : '' ?>><?= e(t('Lender / MFI / cooperative','Prêteur / EMF / coopérative')) ?></option></select></label>
<label><?= e(t('Full name','Nom complet')) ?><input name="full_name" autocomplete="name" required value="<?= e($_POST['full_name'] ?? '') ?>"></label>
<label><?= e(t('Email address','Adresse e-mail')) ?><input type="email" name="email" autocomplete="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
<label><?= e(t('Phone (optional)','Téléphone (facultatif)')) ?><input name="phone" placeholder="+237 6XX XXX XXX" value="<?= e($_POST['phone'] ?? '') ?>"></label>
<div id="business-fields" <?= ($_POST['role'] ?? 'business_owner') === 'lender' ? 'hidden' : '' ?>><label><?= e(t('Business name','Nom de l’entreprise')) ?><input name="business_name" value="<?= e($_POST['business_name'] ?? '') ?>"></label><label><?= e(t('Business sector','Secteur d’activité')) ?><select name="sector"><option value=""><?= e(t('Choose a sector','Choisir un secteur')) ?></option><?php foreach (['trade'=>['Trade / retail','Commerce / vente'],'agriculture'=>['Agriculture / farming','Agriculture'],'crafts'=>['Crafts / tailoring','Artisanat / couture'],'transport'=>['Transport','Transport'],'services'=>['Services','Services'],'food'=>['Food / hospitality','Alimentation / restauration'],'other'=>['Other','Autre']] as $v=>$labels): ?><option value="<?= e($v) ?>" <?= ($_POST['sector'] ?? '')===$v?'selected':'' ?>><?= e(t($labels[0],$labels[1])) ?></option><?php endforeach; ?></select></label></div>
<label><?= e(t('Password (minimum 8 characters)','Mot de passe (8 caractères minimum)')) ?><input type="password" name="password" minlength="8" autocomplete="new-password" required></label>
<button class="btn btn-primary btn-large full-width" type="submit"><?= e(t('Create account','Créer le compte')) ?> →</button><p class="form-foot"><?= e(t('Already registered?','Déjà inscrit ?')) ?> <a href="<?= e(url('login.php')) ?>"><?= e(t('Sign in','Se connecter')) ?></a></p></form></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const businessFields = document.getElementById('business-fields');

    if (!roleSelect || !businessFields) return;

    const businessName = businessFields.querySelector('input[name="business_name"]');
    const sector = businessFields.querySelector('select[name="sector"]');

    function updateBusinessFields() {
        const isBusinessOwner = roleSelect.value === 'business_owner';

        businessFields.hidden = !isBusinessOwner;

        if (businessName) businessName.required = isBusinessOwner;
        if (sector) sector.required = isBusinessOwner;

        if (!isBusinessOwner) {
            if (businessName) businessName.required = false;
            if (sector) sector.required = false;
        }
    }

    roleSelect.addEventListener('change', updateBusinessFields);
    updateBusinessFields();
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
