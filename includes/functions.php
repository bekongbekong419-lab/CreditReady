<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function t(string $en, string $fr): string {
    return (($_SESSION['lang'] ?? 'en') === 'fr') ? $fr : $en;
}
function url(string $path = ''): string {
    return rtrim(APP_BASE_URL, '/') . '/' . ltrim($path, '/');
}
function redirect(string $path): never {
    header('Location: ' . url($path));
    exit;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    $token = $_POST['csrf'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit(t('Your session expired. Please go back and try again.', 'Votre session a expiré. Revenez en arrière et réessayez.'));
    }
}
function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function get_flash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($f) ? $f : null;
}
function current_user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    static $user = null;
    if ($user !== null) return $user;
    $stmt = db()->prepare('SELECT id, full_name, email, role, preferred_language, business_name FROM users WHERE id = ?');
    $stmt->execute([(int)$_SESSION['user_id']]);
    $user = $stmt->fetch() ?: null;
    return $user;
}
function require_login(): array {
    $u = current_user();
    if (!$u) { flash('error', t('Please sign in to continue.', 'Veuillez vous connecter pour continuer.')); redirect('login.php'); }
    return $u;
}
function require_role(string ...$roles): array {
    $u = require_login();
    if (!in_array($u['role'], $roles, true)) { http_response_code(403); exit(t('You do not have permission to view this page.', 'Vous n’avez pas la permission de consulter cette page.')); }
    return $u;
}
function readiness_data(int $userId): array {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM business_activity WHERE user_id = ?');
    $stmt->execute([$userId]); $activityCount = (int)$stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM documents WHERE user_id = ?');
    $stmt->execute([$userId]); $documentCount = (int)$stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT business_name, sector, years_operating, monthly_revenue, monthly_expenses, registration_status, has_bank_account, collateral_available FROM users WHERE id = ?');
    $stmt->execute([$userId]); $u = $stmt->fetch() ?: [];
    $checks = [
        ['en'=>'Business profile completed','fr'=>'Profil de l’entreprise complété','ok'=>!empty($u['business_name']) && !empty($u['sector'])],
        ['en'=>'Business activity recorded','fr'=>'Activité commerciale enregistrée','ok'=>$activityCount >= 3],
        ['en'=>'Revenue and expenses entered','fr'=>'Revenus et dépenses renseignés','ok'=>(float)($u['monthly_revenue'] ?? 0) > 0 && (float)($u['monthly_expenses'] ?? 0) >= 0],
        ['en'=>'Supporting documents added','fr'=>'Pièces justificatives ajoutées','ok'=>$documentCount >= 1],
        ['en'=>'Business registration status provided','fr'=>'Statut d’immatriculation renseigné','ok'=>!empty($u['registration_status'])],
        ['en'=>'Banking and collateral information provided','fr'=>'Informations bancaires et garanties renseignées','ok'=>isset($u['has_bank_account']) && $u['has_bank_account'] !== null && isset($u['collateral_available']) && $u['collateral_available'] !== null],
    ];
    $passed = count(array_filter($checks, fn($c) => $c['ok']));
    return ['score'=>(int)round(($passed / count($checks)) * 100), 'checks'=>$checks, 'activity_count'=>$activityCount, 'document_count'=>$documentCount];
}
function matched_lenders(int $userId): array {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?'); $stmt->execute([$userId]); $biz = $stmt->fetch();
    if (!$biz) return [];
    $criteria = $pdo->query('SELECT lc.*, u.full_name AS lender_name, u.business_name AS institution_name FROM lender_criteria lc JOIN users u ON u.id=lc.lender_user_id WHERE lc.is_active=1')->fetchAll();
    $results = [];
    foreach ($criteria as $c) {
        $reasons = [];
        $amount = (float)($biz['requested_amount'] ?? 0);
        if ($amount > 0 && ($amount < (float)$c['min_amount'] || $amount > (float)$c['max_amount'])) $reasons[] = t('Requested amount is outside this lender’s range', 'Le montant demandé est hors de la fourchette de ce prêteur');
        if (!empty($c['sector']) && $c['sector'] !== 'all' && $c['sector'] !== ($biz['sector'] ?? '')) $reasons[] = t('Business sector does not match', 'Le secteur d’activité ne correspond pas');
        if ((int)($biz['years_operating'] ?? 0) < (int)$c['min_years_operating']) $reasons[] = t('Business needs more operating history', 'L’entreprise doit justifier d’une plus longue ancienneté');
        if ($c['requires_registration'] && ($biz['registration_status'] ?? '') !== 'registered') $reasons[] = t('Formal registration is required', 'Une immatriculation officielle est requise');
        if ($c['requires_bank_account'] && (int)($biz['has_bank_account'] ?? 0) !== 1) $reasons[] = t('A business bank account is required', 'Un compte bancaire professionnel est requis');
        $results[] = ['criteria'=>$c, 'matches'=>count($reasons)===0, 'reasons'=>$reasons];
    }
    usort($results, fn($a,$b) => (int)$b['matches'] <=> (int)$a['matches']);
    return $results;
}
