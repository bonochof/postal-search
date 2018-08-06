<?php
ini_set('display_errors', 1);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
</head>
<body>
<div class="container">
<div class="col-xs-2">

<?php
require 'Postal.php';

define("ARTICLE_MAX_NUM","20");
define("PAGEING_MAX_NUM","10");
define("FIRST_VISIT_PAGE","1");
define("INPUT_NUM","0");
define("INPUT_KANA","1");
define("INPUT_ADDR","2");

//入力
$input_data = '';
if(isset($_GET['input_data'])){
  $input_data = $_GET["input_data"];
}
if(!isset($_GET['page'])){
  $now_page = FIRST_VISIT_PAGE;
}else if(preg_match("/^[1-9][0-9]*$/",$_GET['page'])){
  $now_page = htmlspecialchars($_GET['page'],ENT_QUOTES,'UTF-8');
}else{
  echo "pageが正しく設定されていなかったので".FIRST_VISIT_PAGE."に設定しました!<br>";
  $now_page = FIRST_VISIT_PAGE;
}
echo '['.$input_data.']の検索結果一覧<br>';

$search_type = INPUT_ADDR;
if(preg_match("/^[1-9][0-9]*$/",$input_data)){
  $search_type = INPUT_NUM;
}else if(preg_match("/^[ァ-ヶー]+$/u",$input_data)){
  $search_type = INPUT_KANA;
}

$data_num = 0;
if($input_data != ''){
  $postal = new Postal("localhost", "takata", "passwd", "CSexp1DB", "zipZenkoku");
  $row = NULL;
  if($search_type == INPUT_NUM){
    $row = $postal->search_by_zip($input_data);
  }else if($search_type == INPUT_KANA){
    $row = $postal->search_by_kana($input_data);
  }else{
    $row = $postal->search_by_addr($input_data);
  }
  $data_num = count($row);
  if(count($row) > 0){
    echo '<table class="table">';
    echo '<thead>';
    echo '<th scope="col">郵便番号</th>';
    echo '<th scope="col">住所</th>';
    echo '<th scope="col">地図</th>';
    echo '</thead>';
    echo '<tbody>';
    $end = $now_page * ARTICLE_MAX_NUM;
    for($i = ($now_page - 1) * ARTICLE_MAX_NUM; $i < $end && $i<$data_num ; $i++){
      echo '<tr>';
      echo '<td>'.$row[$i]['zip'].'</td>';
      echo '<td>'.$row[$i]['addr'].'</td>';
      echo '<td><a href="./map.html?zip='.$row[$i]['zip'].'" class="btn btn-info" role="button">地図を表示</a></td>';
      echo '</tr>';
    }   
    echo '</tbody></table>';
  }else{
    echo '検索結果がありません。<br>';
  }
}else{
  echo '検索文字列が入力されていません。<br>';
}

//グーグル検索っぽいページング
$max_page = floor($data_num / ARTICLE_MAX_NUM);
if(($data_num % ARTICLE_MAX_NUM) != 0 ){
  $max_page += 1;
}
if($now_page > 1){
  echo '<a href=\'./search_address.php?page='.($now_page-1).'&input_data='.$input_data.'\' >←前へ</a>';
}

for($i = $now_page - 4, $cnt = 0; $cnt < PAGEING_MAX_NUM && $i<=$max_page ; $i++){
  if( $i > 0){
    if( $i == $now_page){
      echo $i.'  ';
    }else{
      echo '<a href=\'./search_address.php?page='.$i.'&input_data='.$input_data.'\' >'.$i.'  </a>';
    }
    $cnt+=1;
  }
}
if($now_page < $max_page){
  echo '<a href=\'./search_address.php?page='.($now_page+1).'&input_data='.$input_data.'\' >次へ→</a>';
}

echo '(全'.$data_num.'件)'

?>
<br>
<a href="search_address.html" class="btn btn-info" role="button">Back</a>
</div>
</div>
</body>
</html>
