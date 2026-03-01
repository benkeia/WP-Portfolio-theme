function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

const phrases = ["Développeur Web", "Designer Graphique", "Monteur vidéo", "UI/UX designer", "Développeur Mobile", "Développeur C#", "Développeur VR/AR"];

export function initTypewriter() {
    const el = document.getElementById("typewriter");
    if (!el || el.dataset.initialized === 'true') {
        return;
    }
    el.dataset.initialized = 'true';

    let sleepTime = 80;
    let curPhraseIndex = 0;

    const writeLoop = async () => {
        if (!el.isConnected) return; // Stop if the element is removed from the DOM

        const initialText = el.innerText;
        for (let i = initialText.length; i > 0; i--) {
            if (!el.isConnected) return;
            el.innerText = initialText.substring(0, i - 1);
            await sleep(sleepTime);
        }
        if (!el.isConnected) return;
        await sleep(sleepTime * 20);

        while (el.isConnected) {
            let curWord = phrases[curPhraseIndex];

            for (let i = 0; i < curWord.length; i++) {
                if (!el.isConnected) return;
                el.innerText = curWord.substring(0, i + 1);
                await sleep(sleepTime);
            }

            if (!el.isConnected) return;
            await sleep(sleepTime * 40);

            for (let i = curWord.length; i > 0; i--) {
                if (!el.isConnected) return;
                el.innerText = curWord.substring(0, i - 1);
                await sleep(sleepTime);
            }
            
            if (!el.isConnected) return;
            await sleep(sleepTime * 20);

            if (curPhraseIndex === phrases.length - 1) {
                curPhraseIndex = 0;
            } else {
                curPhraseIndex++;
            }
        }
    };

    writeLoop();
}