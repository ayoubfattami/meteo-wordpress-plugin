<?php
if (!defined('ABSPATH')) exit;

// Shortcode [meteo_fr_widget]
add_shortcode('meteo_fr_widget', function() {
    $city = get_option('meteo_fr_city', 'Paris');
    $weather = meteo_fr_get_weather($city);
    if (!$weather || empty($weather['current'])) {
        return '<div class="meteo-widget">Météo indisponible.</div>';
    }
    $icon = esc_url('https:' . $weather['current']['condition']['icon']);
    $desc = esc_html($weather['current']['condition']['text']);
    $temp = esc_html($weather['current']['temp_c']);
    $city_name = esc_html($weather['location']['name']);
    ob_start();
    ?>
    <style>
    .meteo-widget {
        background: #fff;
        border: 1px solid #e2e4e7;
        border-radius: 10px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.06);
        padding: 22px 28px 14px 28px;
        min-width: 200px;
        min-height: 200px;
        text-align: center;
        display: inline-block;
        font-family: 'Segoe UI', Arial, sans-serif;
    }
    .meteo-widget .meteo-city {
        font-size: 1.35em;
        font-weight: bold;
        margin-bottom: 8px;
        color: #222;
    }
    .meteo-widget .meteo-icon img {
        width: 54px;
        height: 54px;
        margin: 10px 0 10px 0;
        display: inline-block;
        vertical-align: middle;
    }
    .meteo-widget .meteo-temp {
        font-size: 2.3em;
        color: #2ca0ff;
        font-weight: 500;
        margin: 8px 0 0 0;
    }
    .meteo-widget .meteo-desc {
        font-size: 1.1em;
        color: #444;
        margin-top: 8px;
        margin-bottom: 0;
    }
    .meteo-widget .meteo-copyright {
        font-size: 0.85em;
        color: #888;
        margin-top: 14px;
        margin-bottom: 0;
        text-align: center;
        line-height: 1.3;
    }
    .meteo-widget .meteo-copyright a {
        color: #2271b1;
        text-decoration: none;
        font-weight: 500;
    }
    .meteo-widget .meteo-copyright a:hover {
        text-decoration: underline;
        color: #2ca0ff;
    }
    /* Centrage du widget dans son bloc parent */
    .meteo-widget-center {
        text-align: center;
        width: 100%;
        display: block;
    }
    </style>
    <div class="meteo-widget-center">
        <div class="meteo-widget">
            <div class="meteo-city"><?php echo $city_name; ?></div>
            <div class="meteo-icon"><img src="<?php echo $icon; ?>" alt="<?php echo $desc; ?>" /></div>
            <div class="meteo-temp"><?php echo $temp; ?>°C</div>
            <div class="meteo-desc"><?php echo $desc; ?></div>
            <div class="meteo-copyright">
                Données météo par <a href="https://meteomaroc.fr" target="_blank" rel="noopener">meteomaroc.fr</a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
});
