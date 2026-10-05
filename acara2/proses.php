<?php 
$keyword = $_GET['keyword'] ?? ''; 
echo "Anda mencari: " . htmlspecialchars($keyword); 
?> 