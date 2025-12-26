<?php 
$password = 'gokhanDogan123!'; 
$hashedPassword = password_hash($password, PASSWORD_BCRYPT); 
echo $hashedPassword; 
?>

<!-- 

SQL'de kullanıcıyı eklemek için örnek komut:
INSERT INTO users (full_name, email, password_hash, role, created_at)
VALUES ('Gökhan Doğan', 'gokhan.dogan@klu.edu.tr', 'HASHLI_SIFRE', 'admin', NOW());

HASHLI_SIFRE kısmını yukarıdaki PHP çıktısı ile değiştirin.

-->