/* CreditReady compact text-to-speech widget. Uses the browser's built-in speech synthesis. */
(() => {
  const toggle = document.getElementById('speechToggle');
  const panel = document.getElementById('speechPanel');
  if (!toggle || !panel || !('speechSynthesis' in window)) {
    if (toggle) toggle.hidden = true;
    return;
  }
  const status = document.getElementById('speechStatus');
  const setStatus = (message) => { if (status) status.textContent = message; };
  const getReadableText = () => {
    const main = document.querySelector('main') || document.querySelector('.page-content') || document.body;
    const clone = main.cloneNode(true);
    clone.querySelectorAll('script, style, noscript, button, input, select, textarea, nav, footer, .speech-widget').forEach(el => el.remove());
    return (clone.innerText || clone.textContent || '').replace(/\s+/g, ' ').trim();
  };
  toggle.addEventListener('click', () => {
    const opening = panel.hidden;
    panel.hidden = !opening;
    toggle.setAttribute('aria-expanded', String(opening));
    toggle.setAttribute('aria-label', opening ? 'Close read aloud controls' : 'Open read aloud controls');
    toggle.classList.toggle('is-open', opening);
    if (opening) document.getElementById('speechPlay')?.focus();
  });
  document.getElementById('speechPlay')?.addEventListener('click', () => {
    if (speechSynthesis.paused) { speechSynthesis.resume(); setStatus('Reading…'); return; }
    speechSynthesis.cancel();
    const text = getReadableText();
    if (!text) { setStatus('No page text found to read.'); return; }
    const utterance = new SpeechSynthesisUtterance(text);
    const lang = document.documentElement.lang || 'en';
    utterance.lang = lang.toLowerCase().startsWith('fr') ? 'fr-FR' : 'en-US';
    utterance.rate = 0.92;
    utterance.onstart = () => setStatus('Reading…');
    utterance.onend = () => setStatus('Finished reading.');
    utterance.onerror = () => setStatus('Could not read this page. Try again.');
    speechSynthesis.speak(utterance);
  });
  document.getElementById('speechPause')?.addEventListener('click', () => {
    if (speechSynthesis.speaking && !speechSynthesis.paused) { speechSynthesis.pause(); setStatus('Paused.'); }
  });
  document.getElementById('speechStop')?.addEventListener('click', () => {
    speechSynthesis.cancel(); setStatus('Stopped.');
  });
  window.addEventListener('beforeunload', () => speechSynthesis.cancel());
})();