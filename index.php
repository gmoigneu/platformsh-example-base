<?php
class Example {
  public $host = '127.0.0.1';
	public $user = 'root';
	public $password = 'root';
	public $db = 'ldc';

  public function __construct() {
		if (empty($_ENV['PLATFORM_RELATIONSHIPS'])) {
      // We're not on platform.sh
			return;
		}
		// This is where we get the relationships of our application dynamically
    // from Platform.sh
		$relationships = json_decode(base64_decode($_ENV['PLATFORM_RELATIONSHIPS']), TRUE);
		// We are using the first relationship called "database" found in your
		// relationships. Note that you can call this relationship as you wish
		// in you .platform.app.yaml file, but 'database' is a good name.
		$this->db = $relationships['database'][0]['path'];
		$this->user = $relationships['database'][0]['username'];
		$this->password = $relationships['database'][0]['password'];
		$this->host = $relationships['database'][0]['host'];
	}

  public function connect() {
    $db = new PDO('mysql:host='.$this->host.';dbname='.$this->db.';charset=utf8mb4', $this->username, $this->password, array(PDO::ATTR_EMULATE_PREPARES => false));
    try {
      $db->query();
      $stmt = $db->prepare("SHOW VARIABLES WHERE Variable_Name = 'version';");
      $stmt->execute();
      $row = $stmt->fetch();
      return "<p>MySQL version is : " . $row['Value'] . "</p>";
    } catch(PDOException $ex) {
        die('MySQL connection is not working:' . $ex->getMessage());
    }
  }
}

$example = new Example();
echo $example->connect();

phpinfo();
