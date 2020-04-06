<?php
  class Database {



    private $db_host;
    private $db_name;
    private $db_user;
    private $db_pass;
    private $db_char;



    public function connect () {

      $config = parse_ini_file('/srv/web/services/shortURLs/config/config.ini');

      $this->db_host = $config['db_host'];
      $this->db_name = $config['db_name'];
      $this->db_user = $config['db_user'];
      $this->db_pass = $config['db_pass'];
      $this->db_char = $config['db_char'];

      try {
        $database = new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name.';charset='.$this->db_char, $this->db_user, $this->db_pass);
        $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $database;
      }
      catch (PDOException $e) { echo 'Connnection failed: '.$e->getMessage(); }

    }



    public function getData ($queryTemplate, $queryData) {

      $database = $this->connect();

      $query = $database->prepare($queryTemplate);
      $query->execute($queryData);
      $queryResult = $query->fetch();

      return $queryResult;

    }



    public function placeData ($queryTemplate, $queryData) {

      $database = $this->connect();

      $query = $database->prepare($queryTemplate);
      $queryResult = $query->execute($queryData);

      return $queryResult;

    }

  }
?>