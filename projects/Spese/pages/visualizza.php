<?php
include 'layout/menu_visualizza.php';
$modalita = $_GET['modalita'] ?? 'membri';
?>

<canvas id="pieChart" class="canvas"></canvas>

<?php
if ($modalita === 'membri') {
    $membri = $db->select($modalita);
    var_dump($membri);
    ?>
    <script>
        const ctx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Rosso', 'Verde'],
                datasets: [{
                    data: [3, 7],
                    backgroundColor: ['#d10000', '#13b613'], // Rosso e verde vivaci
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });
    </script>
    <?php
}
?>