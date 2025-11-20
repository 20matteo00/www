<?php
class Helper
{

    public static function getSeasons($json)
    {
        $seasons = [];
        foreach ($json as $key => $season) {
            $seasons[] = $key;
        }
        return $seasons;
    }

    public static function viewDaysForSeason($json, $season)
    {
        $giornate[] = [];
        ?>
        <div class="row">
            <?php foreach ($json[$season]['giornate'] as $giornata => $matches): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Giornata <?= $giornata ?></div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($matches as $match): ?>
                                    <li class="list-group-item">
                                        <?= $match['data'] ?>
                                        <?= $match['squadre'] ?>
                                        <?= $match['risultato'] ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    public static function viewTableForSeason($json, $season)
    {

    }
}


?>