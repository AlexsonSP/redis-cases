<?php  
require_once '/var/www/html/vendor/predis/predis/autoload.php';

if($_GET!= null){
    $repeat = $_GET['repeat'];
    $startRange = $_GET['startRange'];
    $endRange = $_GET['endRange'];
} else{
    $repeat = 100;
    $startRange = 1;
    $endRange = 1000;
}

$con = mysqli_connect("mysql","root","root","blog_db"); 
$startTime = microtime(true)*1000; 
// echo "</br>start MySql: " . date("H:i:s") . "</br>";
for($i = 1; $i <= $repeat; $i++) {   
        $rand = rand($startRange, $endRange);  
        $sql = "SELECT VALUE from data WHERE `key` = $rand";            
        if (!mysqli_query($con, $sql)) {  
            echo "Error: " . $sql . "" . mysqli_error($con);  
        }  
      
}

$endTime = microtime(true)*1000;
// echo "finish: " . date("H:i:s") . "</br>";
$time = ((int)$endTime - (int)$startTime);
echo "MySQL Time in ms: " . $time;


echo "</br>====================</br>";
echo "</br>====================</br>";

$redis = new Predis\Client(['host' => 'redis']);
$redisStatus = redisConnect($redis);

// $client = $redis;

$contMySql=0;
$contRedis=0;
$contTotal=0;

$startTime = microtime(true)*1000; 
// echo "start Redis: " . date("H:i:s") . "</br>";
for($i = 1; $i <= $repeat; $i++) { 
        $rand = rand($startRange, $endRange);  
        $contTotal++;
        if(!$redis->exists($rand)) {  
            $redis->set($rand, $rand);
            $sql = "SELECT VALUE from data WHERE `key` = $rand";     
            $contMySql++; 
            if (!mysqli_query($con, $sql)) {  
                echo "Error: " . $sql . "" . mysqli_error($con);  
            }  
        }   
    
}

$endTime = microtime(true)*1000;
// echo "finish: " . date("H:i:s") . "</br>";
$time = ((int)$endTime - (int)$startTime);
echo "REDIS Time in ms: " . $time;


$contRedis = $contTotal-$contMySql;
echo "</br>Total requisições: " . $contTotal . "</br>";
echo "</br>Total Redis: " . $contRedis . "</br>";
echo "</br>Total MySql: " . $contMySql . "</br>";
                        

exit(1);


function redisConnect($mem) {
  try {
      $mem->connect();
      $mem->select(0);
      $status = "OK";
  }

  catch (Exception $exception) {
      $status = "Redis failed to connect";
  }
  return $status;
}
?>