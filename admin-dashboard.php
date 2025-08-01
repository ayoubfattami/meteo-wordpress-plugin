<?php
if (!defined('ABSPATH')) exit;

// Ajouter le menu admin
add_action('admin_menu', function() {
    add_menu_page(
        'Météo FR V1',
        'Météo FR V1',
        'manage_options',
        'meteo_fr_dashboard',
        'meteo_fr_dashboard_page',
        'dashicons-cloud',
        80
    );
});

// Page dashboard
function meteo_fr_dashboard_page() {
    $countries = meteo_fr_get_countries();
    $selected_country = get_option('meteo_fr_country', 'France');
    $selected_city = get_option('meteo_fr_city', '');
    $cities = meteo_fr_get_cities_by_country($selected_country);
    ?>
    <div class="wrap">
        <h1>Météo FR - Réglages</h1>
        <form id="meteo-fr-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('meteo_fr_save', 'meteo_fr_nonce_field'); ?>
            <input type="hidden" name="action" value="meteo_fr_save" />
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="meteo_fr_country">Pays</label></th>
                    <td>
                        <select name="meteo_fr_country" id="meteo_fr_country">
                            <?php foreach ($countries as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($selected_country, $key); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="meteo_fr_city">Ville</label></th>
                    <td>
                        <select name="meteo_fr_city" id="meteo_fr_city">
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo esc_attr($city); ?>" <?php selected($selected_city, $city); ?>><?php echo esc_html($city); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <p>
                <button type="submit" class="button button-primary">Sauvegarder</button>
            </p>
            <p>
                <label>Shortcode à copier&nbsp;:</label>
                <input type="text" readonly value="[meteo_fr_widget]" style="width:200px;" onclick="this.select();" />
            </p>
        </form>
        <div id="meteo-fr-message"></div>
        <h2>Aperçu du widget météo</h2>
        <div id="meteo-fr-widget-preview">
            <?php
            // Afficher l'aperçu du widget météo avec les valeurs actuelles
            echo do_shortcode('[meteo_fr_widget]');
            ?>
        </div>
    </div>
    <?php
}

// AJAX : Récupérer les villes selon le pays
add_action('wp_ajax_meteo_fr_get_cities', function() {
    check_ajax_referer('meteo_fr_nonce', 'nonce');
    $country = sanitize_text_field($_POST['country']);
    $cities = meteo_fr_get_cities_by_country($country);
    wp_send_json($cities);
});

// Sauvegarder les options
add_action('admin_post_meteo_fr_save', function() {
    if (!current_user_can('manage_options')) wp_die('Non autorisé');
    check_admin_referer('meteo_fr_save', 'meteo_fr_nonce_field');
    $country = sanitize_text_field($_POST['meteo_fr_country']);
    $city = sanitize_text_field($_POST['meteo_fr_city']);
    update_option('meteo_fr_country', $country);
    update_option('meteo_fr_city', $city);
    wp_redirect(admin_url('admin.php?page=meteo_fr_dashboard&saved=1'));
    exit;
});
