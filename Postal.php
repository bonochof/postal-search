<?php

class Postal
{
  public $pdo;
  public $tblname;

  public function __construct($servername, $username, $password, $dbname, $tblname) {
    $dsn = "mysql:dbname=" . $dbname . ";host=" . $servername;
    $this->pdo = null;
    $this->tblname = $tblname;

    // create/check connection
    try {
      $this->pdo = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
      print('Connection failed: ' . $e->getMessage());
      die();
    }
  }

  public function __destruct() {
    $this->pdo = null;
  }

  public function search_by_zip($zip) {
    $keyword = "%" . $zip . "%";
    $sql  = "SELECT concat(addr1, addr2, addr3) as addr, zip FROM ". $this->tblname." WHERE zip LIKE :keyword order by zip";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':keyword', $keyword);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function search_by_kana($kana) {
    $keyword = "%" . $kana . "%";
    $sql  = "SELECT concat(addr1, addr2, addr3) as addr, zip FROM ". $this->tblname." WHERE concat(kana1,kana2,kana3) LIKE :keyword order by zip";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':keyword', $keyword);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function search_by_addr($addr) {
    $keyword = "%" . $addr . "%";
    $sql  = "SELECT concat(addr1, addr2, addr3) as addr, zip FROM ". $this->tblname." WHERE concat(addr1,addr2,addr3) LIKE :keyword order by zip";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':keyword', $keyword);
    $stmt->execute();
    return $stmt->fetchAll();
  }
  
  public function get_list_num() {
    $sql = "SELECT count(*)";
  }
}

?>
