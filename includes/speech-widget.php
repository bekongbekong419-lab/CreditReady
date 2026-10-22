<?php /* Include this once near the end of includes/footer.php, before </body>. */ ?>
<div class="speech-widget" id="speechWidget">
  <button class="speech-toggle" id="speechToggle" type="button" aria-expanded="false" aria-controls="speechPanel" aria-label="Open read aloud controls" title="Read page aloud">
    <span aria-hidden="true">🔊</span>
  </button>
  <div class="speech-panel" id="speechPanel" hidden>
    <strong><?= e(t('Read this page','Lire cette page')) ?></strong>
    <div class="speech-controls">
      <button type="button" id="speechPlay"><?= e(t('Play','Lire')) ?></button>
      <button type="button" id="speechPause"><?= e(t('Pause','Pause')) ?></button>
      <button type="button" id="speechStop"><?= e(t('Stop','Arrêter')) ?></button>
    </div>
    <p class="speech-status" id="speechStatus" aria-live="polite"><?= e(t('Ready to read','Prêt à lire')) ?></p>
  </div>
</div>
<script src="<?= e(url('assets/speech.js')) ?>" defer></script>
