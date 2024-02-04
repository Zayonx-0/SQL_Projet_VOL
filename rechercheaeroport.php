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

    // Query to retrieve all airports for the dropdown list
    $query = $pdo->prepare("SELECT codeIATA, nom FROM aeroports");
    $query->execute();
    $airports = $query->fetchAll();
    ?>

    <h1>Résultats de recherche d'aéroport</h1>

    <form action="resultatsrechercheaeroport.php" method="get">
        <label for="rechercheaeroport">Sélectionnez un aéroport :</label>
        <select name="rechercheaeroport" id="rechercheaeroport">
            <?php foreach ($airports as $airportOption): ?>
                <option value="<?php echo $airportOption['codeIATA']; ?>">
                    <?php echo $airportOption['nom']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">OK</button>
    </form>
</body>
</html>


