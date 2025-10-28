<?php
// Numero di articoli da mostrare
$n = 5;
?>

<div id="articles-carousel" class="carousel border border-2 my-5" role="region" aria-label="Articoli">
    <div class="container position-relative">
        <div class="row overflow-hidden flex-nowrap" id="carouselItemsContainer" style="scroll-behavior: smooth;"
            aria-live="polite">

            <?php for ($i = 1; $i <= $n; $i++): ?>
                <article class="col-12 flex-shrink-0 px-2 carousel-item-custom" tabindex="0">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md py-2">
                                <!-- Immagine statica di default -->
                                <img src="https://static.vecteezy.com/ti/foto-gratuito/p1/11247398-galassia-della-via-lattea-con-stelle-e-polvere-spaziale-nell-universografia-a-lunga-esposizione-con-grano-gratuito-foto.jpg"
                                    class="d-block img-fluid" alt="Immagine articolo <?= $i ?>" loading="lazy">
                            </div>
                            <div class="col-md py-2">
                                <h2 class="fw-bold h1"><a href="#ant" class="text-dark">Articolo <?= $i ?></a></h2>
                                <div class="row align-items-center">
                                    <div class="col-auto border-end py-2 px-4">
                                        <time datetime="2025-10-10" class="text-center">
                                            <span class="fs-3 lh-1 d-block">OCT</span>
                                            <span class="fs-3 lh-1 d-block">10</span>
                                        </time>
                                    </div>
                                    <div class="col-auto">
                                        <time datetime="2025-10-10" class="text-center">
                                            <span class="fs-1 lh-1">25</span>
                                        </time>
                                    </div>
                                </div>
                                <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Nulla vitae elit libero, a
                                    pharetra augue.</p>
                                <p class="fst-italic text-end">Categoria demo</p>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endfor; ?>
        </div>

        <!-- Frecce -->
        <button class="carousel-control-prev" id="prevBtn" type="button">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" id="nextBtn" type="button">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>

        <!-- Indicatori -->
        <div class="carousel-indicators">
            <?php for ($i = 0; $i < $n; $i++): ?>
                <button type="button" aria-label="Slide <?= $i + 1 ?>" data-bs-target="#carouselItemsContainer"
                    data-bs-slide-to="<?= $i ?>" tabindex="0" <?= ($i === 0) ? 'class="active" aria-current="true"' : '' ?>>
                </button>
            <?php endfor; ?>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('carouselItemsContainer');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const items = container.querySelectorAll('.carousel-item-custom');
        const indicators = document.querySelectorAll('.carousel-indicators button');

        let currentIndex = 0;

        function getItemWidth() {
            return items[0] ? items[0].offsetWidth : 0;
        }

        function scrollToIndex(index) {
            const width = getItemWidth();
            container.scrollTo({
                left: width * index,
                behavior: 'smooth'
            });
            currentIndex = index;
            updateIndicators();
        }

        function updateIndicators() {
            indicators.forEach((btn, idx) => {
                btn.classList.toggle('active', idx === currentIndex);
            });
        }

        prevBtn.addEventListener('click', function () {
            let newIndex = (currentIndex === 0) ? items.length - 1 : currentIndex - 1;
            scrollToIndex(newIndex);
        });

        nextBtn.addEventListener('click', function () {
            let newIndex = (currentIndex === items.length - 1) ? 0 : currentIndex + 1;
            scrollToIndex(newIndex);
        });

        indicators.forEach((btn, idx) => {
            btn.addEventListener('click', function () {
                scrollToIndex(idx);
            });
        });

        window.addEventListener('resize', () => {
            scrollToIndex(currentIndex);
        });
    });
</script>

<style>
    #articles-carousel {
        background-color: #5680a0ff;
    }

    .carousel-control-prev,
    .carousel-control-next {
        width: 3rem;
        height: 3rem;
        top: auto;
        bottom: -3rem;
    }

    .carousel-indicators {
        bottom: -2.2rem;
        margin: 0;
        left: 50%;
        transform: translateX(-50%);
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-size: 100% 100%;
    }
</style>