<?php
// Recupera il rating inviato via POST, altrimenti 0
$rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
$stars = 5; // Numero totale di stelle
?>

<div class="container">
  <p class="mb-1 fs-5">Valuta il nostro servizio:</p>

  <!-- 👉 FLEX: elimina gli spazi tra i bottoni -->
  <form action="" method="post" class="d-inline-flex align-items-center gap-0" style="line-height:1;">
    <?php for ($i = 0; $i < $stars; $i++) { ?>
      <button type="submit" name="rating" value="<?php echo $i + 1; ?>"
        class="star-btn bi fs-3 px-2 <?php echo ($i < $rating) ? 'bi-star-fill gold' : 'bi-star'; ?>">
      </button>
    <?php } ?>
  </form>

  <?php if ($rating > 0): ?>
    <span class="ms-4 fs-5">Hai dato un voto di: <strong><?php echo $rating . "/" . $stars; ?> stelle</strong></span>
  <?php endif; ?>
</div>

<style>
  /* --- Layout pulito --- */
  .star-btn {
    background: none;
    border: none;
    padding: 0;
    margin: 0;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  /* niente spacing tra i bottoni */
  form.d-inline-flex {
    font-size: 0;
  }

  .bi {
    transition: color 0.2s ease;
    font-size: 1.8rem;
  }

  .bi:hover {
    cursor: pointer;
  }

  .gold {
    color: gold;
  }
</style>

<script>
  // Prende solo le stelle dentro il form
  const stars = document.querySelectorAll('form .bi');
  const rating = <?php echo $rating; ?>;

  // Funzione che colora fino a una certa stella
  function colorStars(limit) {
    stars.forEach((s, i) => {
      s.classList.toggle('bi-star-fill', i <= limit);
      s.classList.toggle('bi-star', i > limit);
      s.classList.toggle('gold', i <= limit);
    });
  }

  // Al passaggio del mouse
  stars.forEach((star, index) => {
    star.addEventListener('mouseenter', () => colorStars(index));
    star.addEventListener('mouseleave', () => colorStars(rating - 1));
  });

  // Imposta lo stato iniziale (mantiene il rating inviato)
  colorStars(rating - 1);
</script>
