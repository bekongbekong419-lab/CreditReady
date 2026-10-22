
(function () {
    "use strict";

    function initialiseSpeech() {
        if (document.getElementById("cr-speech-panel")) return;

        const style = document.createElement("style");
        style.textContent = `
            #cr-speech-panel {
                position: fixed;
                right: 20px;
                bottom: 20px;
                z-index: 999999;
                width: min(360px, calc(100vw - 32px));
                box-sizing: border-box;
                padding: 18px;
                background: #142B4A;
                color: #FFFFFF;
                border: 2px solid #16845B;
                border-radius: 14px;
                box-shadow: 0 6px 24px rgba(0,0,0,.24);
                font-family: Arial, Helvetica, sans-serif;
                font-size: 16px;
                line-height: 1.6;
            }

            #cr-speech-panel .cr-speech-heading {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 12px;
                font-size: 19px;
                font-weight: 700;
            }

            #cr-speech-panel .cr-speech-icon {
                width: 28px;
                height: 28px;
                flex-shrink: 0;
            }

            #cr-speech-panel .cr-speech-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            #cr-speech-panel button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                min-height: 46px;
                padding: 10px 14px;
                border: 2px solid transparent;
                border-radius: 8px;
                background: #16845B;
                color: #FFFFFF;
                font-family: inherit;
                font-size: 16px;
                font-weight: 700;
                line-height: 1.4;
                cursor: pointer;
            }

            #cr-speech-panel button:hover {
                background: #116B49;
            }

            #cr-speech-panel button:focus-visible {
                outline: 3px solid #D9A441;
                outline-offset: 3px;
            }

            #cr-speech-panel .cr-speech-secondary {
                background: #FFFFFF;
                color: #142B4A;
                border-color: #FFFFFF;
            }

            #cr-speech-panel .cr-speech-secondary:hover {
                background: #F7F9FC;
            }

            #cr-speech-status {
                display: block;
                margin-top: 12px;
                color: #FFFFFF;
                font-size: 15px;
                line-height: 1.6;
                overflow-wrap: anywhere;
            }

            @media (max-width: 480px) {
                #cr-speech-panel {
                    right: 12px;
                    bottom: 12px;
                    width: calc(100vw - 24px);
                }

                #cr-speech-panel button {
                    flex: 1 1 auto;
                }
            }
        `;
        document.head.appendChild(style);

        const panel = document.createElement("section");
        panel.id = "cr-speech-panel";
        panel.setAttribute("aria-label", "Page reading controls");

        panel.innerHTML = `
            <div class="cr-speech-heading">
                <svg class="cr-speech-icon" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <path d="M11 5 6 9H2v6h4l5 4z"></path>
                    <path d="M15.5 8.5a5 5 0 0 1 0 7"></path>
                    <path d="M19 5a10 10 0 0 1 0 14"></path>
                </svg>
                <span>Listen to this page</span>
            </div>

            <div class="cr-speech-buttons">
                <button type="button" id="cr-speech-start">
                    <span aria-hidden="true">▶</span>
                    <span>Listen</span>
                </button>
                <button type="button" id="cr-speech-pause"
                        class="cr-speech-secondary">
                    Pause
                </button>
                <button type="button" id="cr-speech-stop"
                        class="cr-speech-secondary">
                    Stop
                </button>
            </div>

            <div id="cr-speech-status" role="status"
                 aria-live="polite">
                Press Listen to hear the page read aloud.
            </div>
        `;

        document.body.appendChild(panel);

        const startButton = document.getElementById("cr-speech-start");
        const pauseButton = document.getElementById("cr-speech-pause");
        const stopButton = document.getElementById("cr-speech-stop");
        const status = document.getElementById("cr-speech-status");

        function pageLanguage() {
            const lang = (
                document.documentElement.lang ||
                document.body.getAttribute("lang") ||
                "en"
            ).toLowerCase();

            return lang.startsWith("fr") ? "fr-FR" : "en-GB";
        }

        function getPageText() {
            const content = document.querySelector(
                "main, [role='main'], .page-shell"
            ) || document.body;

            const copy = content.cloneNode(true);

            copy.querySelectorAll(
                "script, style, noscript, nav, footer, " +
                "button, input, select, textarea, " +
                "[aria-hidden='true'], [hidden], " +
                "#cr-speech-panel"
            ).forEach(element => element.remove());

            return (copy.innerText || copy.textContent || "")
                .replace(/\s+/g, " ")
                .trim();
        }

        function setStatus(message) {
            status.textContent = message;
        }

        startButton.addEventListener("click", function () {
            if (!("speechSynthesis" in window) ||
                !("SpeechSynthesisUtterance" in window)) {
                setStatus(
                    "Speech is unavailable in this browser. " +
                    "Please try an updated version of Chrome."
                );
                return;
            }

            window.speechSynthesis.cancel();

            const text = getPageText();

            if (!text) {
                setStatus("No readable page content was found.");
                return;
            }

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = pageLanguage();
            utterance.rate = 0.85;
            utterance.pitch = 1;

            utterance.onstart = function () {
                setStatus("Reading the page aloud.");
                startButton.querySelector("span:last-child").textContent =
                    "Reading";
            };

            utterance.onend = function () {
                setStatus("Reading finished.");
                startButton.querySelector("span:last-child").textContent =
                    "Listen";
                pauseButton.textContent = "Pause";
            };

            utterance.onerror = function (event) {
                setStatus(
                    "Unable to read the page: " + event.error +
                    ". Please press Listen to try again."
                );
                startButton.querySelector("span:last-child").textContent =
                    "Listen";
            };

            window.speechSynthesis.speak(utterance);
        });

        pauseButton.addEventListener("click", function () {
            if (!("speechSynthesis" in window)) return;

            if (window.speechSynthesis.speaking &&
                !window.speechSynthesis.paused) {
                window.speechSynthesis.pause();
                pauseButton.textContent = "Resume";
                setStatus("Reading is paused. Press Resume to continue.");
            } else if (window.speechSynthesis.paused) {
                window.speechSynthesis.resume();
                pauseButton.textContent = "Pause";
                setStatus("Reading has resumed.");
            }
        });

        stopButton.addEventListener("click", function () {
            if ("speechSynthesis" in window) {
                window.speechSynthesis.cancel();
            }

            startButton.querySelector("span:last-child").textContent =
                "Listen";
            pauseButton.textContent = "Pause";
            setStatus("Reading stopped.");
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initialiseSpeech);
    } else {
        initialiseSpeech();
    }
})();
