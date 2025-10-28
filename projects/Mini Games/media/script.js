document.addEventListener("DOMContentLoaded", () => {
    // Utility: funzione per messaggi colorati
    function mostraMessaggio(id, testo, colore) {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = testo;
            el.style.color = colore;
            el.style.display = "block";
        }
    }

    // =======================
    // GIOCO: Indovina il numero
    // =======================
    if (document.getElementById("tentativo")) {
        const numeroSegreto = Math.floor(Math.random() * 100) + 1;
        let tentativi = 0;

        window.controlla = function () {
            const input = document.getElementById("tentativo");
            const valore = parseInt(input.value);

            if (isNaN(valore)) {
                mostraMessaggio("risultato", "⚠️ Inserisci un numero valido!", "#ff4444");
                return;
            }

            tentativi++;

            if (valore === numeroSegreto) {
                mostraMessaggio("risultato", `🎉 Indovinato in ${tentativi} tentativi!`, "#00ff88");
            } else if (valore < numeroSegreto) {
                mostraMessaggio("risultato", "Troppo basso! 📉", "#ffcc00");
            } else {
                mostraMessaggio("risultato", "Troppo alto! 📈", "#ff6600");
            }

            input.value = "";
            input.focus();
        };
    }

    // =======================
    // GIOCO: Blocco che scappa
    // =======================
    if (document.getElementById("blocchetto")) {
        const blocco = document.getElementById("blocchetto");
        const container = document.querySelector(".contenitore");
        const messaggio = document.getElementById("messaggio");

        function moveBlocco() {
            const maxX = container.clientWidth - blocco.offsetWidth;
            const maxY = container.clientHeight - blocco.offsetHeight;
            const randomX = Math.floor(Math.random() * maxX);
            const randomY = Math.floor(Math.random() * maxY);
            blocco.style.transform = `translate(${randomX}px, ${randomY}px)`;
        }

        blocco.addEventListener("mouseenter", moveBlocco);
        blocco.addEventListener("click", () => {
            if (messaggio) messaggio.style.display = "block";
        });
    }

    // Qui puoi aggiungere altri minigiochi in futuro
});
