<?php
session_start();
require_once('classes/PDO.class.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Nothing Here. Stop peeking!';
    header('Refresh: 3; url=/index');
    exit;
}

// Connect to the database
$dbh = PDO_DB::factory();

// Retrieve the software data
$query = "SELECT id, softname, softversion, softType, softhidden FROM software WHERE userid = :userid AND isNPC = 0 AND softHidden = 0 AND softType <> 31 GROUP BY softType, softVersion DESC, softLastEdit ASC";
$stmt = $dbh->prepare($query);
$stmt->bindValue(':userid', $_SESSION['id']);
$stmt->execute();
$softwareData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create an array to hold the formatted software data
$formattedData = array();

// Map the softType numbers to their respective software types
$softTypes = array(
    1 => "Cracker",
    2 => "Hasher",
    3 => "Port Scanner",
    4 => "Firewall",
    5 => "Hidder",
    6 => "Seeker",
    7 => "AntiVirus",
    8 => "Virus Spam",
    9 => "Virus Warez",
    10 => "Virus DDoS",
    11 => "Virus Collector",
    12 => "DDoS Breaker",
    13 => "FTP Exploit",
    14 => "SSH Exploit",
    15 => "Nmap Scanner",
    16 => "Hardware Analyzer",
    17 => "Torrent",
    20 => "Virus Miner",
    29 => "DOOM Virus",
    30 => "Text File"
);

// Iterate over the software data and format it for the JSON response
foreach ($softwareData as $row) {
    if ($row['softhidden'] == 0) {
        $formattedSoftware = array(
            'text' => $softTypes[$row['softType']],
            'children' => array(
                array(
                    'id' => $row['id'],
                    'text' => $row['softname'] . " (" . number_format($row['softversion'], 1) . ")"
                )
            )
        );
        $formattedData[] = $formattedSoftware;
    }
}

// Convert the data to JSON format
$jsonData = json_encode($formattedData);

// Send the response as JSON
header('Content-Type: application/json');
?>
