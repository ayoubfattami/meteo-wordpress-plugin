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
    <div class="meteo-widget">
        <div class="meteo-city"><?php echo $city_name; ?></div>
        <div class="meteo-icon"><img src="<?php echo $icon; ?>" alt="<?php echo $desc; ?>" /></div>
        <div class="meteo-temp"><?php echo $temp; ?>°C</div>
        <div class="meteo-desc"><?php echo $desc; ?></div>
    </div>
    <?php
    return ob_get_clean();
});
