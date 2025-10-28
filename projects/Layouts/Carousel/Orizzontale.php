<?php
$n = 10;
?>
<section>
    <div class="container position-relative">
        <div class="pt-5 pb-3 d-flex align-items-center">
            <h2 class="flex-grow-1 mb-0">Carousel</h2>
            <button class="btn btn-outline-dark rounded-circle p-0 px-1 me-2" type="button" id="prevBtn"
                aria-label="Previous">
                <i class="bi bi-arrow-left-short fs-5"></i>
            </button>
            <button class="btn btn-outline-dark rounded-circle p-0 px-1" type="button" id="nextBtn" aria-label="Next">
                <i class="bi bi-arrow-right-short fs-5"></i>
            </button>
        </div>

        <div id="carouselContainer" class="d-flex overflow-auto gap-3 pb-3 scroll-smooth"
            style="scroll-snap-type: x mandatory;">

            <?php for ($i = 1; $i <= $n; $i++): ?>
                <div class="flex-shrink-0" style="scroll-snap-align: start; width: 250px;">
                    <a href="#ant" class="btn btn-outline-warning w-100 py-4 text-truncate rounded fw-bold">
                        Articolo <?= $i ?>
                    </a>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById("carouselContainer");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const cards = Array.from(container.children);

        let currentIndex = 0;

        function scrollToCard(index) {
            index = Math.max(0, Math.min(cards.length - 1, index)); // evita overflow
            const card = cards[index];
            if (card) {
                container.scrollTo({
                    left: card.offsetLeft,
                    behavior: "smooth"
                });
                currentIndex = index;
            }
        }

        prevBtn.addEventListener("click", () => scrollToCard(currentIndex - 1));
        nextBtn.addEventListener("click", () => scrollToCard(currentIndex + 1));

        // aggiorna indice quando si scrolla manualmente
        container.addEventListener("scroll", () => {
            let nearest = 0;
            let minDist = Infinity;
            cards.forEach((card, i) => {
                const dist = Math.abs(container.scrollLeft - card.offsetLeft);
                if (dist < minDist) {
                    minDist = dist;
                    nearest = i;
                }
            });
            currentIndex = nearest;
        });

        // riallinea la card corrente quando si ridimensiona la finestra
        window.addEventListener("resize", () => {
            scrollToCard(currentIndex, false); // senza animazione per evitare glitch
        });
    });
</script>

<style>
    .scroll-smooth {
        scroll-behavior: smooth;
    }

    #carouselContainer::-webkit-scrollbar {
        height: 8px;
    }

    #carouselContainer::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    #carouselContainer::-webkit-scrollbar-thumb:hover {
        background: #999;
    }
</style>