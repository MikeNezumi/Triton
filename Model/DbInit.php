    <?php
    /* This procedural script creates underlying database structure */

    echo "<head><title>Triton, creating databases...</title></head>";

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $servername = "localhost";
    $username = "root";
    $password = "database";

    createDb($servername, $username, $password, "TECHNICALITIES");
    $columns = "
    id int(11) AUTO_INCREMENT,
    username varchar(255) UNIQUE NOT NULL,
    gender varchar(255) NOT NULL,
    email varchar(255) UNIQUE NOT NULL,
    password varchar(255) NOT NULL,
    phone varchar(255) NOT NULL,
    role varchar(255) NOT NULL,
    permissions varchar(255) NOT NULL,
    PRIMARY KEY (id)
    ";
    createTable($servername, $username, $password, "TECHNICALITIES", "human", $columns);

    createDb($servername, $username, $password, "DOCUMENTS");
    $columns = "
    id int(11) AUTO_INCREMENT,
    headline varchar(255),
    author varchar(255),
    visibility varchar(255),
    published datetime(6) DEFAULT CURRENT_TIMESTAMP,
    docpath varchar(255) UNIQUE,
    PRIMARY KEY (id)
    ";
    createTable($servername, $username, $password, "DOCUMENTS", "article", $columns);

    $columns = "
    id int(11) AUTO_INCREMENT,
    topic varchar(255),
    course varchar(255),
    author varchar(255),
    license varchar(255),
    version int(11),
    published datetime(6) DEFAULT CURRENT_TIMESTAMP,
    docpath varchar(255) UNIQUE,
    PRIMARY KEY (id)
    ";
    createTable($servername, $username, $password, "DOCUMENTS", "paper", $columns);
    // Table 'recent_paper' has columns identical to 'paper'
    createTable($servername, $username, $password, "DOCUMENTS", "recent_paper", $columns);

    function createDb($servername, $username, $password, $db_name){
      $conn = new mysqli($servername, $username, $password);  # Create connection
      mysqli_set_charset($conn, "utf8");
      // Check connection
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
      // Create database
      $sql = "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4;";
      if ($conn->query($sql) === TRUE) {
          echo "<br>Database ".$db_name." was created.<br>";
      } else {
          echo "Error creating database: " . $conn->error;
          error_log("In DbInit.createDb(): " . $conn->error . "\n");
      }
    $conn->close();
    }

    function createTable($servername, $username, $password, $db_name, $table_name, $columns){
      $conn = new mysqli($servername, $username, $password, $db_name);  # Create connection
      mysqli_set_charset($conn, "utf8");
      // Check connection
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
      $sql = "CREATE TABLE IF NOT EXISTS $table_name ($columns);";
      if ($conn->query($sql) === TRUE) {
          echo "Table ".$table_name." was added to database ".$db_name.".<br>";
      } else {
          echo "Error adding table: " . $conn->error;
          error_log("In DbInit.createTable(): " . $conn->error . "\n");
      }
    $conn->close();
    }
    ?>
