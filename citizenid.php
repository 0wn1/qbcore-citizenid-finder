<?php

  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "qbcore";

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
  
  $citizenid = $_POST["citizenid"];
  
  $sql = "SELECT p.id, p.citizenid, p.license, p.name, p.money, p.job, p.gang, p.position, p.metadata, p.inventory, p.last_updated, 
          GROUP_CONCAT(DISTINCT CONCAT('{\"', pv.vehicle, '\":\"', pv.plate, '\"}') SEPARATOR ',') AS vehicles 
          FROM players p 
          LEFT JOIN player_vehicles pv ON p.citizenid = pv.citizenid 
          WHERE p.citizenid = :citizenid 
          GROUP BY p.id";
  
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":citizenid", $citizenid);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (count($result) > 0) {
    $row = $result[0];
    $output = array(
      "ID" => $row["id"],
      "Citizen ID" => $row["citizenid"],
      "License" => $row["license"],
      "Name" => $row["name"],
      "Money" => json_decode($row["money"]),
      "Job" => json_decode($row["job"]),
      "Gang" => json_decode($row["gang"]),
      "Position" => json_decode($row["position"]),
      "Metadata" => json_decode($row["metadata"]),
      "Inventory" => json_decode($row["inventory"]),
      "Last Updated" => $row["last_updated"],
      "Vehicles" => json_decode("[{$row['vehicles']}]")
    );

    $output = json_encode($output, JSON_PRETTY_PRINT);

    echo "<textarea readonly id='json-textarea' style='width: 100%; height: 100%; margin: 0; padding: 0; background-color: #36454F; color: white'>$output</textarea>";

  } else {
    echo "Unable to find player with Citizen ID $citizenid";
  }
  
  $conn = null;
?>