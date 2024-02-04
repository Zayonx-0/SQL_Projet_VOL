<!DOCTYPE html>
<html>
<head>
    <title>Résultats de recherche d'aéroport</title>
</head>
<body>
    <?php
    // Connect to the database
    $dsn = 'mysql:host=localhost;port=8889;dbname=basevol';
    $username = 'root';
    $password = 'root';

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $callsign = $_GET['rechercheaeroport'];

    // Query to retrieve airport information
    $query1 = $pdo->prepare("SELECT aeroports.nom, aeroports.ville, aeroports.latitude, aeroports.longitude
                            FROM aeroports
                            WHERE aeroports.codeIATA = :callsign");
    $query1->bindParam(':callsign', $callsign);
    $query1->execute();
    $airport = $query1->fetch();

    // Query to retrieve departing flights and corresponding arrival airports
    $query2 = $pdo->prepare("SELECT v.callSign, aeroports.nom AS nom_aeroport_arrivee
                            FROM vols v
                            JOIN aeroports ON v.codeIATAArr = aeroports.codeIATA
                            WHERE v.codeIATADep = :callsign");
    $query2->bindParam(':callsign', $callsign);
    $query2->execute();
    $departingFlights = $query2->fetchAll();

    // Query to retrieve arriving flights and corresponding departure airports
    $query3 = $pdo->prepare("SELECT v.callSign, aeroports.nom AS departure_airport
                            FROM vols v
                            JOIN aeroports ON v.codeIATADep = aeroports.codeIATA
                            WHERE v.codeIATAArr = :callsign");
    $query3->bindParam(':callsign', $callsign);
    $query3->execute();
    $arrivingFlights = $query3->fetchAll();

    // Query to retrieve all airports for the dropdown list
    $query4 = $pdo->prepare("SELECT codeIATA, nom FROM aeroports");
    $query4->execute();
    $airports = $query4->fetchAll();
    ?>

    <h1>Résultats de recherche d'aéroport</h1>

    <form action="testrecherche.php" method="get">
        <label for="rechercheaeroport">Sélectionnez un aéroport :</label>
        <select name="rechercheaeroport" id="rechercheaeroport" onchange="this.form.submit()">
            <?php foreach ($airports as $airportOption): ?>
                <option value="<?php echo $airportOption['codeIATA']; ?>" <?php if ($airportOption['codeIATA'] == $callsign) echo 'selected'; ?>>
                    <?php echo $airportOption['nom']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" formaction="resultatsrechercheaeroport.php?rechercheaeroport=<?php echo $airport['codeIATA']; ?>">OK</button>
    </form>

    <h2>Informations sur l'aéroport</h2>
    <p>Identifiant: <?php echo $callsign; ?></p>
    <p>Nom: <?php echo $airport['nom']; ?></p>
    <p>Ville: <?php echo $airport['ville']; ?></p>
    <p>Latitude: <?php echo $airport['latitude']; ?></p>
    <p>Longitude: <?php echo $airport['longitude']; ?></p>

    <h2>Vols au départ de cet aéroport</h2>
    <ul>
        <?php foreach ($departingFlights as $flight): ?>
            <li>Call Sign: <?php echo $flight['callSign']; ?>, Aéroport d'arrivée: <?php echo $flight['nom_aeroport_arrivee']; ?></li>
        <?php endforeach; ?>
    </ul>

    <h2>Vols arrivant à cet aéroport</h2>
    <ul>
        <?php foreach ($arrivingFlights as $flight): ?>
            <li>Call Sign: <?php echo $flight['callSign']; ?>, Aéroport de départ: <?php echo $flight['departure_airport']; ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>


