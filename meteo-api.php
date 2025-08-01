<?php
if (!defined('ABSPATH')) exit;

// Clé API cachée
function meteo_fr_get_api_key() {
    return 'f86567b5c2e7414996b142126252107';
}

// Solution classique et optimale :
// Utiliser une liste statique des pays principaux (ceux supportés par WeatherAPI)
// et pour les villes, ne faire la recherche que sur quelques lettres stratégiques et grandes villes connues du pays.

function meteo_fr_get_countries() {
    // Liste statique des pays principaux les plus consultés
    return [
        'France' => 'France',
        'Morocco' => 'Morocco',
        'Belgium' => 'Belgium',
        'Canada' => 'Canada',
        'United States of America' => 'United States of America',
        'United Kingdom' => 'United Kingdom',
        'Germany' => 'Germany',
        'Spain' => 'Spain',
        'Italy' => 'Italy',
        'Switzerland' => 'Switzerland',
        'Netherlands' => 'Netherlands',
        'Portugal' => 'Portugal',
        'Turkey' => 'Turkey',
        'Russia' => 'Russia',
        'China' => 'China',
        'Japan' => 'Japan',
        'Australia' => 'Australia',
        'Brazil' => 'Brazil',
        'India' => 'India',
        'Egypt' => 'Egypt',
        'United Arab Emirates' => 'United Arab Emirates',
        'Mexico' => 'Mexico',
        'South Africa' => 'South Africa',
        'Argentina' => 'Argentina',
        'Indonesia' => 'Indonesia',
        'Saudi Arabia' => 'Saudi Arabia',
        'Thailand' => 'Thailand',
        'South Korea' => 'South Korea',
        'Poland' => 'Poland',
        'Sweden' => 'Sweden',
        // ...ajoutez d'autres pays si besoin...
    ];
}

function meteo_fr_get_cities_by_country($country) {
    $api_key = meteo_fr_get_api_key();
    $cities = [];
    $letters = ['a', 'e', 'i', 'o', 'u', 'y', 'm', 'b', 'c', 'd', 'r', 's', 't', 'l', 'n'];
    $big_cities = [
        'France' => [
            'Paris', 'Marseille', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Montpellier', 'Bordeaux', 'Lille',
            'Rennes', 'Reims', 'Le Havre', 'Saint-Étienne', 'Toulon', 'Grenoble', 'Dijon', 'Angers', 'Nîmes', 'Villeurbanne'
        ],
        'Morocco' => [
            'Casablanca', 'Rabat', 'Marrakech', 'Fes', 'Agadir', 'Tangier', 'Oujda', 'Kenitra', 'Tetouan', 'Safi',
            'Mohammedia', 'Khouribga', 'El Jadida', 'Beni Mellal', 'Taza', 'Settat', 'Larache', 'Ksar El Kebir', 'Guelmim'
        ],
        'Belgium' => [
            'Brussels', 'Antwerp', 'Ghent', 'Charleroi', 'Liege', 'Bruges', 'Namur', 'Leuven', 'Mons', 'Aalst',
            'Mechelen', 'La Louvière', 'Kortrijk', 'Hasselt', 'Sint-Niklaas', 'Ostend', 'Tournai', 'Genk', 'Roeselare'
        ],
        'Canada' => [
            'Toronto', 'Montreal', 'Vancouver', 'Calgary', 'Ottawa', 'Edmonton', 'Mississauga', 'Winnipeg', 'Quebec', 'Hamilton',
            'Brampton', 'Surrey', 'Kitchener', 'Halifax', 'London', 'Victoria', 'Markham', 'Windsor', 'Gatineau'
        ],
        'United States of America' => [
            'New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'San Jose',
            'Austin', 'Jacksonville', 'Fort Worth', 'Columbus', 'Charlotte', 'San Francisco', 'Indianapolis', 'Seattle', 'Denver'
        ],
        'United Kingdom' => [
            'London', 'Birmingham', 'Manchester', 'Liverpool', 'Leeds', 'Sheffield', 'Bristol', 'Glasgow', 'Leicester', 'Edinburgh',
            'Coventry', 'Kingston upon Hull', 'Bradford', 'Cardiff', 'Belfast', 'Stoke-on-Trent', 'Wolverhampton', 'Nottingham', 'Plymouth'
        ],
        'Germany' => [
            'Berlin', 'Hamburg', 'Munich', 'Cologne', 'Frankfurt', 'Stuttgart', 'Düsseldorf', 'Dortmund', 'Essen', 'Leipzig',
            'Bremen', 'Dresden', 'Hanover', 'Nuremberg', 'Duisburg', 'Bochum', 'Wuppertal', 'Bielefeld', 'Bonn'
        ],
        'Spain' => [
            'Madrid', 'Barcelona', 'Valencia', 'Seville', 'Zaragoza', 'Malaga', 'Murcia', 'Palma', 'Las Palmas', 'Bilbao',
            'Alicante', 'Cordoba', 'Valladolid', 'Vigo', 'Gijon', 'Hospitalet', 'A Coruña', 'Vitoria-Gasteiz', 'Granada'
        ],
        'Italy' => [
            'Rome', 'Milan', 'Naples', 'Turin', 'Palermo', 'Genoa', 'Bologna', 'Florence', 'Bari', 'Catania',
            'Venice', 'Verona', 'Messina', 'Padua', 'Trieste', 'Taranto', 'Brescia', 'Prato', 'Parma'
        ],
        'Switzerland' => [
            'Zurich', 'Geneva', 'Basel', 'Bern', 'Lausanne', 'Winterthur', 'Lucerne', 'St. Gallen', 'Lugano', 'Biel/Bienne',
            'Thun', 'Köniz', 'La Chaux-de-Fonds', 'Schaffhausen', 'Fribourg', 'Chur', 'Neuchâtel', 'Vernier', 'Uster'
        ],
        'Netherlands' => [
            'Amsterdam', 'Rotterdam', 'The Hague', 'Utrecht', 'Eindhoven', 'Tilburg', 'Groningen', 'Almere', 'Breda', 'Nijmegen',
            'Enschede', 'Apeldoorn', 'Haarlem', 'Arnhem', 'Zaanstad', 'Amersfoort', 'Haarlemmermeer', 'Zwolle', 'Leiden'
        ],
        'Portugal' => [
            'Lisbon', 'Porto', 'Vila Nova de Gaia', 'Amadora', 'Braga', 'Coimbra', 'Funchal', 'Setúbal', 'Agualva-Cacém', 'Almada',
            'Queluz', 'Cacém', 'Vila Nova de Famalicão', 'Viseu', 'Odivelas', 'Aveiro', 'Leiria', 'Barreiro', 'Rio Tinto'
        ],
        'Turkey' => [
            'Istanbul', 'Ankara', 'Izmir', 'Bursa', 'Adana', 'Gaziantep', 'Konya', 'Antalya', 'Kayseri', 'Mersin',
            'Eskisehir', 'Diyarbakir', 'Samsun', 'Denizli', 'Sanliurfa', 'Malatya', 'Kahramanmaras', 'Erzurum', 'Van'
        ],
        'Russia' => [
            'Moscow', 'Saint Petersburg', 'Novosibirsk', 'Yekaterinburg', 'Nizhny Novgorod', 'Kazan', 'Chelyabinsk', 'Omsk', 'Samara', 'Rostov-on-Don',
            'Ufa', 'Krasnoyarsk', 'Perm', 'Voronezh', 'Volgograd', 'Krasnodar', 'Saratov', 'Tyumen', 'Tolyatti'
        ],
        'China' => [
            'Beijing', 'Shanghai', 'Chongqing', 'Tianjin', 'Guangzhou', 'Shenzhen', 'Chengdu', 'Nanjing', 'Wuhan', 'Xi\'an',
            'Hangzhou', 'Dongguan', 'Foshan', 'Shenyang', 'Harbin', 'Qingdao', 'Dalian', 'Jinan', 'Zhengzhou'
        ],
        'Japan' => [
            'Tokyo', 'Yokohama', 'Osaka', 'Nagoya', 'Sapporo', 'Kobe', 'Kyoto', 'Fukuoka', 'Kawasaki', 'Saitama',
            'Hiroshima', 'Sendai', 'Kitakyushu', 'Chiba', 'Sakai', 'Niigata', 'Hamamatsu', 'Shizuoka', 'Okayama'
        ],
        'Australia' => [
            'Sydney', 'Melbourne', 'Brisbane', 'Perth', 'Adelaide', 'Gold Coast', 'Canberra', 'Newcastle', 'Wollongong', 'Logan City',
            'Geelong', 'Hobart', 'Townsville', 'Cairns', 'Toowoomba', 'Darwin', 'Launceston', 'Albury', 'Ballarat'
        ],
        'Brazil' => [
            'São Paulo', 'Rio de Janeiro', 'Brasília', 'Salvador', 'Fortaleza', 'Belo Horizonte', 'Manaus', 'Curitiba', 'Recife', 'Goiânia',
            'Belém', 'Porto Alegre', 'Guarulhos', 'Campinas', 'São Luís', 'São Gonçalo', 'Maceió', 'Duque de Caxias', 'Natal'
        ],
        'India' => [
            'Mumbai', 'Delhi', 'Bangalore', 'Hyderabad', 'Ahmedabad', 'Chennai', 'Kolkata', 'Surat', 'Pune', 'Jaipur',
            'Lucknow', 'Kanpur', 'Nagpur', 'Indore', 'Thane', 'Bhopal', 'Visakhapatnam', 'Pimpri-Chinchwad', 'Patna'
        ],
        'Egypt' => [
            'Cairo', 'Alexandria', 'Giza', 'Shubra El Kheima', 'Port Said', 'Suez', 'El Mahalla El Kubra', 'Luxor', 'Mansoura', 'Tanta',
            'Asyut', 'Ismailia', 'Fayyum', 'Zagazig', 'Aswan', 'Damietta', 'Damanhur', 'Beni Suef', 'Qena'
        ],
        'United Arab Emirates' => [
            'Dubai', 'Abu Dhabi', 'Sharjah', 'Al Ain', 'Ajman', 'Ras Al Khaimah', 'Fujairah', 'Umm Al Quwain', 'Khor Fakkan', 'Kalba',
            'Dibba Al-Fujairah', 'Dibba Al-Hisn', 'Jebel Ali', 'Al Madam', 'Al Dhaid', 'Al Rams', 'Ghayathi', 'Liwa Oasis', 'Ruwais'
        ],
        'Mexico' => [
            'Mexico City', 'Ecatepec', 'Guadalajara', 'Puebla', 'Juárez', 'Tijuana', 'León', 'Zapopan', 'Monterrey', 'Nezahualcóyotl',
            'Mérida', 'San Luis Potosí', 'Aguascalientes', 'Hermosillo', 'Saltillo', 'Chihuahua', 'Morelia', 'Culiacán', 'Querétaro'
        ],
        'South Africa' => [
            'Johannesburg', 'Cape Town', 'Durban', 'Pretoria', 'Port Elizabeth', 'Bloemfontein', 'Nelspruit', 'Kimberley', 'Polokwane', 'Pietermaritzburg',
            'East London', 'Welkom', 'Uitenhage', 'Vereeniging', 'Klerksdorp', 'George', 'Witbank', 'Krugersdorp', 'Rustenburg'
        ],
        'Argentina' => [
            'Buenos Aires', 'Córdoba', 'Rosario', 'Mendoza', 'La Plata', 'Tucumán', 'Mar del Plata', 'Salta', 'Santa Fe', 'San Juan',
            'Resistencia', 'Santiago del Estero', 'Corrientes', 'Bahía Blanca', 'Paraná', 'Posadas', 'San Salvador de Jujuy', 'Neuquén', 'Formosa'
        ],
        'Indonesia' => [
            'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Bekasi', 'Depok', 'Semarang', 'Tangerang', 'Palembang', 'Makassar',
            'South Tangerang', 'Batam', 'Pekanbaru', 'Bogor', 'Padang', 'Denpasar', 'Malang', 'Samarinda', 'Tasikmalaya'
        ],
        'Saudi Arabia' => [
            'Riyadh', 'Jeddah', 'Mecca', 'Medina', 'Dammam', 'Khobar', 'Tabuk', 'Buraidah', 'Khamis Mushait', 'Hofuf',
            'Al Mubarraz', 'Hail', 'Najran', 'Abha', 'Yanbu', 'Sakakah', 'Jubail', 'Al Qatif', 'Al Khafji'
        ],
        'Thailand' => [
            'Bangkok', 'Nonthaburi', 'Nakhon Ratchasima', 'Chiang Mai', 'Hat Yai', 'Udon Thani', 'Pak Kret', 'Khon Kaen', 'Ubon Ratchathani', 'Nakhon Si Thammarat',
            'Nakhon Sawan', 'Phitsanulok', 'Pattaya', 'Songkhla', 'Surat Thani', 'Rayong', 'Samut Prakan', 'Trang', 'Lampang'
        ],
        'South Korea' => [
            'Seoul', 'Busan', 'Incheon', 'Daegu', 'Daejeon', 'Gwangju', 'Suwon', 'Ulsan', 'Changwon', 'Seongnam',
            'Goyang', 'Yongin', 'Bucheon', 'Ansan', 'Cheongju', 'Jeonju', 'Cheonan', 'Namyangju', 'Hwaseong'
        ],
        'Poland' => [
            'Warsaw', 'Kraków', 'Łódź', 'Wrocław', 'Poznań', 'Gdańsk', 'Szczecin', 'Bydgoszcz', 'Lublin', 'Białystok',
            'Katowice', 'Gdynia', 'Częstochowa', 'Radom', 'Sosnowiec', 'Toruń', 'Kielce', 'Gliwice', 'Zabrze'
        ],
        'Sweden' => [
            'Stockholm', 'Gothenburg', 'Malmö', 'Uppsala', 'Västerås', 'Örebro', 'Linköping', 'Helsingborg', 'Jönköping', 'Norrköping',
            'Lund', 'Umeå', 'Gävle', 'Borås', 'Eskilstuna', 'Södertälje', 'Karlstad', 'Täby', 'Växjö'
        ],
        // ...ajoutez d'autres pays/villes si besoin...
    ];
    // Recherche par lettres
    foreach ($letters as $letter) {
        $url = "http://api.weatherapi.com/v1/search.json?key={$api_key}&q={$letter}";
        $response = wp_remote_get($url);
        if (is_wp_error($response)) continue;
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (is_array($data)) {
            foreach ($data as $item) {
                if (isset($item['country']) && $item['country'] === $country && !empty($item['name'])) {
                    $cities[$item['name']] = $item['name'];
                }
            }
        }
    }
    // Recherche par grandes villes connues du pays
    if (isset($big_cities[$country])) {
        foreach ($big_cities[$country] as $city) {
            $url = "http://api.weatherapi.com/v1/search.json?key={$api_key}&q=" . urlencode($city);
            $response = wp_remote_get($url);
            if (is_wp_error($response)) continue;
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (isset($item['country']) && $item['country'] === $country && !empty($item['name'])) {
                        $cities[$item['name']] = $item['name'];
                    }
                }
            }
        }
    }
    asort($cities);
    return array_values($cities);
}

// Récupérer la météo actuelle pour une ville
function meteo_fr_get_weather($city) {
    $api_key = meteo_fr_get_api_key();
    $url = "http://api.weatherapi.com/v1/current.json?key={$api_key}&q=" . urlencode($city) . "&lang=fr";
    $response = wp_remote_get($url);
    if (is_wp_error($response)) return false;
    $data = json_decode(wp_remote_retrieve_body($response), true);
    return $data;
}
