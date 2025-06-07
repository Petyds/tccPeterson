<?php
   $servername = "162.241.62.242";
   $username = "fabia084_bianomx";
   $password = "Bi4noGiIsa23*";
   $dbname = "fabia084_hospital_db";
   
   $charset = 'utf8mb4';
   
   $dsn = "mysql:host=$servername;dbname=$dbname;charset=$charset";
   
   $options = [
       PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Erros como exceções
       PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Resultado como array associativo
       PDO::ATTR_EMULATE_PREPARES   => false,                  // Desativa emulação de prepares (mais seguro)
   ];
   
   try {
       $pdo = new PDO($dsn, $username, $password, $options);
       //echo "Conexão realizada com sucesso!";
   } catch (\PDOException $e) {
       echo "Erro na conexão: " . $e->getMessage();
   }