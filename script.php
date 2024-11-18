<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/** 
 * Simple code for executing commands on a remote Linux server via SSH in PHP
 */

$server   = "172.26.191.110"; // server IP/hostname of the SSH server
$username = "username"; // username for the user you are connecting as on the SSH server
$password = "password"; // password for the user you are connecting as on the SSH server

// Get commands from POST data or default to 'ip a'
$commands = isset($_POST['commands']) ? $_POST['commands'] : ['whoami']; // Default command

// Establish a connection to the SSH Server. Port is the second param.
$connection = ssh2_connect($server, 22);
if (!$connection) {
    die("Connection failed. Please check the server address and network connectivity.");
}

// Authenticate with the SSH server
if (!ssh2_auth_password($connection, $username, $password)) {
    die("Authentication failed. Please check your username and password.");
}

// Execute commands on the connected server and capture the response
$output = '';
foreach ($commands as $command) {
    // Execute the command
    $stream = ssh2_exec($connection, $command);
    if (!$stream) {
        die("Command execution failed for command: {$command}");
    }

    // Sets blocking mode on the stream
    stream_set_blocking($stream, true);

    // Get the response of the executed command in a human-readable form
    $output .= "Command: {$command}\n";
    $output .= "Output:\n" . stream_get_contents($stream) . "\n\n";
    fclose($stream); // Close the stream
}

// Display the output
echo "<pre>{$output}</pre>";
?>
