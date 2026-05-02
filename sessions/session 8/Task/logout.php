<?php
// ============================================================
// logout.php — LOGOUT HANDLER
//
// Steps:
//   1. Start the session (so we have access to it)
//   2. Destroy the session (clear all stored data)
//   3. Redirect the user back to Home page
// ============================================================

session_start();       // Step 1: Open the session

session_destroy();     // Step 2: Destroy ALL session data
                       //         ($_SESSION['email'], ['username'], etc. all gone)

// Step 3: Send user back to home page
header('Location: index.php');
exit();                // Always call exit() after header redirect!
