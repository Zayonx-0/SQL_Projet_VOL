<!DOCTYPE html>
<html>
<head>
  <title>Informations sur les vols</title>
</head>
<body>
  <h1>Informations sur les vols</h1>

  <?php
  // Connexion à la base de données
  $servername = "localhost";
  $username = "root";
  $password = "root";
  $dbname = "basevol";

  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
  }

  // Récupération des informations sur les vols
  $sql = "SELECT v.callSign, v.codeIATADep, v.codeIATAArr, r.latitude, r.longitude, r.altitude
    FROM vols v
    INNER JOIN (
      SELECT callSign, MAX(heure) AS max_heure
      FROM releves
      GROUP BY callSign
    ) r_max ON v.callSign = r_max.callSign
    INNER JOIN releves r ON r.callSign = r_max.callSign AND r.heure = r_max.max_heure";
  $result = $conn->query($sql);

    // Affichage des informations pour le dernier vol
    while ($row = $result->fetch_assoc()) {
  
      echo "<h2>Vol " . $row["callSign"] . "</h2>";
      echo "<p>Départ: " . $row["codeIATADep"] . "</p>";
      echo "<p>Arrivée: " . $row["codeIATAArr"] . "</p>";
      echo "<p>Dernier relevé:</p>";
      echo "<ul>";
      echo "<li>Latitude: " . $row["latitude"] . "</li>";
      echo "<li>Longitude: " . $row["longitude"] . "</li>";
      echo "<li>Altitude: " . $row["altitude"] . "</li>";
      echo "</ul>";
    }
  // Fermeture de la connexion à la base de données
  $conn->close();
  ?>

</body>
</html>
