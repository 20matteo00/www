<?php
/* Librerie e percorsi */
$libraries = [
    'bootstrap' => [
        'css' => '/libraries/bootstrap/css/bootstrap.min.css',
        'js' => '/libraries/bootstrap/js/bootstrap.bundle.min.js',
        'icons' => '/libraries/bootstrap/icons/bootstrap-icons.min.css'
    ],
    'fontawesome' => [
        'icons' => '/libraries/fontawesome/css/all.min.css'
    ],
    'leaflet' => [
        'css' => '/libraries/leaflet/dist/leaflet.css',
        'js' => '/libraries/leaflet/dist/leaflet.js'
    ],
    'leaflet_markercluster' => [
        'css' => '/libraries/leaflet_markercluster/dist/MarkerCluster.css',
        'js' => '/libraries/leaflet_markercluster/src/MarkerCluster.js'
    ],
    'chart' => [
        'js' => '/libraries/chart/chart.js'
    ]
];

/* Funzioni helper per generare i tag */
function css_tag($path) {
    return "<link href='$path' rel='stylesheet'>";
}

function js_tag($path) {
    return "<script src='$path'></script>";
}

/* Generazione tag già pronti */
$bootstrap_css = css_tag($libraries['bootstrap']['css']);
$bootstrap_js = js_tag($libraries['bootstrap']['js']);
$bootstrap_icons = css_tag($libraries['bootstrap']['icons']);
$fontawesome_css = css_tag($libraries['fontawesome']['icons']);
$leaflet_css = css_tag($libraries['leaflet']['css']);
$leaflet_js = js_tag($libraries['leaflet']['js']);
$leaflet_markercluster_css = css_tag($libraries['leaflet_markercluster']['css']);
$leaflet_markercluster_js = js_tag($libraries['leaflet_markercluster']['js']);
$chart_js = js_tag($libraries['chart']['js']);
?>
