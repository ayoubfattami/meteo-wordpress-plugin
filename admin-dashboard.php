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
    <style>
        /* Dashboard Meteo FR - UI/UX amélioré */
        .meteo-fr-dashboard-wrap {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            padding: 32px 28px 28px 28px;
            max-width: 600px;
            margin-top: 30px;
        }
        .meteo-fr-dashboard-wrap h1 {
            font-size: 2em;
            margin-bottom: 18px;
            color: #2271b1;
        }
        .meteo-fr-dashboard-wrap .form-table th {
            width: 120px;
            font-weight: 600;
            color: #222;
        }
        .meteo-fr-dashboard-wrap .form-table td {
            padding-bottom: 12px;
        }
        .meteo-fr-dashboard-wrap select,
        .meteo-fr-dashboard-wrap input[type="text"] {
            width: 220px;
            padding: 7px 10px;
            border-radius: 4px;
            border: 1px solid #ccd0d4;
            font-size: 1em;
            transition: border-color 0.2s;
        }
        .meteo-fr-dashboard-wrap select:focus,
        .meteo-fr-dashboard-wrap input[type="text"]:focus {
            border-color: #2271b1;
            outline: none;
        }
        .meteo-fr-dashboard-wrap .button-primary {
            background: linear-gradient(90deg, #2271b1 60%, #1d5e94 100%);
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
            padding: 8px 28px;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 2px rgba(34,113,177,0.08);
        }
        .meteo-fr-dashboard-wrap .button-primary:hover {
            background: linear-gradient(90deg, #1d5e94 60%, #2271b1 100%);
            box-shadow: 0 2px 8px rgba(34,113,177,0.12);
        }
        .meteo-fr-dashboard-wrap label {
            font-weight: 500;
            color: #444;
        }
        .meteo-fr-dashboard-wrap input[readonly] {
            background: #f6f7f7;
            cursor: pointer;
            border: 1px dashed #ccd0d4;
        }
        #meteo-fr-message {
            margin-top: 18px;
        }
        #meteo-fr-widget-preview {
            margin-top: 18px;
            padding: 18px;
            background: #f6f7f7;
            border-radius: 6px;
            border: 1px solid #e2e4e7;
        }
        @media (max-width: 700px) {
            .meteo-fr-dashboard-wrap {
                padding: 12px 4vw;
                max-width: 98vw;
            }
            .meteo-fr-dashboard-wrap .form-table th,
            .meteo-fr-dashboard-wrap .form-table td {
                display: block;
                width: 100%;
            }
        }
    </style>
    <div class="wrap meteo-fr-dashboard-wrap">
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
                <input type="text" id="meteo-fr-shortcode" readonly value="[meteo_fr_widget]" style="width:200px;" onclick="this.select();" />
                <button type="button" id="meteo-fr-copy-btn" style="margin-left:8px;padding:7px 16px;border-radius:4px;border:1px solid #ccd0d4;background:#e5f3ff;color:#2271b1;cursor:pointer;">Copier</button>
            </p>
        </form>
        <div id="meteo-fr-message"></div>
        <h2 style="margin-top:32px;color:#2271b1;">Aperçu du widget météo</h2>
        <div id="meteo-fr-widget-preview">
            <?php
            // Afficher l'aperçu du widget météo avec les valeurs actuelles
            echo do_shortcode('[meteo_fr_widget]');
            ?>
        </div>
    </div>
    <script>
    // UX: Animation lors du changement de ville
    document.addEventListener('DOMContentLoaded', function() {
        var countrySelect = document.getElementById('meteo_fr_country');
        var citySelect = document.getElementById('meteo_fr_city');
        if(countrySelect && citySelect) {
            countrySelect.addEventListener('change', function() {
                var nonce = '<?php echo esc_attr( wp_create_nonce('meteo_fr_nonce') ); ?>';
                var country = this.value;
                citySelect.style.opacity = 0.5;
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=meteo_fr_get_cities&country=' + encodeURIComponent(country) + '&nonce=' + nonce
                })
                .then(response => response.json())
                .then(cities => {
                    citySelect.innerHTML = '';
                    cities.forEach(function(city) {
                        var opt = document.createElement('option');
                        opt.value = city;
                        opt.textContent = city;
                        citySelect.appendChild(opt);
                    });
                    citySelect.style.opacity = 1;
                });
            });
        }
        // Bouton de copie du shortcode
        var copyBtn = document.getElementById('meteo-fr-copy-btn');
        var shortcodeInput = document.getElementById('meteo-fr-shortcode');
        if(copyBtn && shortcodeInput) {
            copyBtn.addEventListener('click', function() {
                shortcodeInput.select();
                shortcodeInput.setSelectionRange(0, 99999);
                document.execCommand('copy');
                copyBtn.textContent = 'Copié !';
                setTimeout(function() {
                    copyBtn.textContent = 'Copier';
                }, 1200);
            });
        }
    });
    </script>
    <?php
}

// AJAX : Récupérer les villes selon le pays
add_action('wp_ajax_meteo_fr_get_cities', function() {
    check_ajax_referer('meteo_fr_nonce', 'nonce');
    $country = isset($_POST['country']) ? sanitize_text_field( wp_unslash($_POST['country']) ) : '';
    $cities = meteo_fr_get_cities_by_country($country);
    wp_send_json($cities);
});

// Sauvegarder les options
add_action('admin_post_meteo_fr_save', function() {
    if (!current_user_can('manage_options')) wp_die('Non autorisé');
    check_admin_referer('meteo_fr_save', 'meteo_fr_nonce_field');
    $country = isset($_POST['meteo_fr_country']) ? sanitize_text_field( wp_unslash($_POST['meteo_fr_country']) ) : '';
    $city = isset($_POST['meteo_fr_city']) ? sanitize_text_field( wp_unslash($_POST['meteo_fr_city']) ) : '';
    update_option('meteo_fr_country', $country);
    update_option('meteo_fr_city', $city);
    wp_redirect(admin_url('admin.php?page=meteo_fr_dashboard&saved=1'));
    exit;
});
