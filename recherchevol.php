<html>
    <head>

</head>
<body>
<?php
// Connect to the database
$dsn = 'mysql:host=localhost;port=8889;dbname=basevol';
$username = 'root';
$password = 'root';


    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch flights from the database
    $query = $pdo->query("
    SELECT `vols`.`codeIATADep`, `vols`.`codeIATAArr`, `vols`.`callSign`
    FROM `vols`;

");

$query2 = $pdo->query("
SELECT AA.ville AS VillArr, AD.Ville AS VilleDep, callsign
FROM (vols INNER JOIN aeroports AS AD ON vols.codeIATADep = AD.codeIATA)
INNER JOIN aeroports AS AA ON vols.codeIATAArr = AA.codeIATA
");

?>

<form action="resultatsrecherchevol.php" method="GET">
<select name="recherchevol">
<?php
while ($vol = $query2->fetch()) {
    $nomDepart = $vol['VilleDep'];
    $nomArrivee = $vol['VillArr'];
    $callsign = $vol['callsign'];


    
    echo '<option value="' . $callsign . '">' . $callsign . " | " . $nomDepart . " --> " .$nomArrivee . '</option>';
}
?>

        
    </select>
    <button type="submit">OK</button>
</form>

</body>
</html>

