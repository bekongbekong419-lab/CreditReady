<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = t('Prepare your business for credit','Préparez votre entreprise au crédit');
require __DIR__ . '/includes/header.php';
?>
<section class="hero hero-image">
  <div class="hero-overlay"></div>
  <div class="hero-copy">
    <span class="eyebrow"><?= e(t('MADE FOR CAMEROONIAN SMEs','POUR LES PME CAMEROUNAISES')) ?></span>
    <h1><?= e(t('Get credit-ready. Find the right lender.','Préparez votre crédit. Trouvez le bon prêteur.')) ?></h1>
    <p class="hero-lead"><?= e(t('CreditReady helps traders, artisans, farmers and growing businesses organise their records, understand what is missing, and connect only with lenders whose requirements they can realistically meet.','CreditReady aide les commerçants, artisans, agriculteurs et entreprises en croissance à organiser leurs dossiers, comprendre les éléments manquants et trouver uniquement les prêteurs dont ils peuvent raisonnablement satisfaire les exigences.')) ?></p>
    <div class="hero-actions"><a class="btn btn-primary btn-large" href="<?= e(url('register.php')) ?>"><?= e(t('Check my readiness','Évaluer ma préparation')) ?> <span aria-hidden="true">→</span></a><a class="btn btn-light btn-large" href="#how-it-works"><?= e(t('See how it works','Voir comment ça marche')) ?></a></div>
    <div class="trust-row"><span>✓ <?= e(t('Your data, your choice','Vos données, votre choix')) ?></span><span>✓ <?= e(t('Clear next steps','Des étapes claires')) ?></span><span>✓ <?= e(t('English / Français','English / Français')) ?></span></div>
  </div>
</section>
<section class="snapshot-section section-block" aria-labelledby="snapshot-title">
  <div class="snapshot-intro">
    <span class="eyebrow"><?= e(t('A CLEAR STARTING POINT','UN POINT DE DÉPART CLAIR')) ?></span>
    <h2 id="snapshot-title"><?= e(t('Readiness snapshot','Aperçu de préparation')) ?></h2>
    <p><?= e(t('A simple example of how CreditReady helps you see what is complete and what to do next.','Un exemple simple de la façon dont CreditReady vous aide à voir ce qui est terminé et ce qu’il reste à faire.')) ?></p>
  </div>
  <div class="hero-card snapshot-card">
    <div class="mini-card-top"><span class="status-dot"></span><?= e(t('Sample business file','Exemple de dossier')) ?><span class="demo-tag"><?= e(t('Example','Exemple')) ?></span></div>
    <div class="score-circle"><div><strong>67%</strong><small><?= e(t('Prepared','Préparé')) ?></small></div></div>
    <h3><?= e(t('Your next best steps','Vos prochaines étapes')) ?></h3>
    <ul class="check-list"><li class="done"><?= e(t('Business profile completed','Profil complété')) ?></li><li class="done"><?= e(t('Monthly sales recorded','Ventes mensuelles enregistrées')) ?></li><li><?= e(t('Add supporting documents','Ajouter des justificatifs')) ?></li><li><?= e(t('Record 3 business activities','Enregistrer 3 activités commerciales')) ?></li></ul>
    <p class="fine-print"><?= e(t('Illustrative example only. Actual readiness depends on the information you provide.','Exemple illustratif. La préparation réelle dépend des informations fournies.')) ?></p>
  </div>
</section>
<section class="stats-strip"><div><strong><?= e(t('For real businesses','Pour les vraies entreprises')) ?></strong><span><?= e(t('Market traders, tailors, transport operators, farmers and more','Commerçants, tailleurs, transporteurs, agriculteurs et autres')) ?></span></div><div><strong><?= e(t('No mystery scores','Pas de score mystérieux')) ?></strong><span><?= e(t('See why something is missing and what to do next','Comprenez ce qui manque et comment agir')) ?></span></div><div><strong><?= e(t('Consent comes first','Le consentement d’abord')) ?></strong><span><?= e(t('Choose exactly which lender can see your file','Choisissez quel prêteur peut consulter votre dossier')) ?></span></div></section>
<section id="how-it-works" class="section-block">
  <div class="section-heading"><span class="eyebrow"><?= e(t('A SIMPLE PATH FORWARD','UN PARCOURS SIMPLE')) ?></span><h2><?= e(t('From daily business to a stronger credit file','De l’activité quotidienne à un dossier de crédit plus solide')) ?></h2><p><?= e(t('You do not need perfect accounts to start. Begin with what you know, then build your evidence step by step.','Vous n’avez pas besoin d’une comptabilité parfaite pour commencer. Partez de ce que vous savez, puis constituez vos preuves progressivement.')) ?></p></div>
  <div class="steps-grid"><article class="step-card"><span class="step-number">01</span><h3><?= e(t('Record','Enregistrer')) ?></h3><p><?= e(t('Track sales, expenses and business activity in one simple place.','Suivez vos ventes, dépenses et activités au même endroit.')) ?></p></article><article class="step-card"><span class="step-number">02</span><h3><?= e(t('Understand','Comprendre')) ?></h3><p><?= e(t('See a plain-language readiness checklist with practical actions.','Consultez une liste claire des éléments à compléter et des actions utiles.')) ?></p></article><article class="step-card"><span class="step-number">03</span><h3><?= e(t('Choose','Choisir')) ?></h3><p><?= e(t('Review lender requirements and decide who may receive your information.','Comparez les exigences des prêteurs et décidez qui peut recevoir vos informations.')) ?></p></article></div>
</section>
<section class="callout"><div><span class="eyebrow"><?= e(t('READY WHEN YOU ARE','À VOTRE RYTHME')) ?></span><h2><?= e(t('Start building a file you can explain with confidence.','Commencez à bâtir un dossier que vous pourrez présenter avec confiance.')) ?></h2><p><?= e(t('CreditReady supports preparation. Lending decisions remain with each lender.','CreditReady vous accompagne dans la préparation. Chaque prêteur reste responsable de sa décision.')) ?></p></div><a class="btn btn-primary btn-large" href="<?= e(url('register.php')) ?>"><?= e(t('Create free account','Créer un compte gratuit')) ?> →</a></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
