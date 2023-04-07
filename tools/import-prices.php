<?php

$host = "localhost";
$username = "root";
$password = "secret";
$dbName = "pricesapi";
$port = 3306;

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbName", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $filename = "./import.csv";
    $csv = file_get_contents($filename);
    $rows = explode("\n", $csv);

    for ($i = 1; $i <= count($rows); $i++) {
        if (! isset($rows[$i]) || $rows[$i] === '') {
            continue;
        }

        $row = $rows[$i];

        $row = explode(',', $row);
        
        $sku = $row[0];
        $accountRef = $row[1];
        $userRef = $row[2];
        $quantity = $row[3];
        $value = $row[4];

        $sqlQuery = "SELECT p.id AS product_id, a.id AS account_id, u.id AS user_id, p.sku, a.external_reference, u.external_reference 
                    FROM products AS p 
                    LEFT JOIN accounts AS a ON a.external_reference = :accountRef 
                    LEFT JOIN users AS u ON u.external_reference = :userRef 
                    WHERE p.sku = :sku;";
        $statement = $conn->prepare($sqlQuery);
        
        $statement->bindValue(':accountRef', $accountRef);
        $statement->bindValue(':userRef', $userRef);
        $statement->bindValue(':sku', $sku);
        $statement->execute();

        $queryResult = $statement->fetch();
        
        $productId = $queryResult['product_id'];
        $accountId = $queryResult['account_id'];
        $userId = $queryResult['user_id'];

        $sqlInsert = "INSERT into prices (product_id,account_id,user_id,quantity,value,created_at,updated_at) 
                        values (:product_id,:account_id,:user_id,:quantity,:value,now(),now())";
        $statement = $conn->prepare($sqlInsert);
        $statement->bindValue(':product_id', $productId);
        $statement->bindValue(':account_id', $accountId);
        $statement->bindValue(':user_id', $userId);
        $statement->bindValue(':quantity', $quantity);
        $statement->bindValue(':value', $value);
        $statement->execute();
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
