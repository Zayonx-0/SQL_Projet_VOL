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

?>

<form action="resultatsrecherchevol.php" method="GET">
<select name="recherchevol">
<?php
while ($vol = $query->fetch()) {
    echo '<option value="' . $vol['callSign'] . '">' . $vol['codeIATADep'] . " --> " .$vol['codeIATAArr'] . '</option>';
}
?>

        
    </select>
    <button type="submit">OK</button>
</form>

</body>
</html>

