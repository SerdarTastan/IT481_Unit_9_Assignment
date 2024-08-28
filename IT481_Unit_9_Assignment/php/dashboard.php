<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <?php
    session_start();

     // Security headers
     header("Content-Security-Policy: default-src 'self';");
     header("X-Content-Type-Options: nosniff");
     header("X-Frame-Options: DENY");

    // Check for session hijacking by verifying the session variables
    if (!isset($_SESSION['username'])) {
        header("Location: index.html");
        exit();
    }

    $server = $_SESSION['server'];
    $database = $_SESSION['database'];
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];

    // Database connection using prepared statements to prevent SQL injection
    $connectionInfo = array("Database" => $database, "UID" => $username, "PWD" => $password);
    $conn = sqlsrv_connect($server, $connectionInfo);

    if ($conn === false) {
        error_log(print_r(sqlsrv_errors(), true)); // Log the error
        die("An error occurred. Please try again later.");
    }

    echo "<div class='dashboard-container'>";
    echo "<div class='userContainer'>";
    echo "<h3>User Name : " . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . "</h3><br>";
    echo "Server : " . htmlspecialchars($server, ENT_QUOTES, 'UTF-8') . "<br><br>";
    echo "Database : " . htmlspecialchars($database, ENT_QUOTES, 'UTF-8') . "<br><br>";
    echo "</div>";

    // Fetch and display data based on user role securely
    $tables = ['Orders', 'Customers', 'Employees'];
    foreach ($tables as $table) {
        echo "<h2>" . htmlspecialchars($table, ENT_QUOTES, 'UTF-8') . " - Table</h2>";

        $query = "SELECT * FROM $table";
        $stmt = sqlsrv_query($conn, $query);

        if ($stmt === false) {
            echo "<p>You do not have permission to view the $table table.</p>";
        } else {
            $rows = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $rows[] = $row;
            }
            echo "<p>Record count: " . count($rows) . "</p>";
            echo "<table class='greyGridTable'><tr>";

            // Display table headers
            foreach (array_keys($rows[0]) as $header) {
                echo "<th>" . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . "</th>";
            }
            echo "</tr>";

            // Display table data
            foreach ($rows as $row) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    if ($value instanceof DateTime) {
                        echo "<td>" . $value->format('Y-m-d H:i:s') . "</td>";
                    } else {
                        echo "<td>" . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . "</td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    sqlsrv_close($conn);
    echo "</div>";
    ?>
</body>
</html>
