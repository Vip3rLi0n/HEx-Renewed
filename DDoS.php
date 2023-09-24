<?php

/**
 * SUMMARY:
 * 
 * This PHP script is designed to initiate a DDoS attack against a specified IP address 
 * under certain conditions. The script goes through several blocks of execution:
 * 
 * 1. Session Initialization: Ensures that the user is logged in.
 * 2. Request Validation: Validates the request type and the presence of an IP address.
 * 3. IP Address Validation: Validates the format of the provided IP address.
 * 4. Process Initiation: Instantiates necessary classes and checks conditions to launch 
 *    the attack. If conditions are met, a DDoS attack is initiated, and a notice is added to the session.
 * 5. Handle Errors: Any error encountered during the previous steps is added to the session as an error message.
 * 6. Redirection: Redirects the user to the specified location.
 * 
 * CHANGES AND REASONS:
 * 
 * 1. Improved Structuring:
 *    - The code is structured into clear blocks, each with a specific purpose, to improve readability 
 *      and maintainability.
 * 
 * 2. Detailed Commenting:
 *    - Each block and significant line of code is accompanied by comments explaining its purpose 
 *      and functionality. This is essential for understanding the flow and logic of the script.
 * 
 * 3. Dependency Explanation:
 *    - At the beginning of the script, dependencies are listed and briefly explained to provide 
 *      context on what classes and functionalities are being used.
 * 
 * 4. Explicit Error Handling:
 *    - Errors are explicitly handled and added to the session. This approach provides clearer feedback 
 *      and aids in debugging.
 * 
 * 5. IP Validation:
 *    - Introduced a separate block for IP address validation to ensure the input is in the correct format 
 *      before proceeding with the process.
 * 
 * These changes aim to make the script more robust, understandable, and maintainable, providing a solid 
 * foundation for future modifications and enhancements.
 */

/**
 * Dependencies:
 * - Session.class.php: Manages user sessions.
 * - System.class.php: Used for IP address validation.
 * - Player.class.php: Retrieves player information by IP.
 * - PC.class.php: Contains the Virus class.
 * - List.class.php: Checks if the IP is listed in the Hacked Database.
 */

// ----------------------------------------
// BLOCK 1: SESSION INITIALIZATION
// ----------------------------------------
require 'classes/Session.class.php'; 
$session = new Session(); // Initialize session.

// Check if the user is logged in.
if (!$session->issetLogin()) {
    header("Location:index");
    exit();
}

// ----------------------------------------
// BLOCK 2: REQUEST VALIDATION
// ----------------------------------------
$error = ''; // Initialize error variable.

// Validate the request method.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $error = 'Invalid request type.';
}

// Validate IP address presence.
if (empty($error) && empty($_POST['ip'])) {
    $error = 'Invalid IP address.';
}

// ----------------------------------------
// BLOCK 3: IP ADDRESS VALIDATION
// ----------------------------------------
if (empty($error)) {
    require 'classes/System.class.php'; 
    $system = new System(); // Instantiate System class for IP validation.

    // Validate the provided IP.
    if (!$system->validate($_POST['ip'], 'ip')) {
        $error = 'Invalid IP address.';
    }
}

// ----------------------------------------
// BLOCK 4: PROCESS INITIATION
// ----------------------------------------
if (empty($error)) {
    // Include the necessary classes for Player, Virus, and List functionalities.
    require 'classes/Player.class.php';
    require 'classes/PC.class.php'; // Assumed to contain the Virus class.
    require 'classes/List.class.php';

    // Instantiate the classes.
    $virus = new Virus(); // Object for handling virus-related functionalities.
    $player = new Player(); // Object for handling player-related functionalities.
    $list = new Lists(); // Object for handling list-related functionalities.

    // Convert the IP address from a string to a long integer.
    $ip = ip2long($_POST['ip']);

    // Retrieve information about the player associated with the given IP address.
    $playerInfo = $player->getIDByIP($ip, '');

    // Check the conditions to initiate the process:
    // 1. The player exists.
    // 2. The IP is listed in the user’s Hacked Database.
    // 3. The user has at least 3 working DDoS viruses.
    if ($playerInfo['0']['existe'] === 1 
        && $list->isListed($_SESSION['id'], $ip) 
        && $virus->DDoS_count() >= 3) {

        // Instantiate the Process class for handling process-related functionalities.
        $process = new Process();

        // Determine whether the player is an NPC.
        // If 'pctype' is 'VPC', it is not an NPC (isNPC = 0), otherwise, it is an NPC (isNPC = 1).
        $isNPC = $playerInfo['0']['pctype'] === 'VPC' ? 0 : 1;

        // Try to initiate a new process.
        // If successful, add a notice message to the session.
        if ($process->newProcess(
                $_SESSION['id'], 
                'DDOS', 
                $playerInfo['0']['id'], 
                'remote', 
                '', 
                '', 
                '', 
                $isNPC)) {

            // Add a notice message to the session indicating that the DDoS attack was launched.
            $session->addMsg(
                sprintf(_('DDoS attack against <strong>%s</strong> launched.'), $_POST['ip']), 
                'notice'
            );
        }
    } else {
        // If any condition is not met, set the appropriate error message.
        $error = 'You need to have at least 3 working DDoS viruses.';
    }
}

// ----------------------------------------
// BLOCK 5: HANDLE ERRORS
// ----------------------------------------
if (!empty($error)) {
    $session->addMsg($error, 'error');
}

// ----------------------------------------
// BLOCK 6: REDIRECTION
// ----------------------------------------
header("Location:list?action=ddos");
exit();

?>
