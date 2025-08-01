jQuery(document).ready(function($) {
    $('#meteo_fr_country').on('change', function() {
        var country = $(this).val();
        $('#meteo_fr_city').html('<option>Chargement...</option>');
        $.post(meteoFR.ajax_url, {
            action: 'meteo_fr_get_cities',
            country: country,
            nonce: meteoFR.nonce
        }, function(data) {
            var options = '';
            if (data.length > 0) {
                data.forEach(function(city) {
                    options += '<option value="' + city + '">' + city + '</option>';
                });
            } else {
                options = '<option>Aucune ville trouvée</option>';
            }
            $('#meteo_fr_city').html(options);
        });
    });

    // Soumission AJAX du formulaire (optionnel, sinon submit classique)
    $('#meteo-fr-form').on('submit', function(e) {
        // Laisser le submit classique pour la simplicité
    });
});
