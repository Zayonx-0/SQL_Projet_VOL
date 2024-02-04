<?php

// Connect to the database
$dsn = 'mysql:host=localhost;port=8889;dbname=basevol';
$username = 'root';
$password = 'root';


$pdo = new PDO($dsn, $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$callsign = $_GET['recherchevol'];

if (empty($callsign)) {
    echo "Erreur : veuillez choisir un vol <br>";
    echo "<a href='recherchevol.php'>Accéder à recherchevol.php</a>";
    exit;
}

$query = $pdo->query("
SELECT r.callSign AS callsign, vols.heureArr AS heureArr, vols.heureDep AS heureDep, vols.compagnie AS compagnie
FROM releves r
JOIN vols ON r.callSign = vols.callSign
WHERE r.callSign = '$callsign';
");

$result = $query->fetch();

echo "Avion callsign $callsign <br>";
echo "Compagnie : $result[compagnie] <br>";
echo "Heure de départ prévue : $result[heureDep] <br>";
echo "Heure d'arrivée prévue : $result[heureArr] <br>";

$query2 = $pdo->query("
SELECT AA.ville AS VillArr, AD.Ville AS VilleDep, callsign
FROM (vols INNER JOIN aeroports AS AD ON vols.codeIATADep = AD.codeIATA)
INNER JOIN aeroports AS AA ON vols.codeIATAArr = AA.codeIATA
WHERE callsign = '$callsign';
");

$result2 = $query2->fetch();

echo "Aéroport de départ : $result2[VilleDep] <br>";
echo "Aéroport d'arrivée : $result2[VillArr] <br>";

$query3 = $pdo->query("
SELECT heure, latitude, longitude
FROM releves
WHERE callSign = '$callsign'
ORDER BY heure DESC;
");

echo "Relevés : <br><br><br>";
while ($result3 = $query3->fetch()) {
    echo "Heure : $result3[heure] <br>";
    echo "Latitude : $result3[latitude] <br>";
    echo "Longitude : $result3[longitude] <br>";
}

echo "<br><br><br>";

$query4 = $pdo->query("
SELECT heure, latitude, longitude
FROM releves
WHERE callSign = '$callsign'
ORDER BY heure ASC
LIMIT 1;
");

$result4 = $query4->fetch();

echo "Heure de départ réelle : $result4[heure] <br>";

$query5 = $pdo->query("
SELECT heure, latitude, longitude
FROM releves
WHERE callSign = '$callsign'
ORDER BY heure DESC
LIMIT 1;
");

$result5 = $query5->fetch();

echo "Heure d'arrivée réelle : $result5[heure] <br>";

// A.2. ajouter une carte centrée sur la position d’arrivée du vol
echo "<div id='map'></div>";

// A.3. ajouter un marqueur sur la position d’arrivée du vol
echo "<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: $result2[VillArr], lng: $result2[VillArr]},
            zoom: 8
        });
        
        var marker = new google.maps.Marker({
            position: {lat: $result2[VillArr], lng: $result2[VillArr]},
            map: map,
            title: 'Arrival Position'
        });
    }
</script>";

// A.4. ajouter un marqueur sur la position de départ du vol
echo "<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: $result2[VilleDep], lng: $result2[VilleDep]},
            zoom: 8
        });
        
        var marker = new google.maps.Marker({
            position: {lat: $result2[VilleDep], lng: $result2[VilleDep]},
            map: map,
            title: 'Departure Position'
        });
    }
</script>";

// A.5. ajouter des marqueurs, chacun correspondant à une des positions (= latitude et longitude) relevées pour le vol.
$query6 = $pdo->query("
SELECT heure, latitude, longitude
FROM releves
WHERE callSign = '$callsign';
");

echo "<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: $result2[VillArr], lng: $result2[VillArr]},
            zoom: 8
        });
        
        var markerArrival = new google.maps.Marker({
            position: {lat: $result2[VillArr], lng: $result2[VillArr]},
            map: map,
            title: 'Arrival Position'
        });
        
        var markerDeparture = new google.maps.Marker({
            position: {lat: $result2[VilleDep], lng: $result2[VilleDep]},
            map: map,
            title: 'Departure Position'
        });
        
        while ($result6 = $query6->$fetch()) {
            var marker = new google.maps.Marker({
                position: {lat: $result6[latitude], lng: $result6[longitude]},
                map: map,
                title: 'Position'
            });
        }
    }
</script>";

?>


