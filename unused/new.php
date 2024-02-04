<?php

// Connect to the database
$dsn = 'mysql:host=localhost;port=8889;dbname=basevol';
$username = 'root';
$password = 'root';


$pdo = new PDO($dsn, $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$callsign = $_GET['recherchevol'];

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

echo "Position d'arrivée du vol :";

// Add Google Maps
echo "<div id='map' style='width: 100%; height: 400px;'></div>";

// ...

$query5 = $pdo->query("
SELECT heure, latitude, longitude
FROM releves
WHERE callSign = '$callsign'
ORDER BY heure DESC
LIMIT 1;
");

$result5 = $query5->fetch();

$latitudeLast = $result5['latitude'];
$longitudeLast = $result5['longitude'];

$query6 = $pdo->query("
SELECT heure, latitude, longitude
FROM releves
WHERE callSign = '$callsign'
ORDER BY heure ASC
LIMIT 1;
");

$result6 = $query6->fetch();

$latitude = $result6['latitude'];
$longitude = $result6['longitude'];



// ...

// Generate Google Maps JavaScript code
echo "<script>
    function initMap() {
        var location = {lat: $latitudeLast, lng: $longitudeLast};
        var arrivalofvol = {lat: $latitudeLast, lng: $longitudeLast};
        var departureofvol = {lat: $latitude, lng: $longitude};
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: location
        });
        var marker = new google.maps.Marker({
            position: location,
            map: map
        })
        var marker2 = new google.maps.Marker({
            position: arrivalofvol,
            map: map
        })
        var marker3 = new google.maps.Marker({
            position: departureofvol,
            map: map
        })
        
    
";

$query7 = $pdo->query("
SELECT heure, latitude, longitude
FROM releves
WHERE callSign = '$callsign'
ORDER BY heure DESC;
");

while ($result7 = $query7->fetch()) {
    $latitudePosition = $result7['latitude'];
    $longitudePosition = $result7['longitude'];
    echo "var position = {lat: $latitudePosition, lng: $longitudePosition};";
    echo "var markerPosition = new google.maps.Marker({position: position, map: map});";
}

echo "}</script>";



// Load Google Maps API and initialize the map
echo "<script src='https://maps.googleapis.com/maps/api/js?key=AIzaSyDLci_HdegGFcMDEma0GHZvobV7byNJCvo&callback=initMap' async defer></script>";




?>

