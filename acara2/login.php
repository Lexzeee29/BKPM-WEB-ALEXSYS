<?php 
$username = $_POST['username'] ?? ''; 
$password = $_POST['password'] ?? ''; 
// Jangan echo password di production! 
echo "Username yang dikirim: " . htmlspecialchars($username); 
?> 