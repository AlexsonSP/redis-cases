<html>
    <head>
        <title>Using Redis Server with PHP and MySQL</title>
    </head> 
    <body>

    <h1 align = 'center'>Students' Register</h1>

    <table align = 'center' border = '2'>        

    <?php 

        require_once '/var/www/html/vendor/predis/predis/autoload.php';
        try {

            

            $data_source = '';

            // $redis = new Redis(); 
            // $redis->connect('127.0.0.1', 6379); 

            $redis = new Predis\Client(['host' => 'redis']);
            $redisStatus = redisConnect($redis);

            $data = []; 

            $sql = 'select
                    student_id,
                    first_name,
                    last_name                                 
                    from student
                    ';

            // $cache_key = md5($sql);
            $cache_key = $sql;

            echo "<br>SQL: " . $sql . "<br>";
            echo "<br>MD5: " . md5($sql) . "<br>";
            

            $startTime = microtime(true)*10000; 
            // echo "start: " . date("H:i:s") . "</br>";

            if ($redis->exists($cache_key)) {

                $data_source = "Data from Redis Server";
                $data = unserialize($redis->get($cache_key));

            } else {

                $data_source = 'Data from MySQL Database';

                // Create connection
                $conn = new mysqli("mysql", "root", "root", "blog_db");
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $data[] = $row;         
                        }
                        $redis->set($cache_key, serialize($result)); 
                        $redis->expire($cache_key, 5); 
                } else {
                    echo "0 results";
                }
                $conn->close();

                $redis->set($cache_key, serialize($data)); 
                $redis->expire($cache_key, 5); 

                
            }

            $endTime = microtime(true)*10000;
            // echo "finish: " . date("H:i:s") . "</br>";
            $time = ((int)$endTime - (int)$startTime);
            echo "Time in microseconds: " . $time;


            echo "</br>====================</br>";
            echo "</br>====================</br>";

            echo "<tr><td colspan = '3' align = 'center'><h2>$data_source</h2></td></tr>";
            echo "<tr><th>Student Id</th><th>First Name</th><th>Last Name</th></tr>";

            foreach ($data as $row) {
                echo '<tr>';
                echo '<td>' . $row['student_id'] . '</td>';
                echo '<td>' . $row['first_name'] . '</td>';
                echo '<td>' . $row['last_name']  . '</td>';                     
                echo '</tr>'; 
            }  
           
            


        } catch (PDOException $e) {
            echo 'Database error. ' . $e->getMessage();
        }


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

    </table>
  </body>
</html>