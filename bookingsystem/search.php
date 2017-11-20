<?php
 
$conn = new mysqli('localhost', 'root', '', 'restaurant_booking');
 
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
else{
    session_start();

    $name = $_POST["cn"];
    $food = $_POST["rn"];
    $number = $_POST["quan"];
    $date = $_POST["bdate"];
    $time = $_POST["bclock"];

    $_SESSION["name"]=$_POST['cn'];
    $_SESSION["number"]=$_POST['quan'];
    $_SESSION["date"]=$_POST['bdate'];
    $_SESSION["time"]=$_POST['bclock'];
    $_SESSION["cphone"]=$_POST['cphone'];
    
    $sql = "DROP VIEW IF EXISTS temp;";
    $conn->query($sql);
    $sql = "CREATE VIEW temp AS
                SELECT rid, rname, description, btime, capacity - SUM(quantity) AS curcapacity
                FROM booking NATURAL JOIN restaurant
                GROUP BY rid, btime;";
    $conn->query($sql);
    if ($food == NULL) {
        $sql = "SELECT rid, rname, description, raddress FROM restaurant
                WHERE $number <= capacity AND rid NOT IN ( SELECT rid FROM temp WHERE DATE(btime) = '$date' AND TIME(btime) = MAKETIME($time,00,00) AND curcapacity < $number)";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "  <table align = 'center' border = '1'>
                    <tr>
                    <th>Restaurant</th>
                    <th>Description</th>
                    <th>Address</th>
                    <th>Select</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>". $row["rname"]."</td>";
                echo "<td>". $row['description']."</td>";
                echo "<td>". $row['raddress']."</td>";
                echo "<td><a href = 'history.php?id=\"".$row['rid']."\"'>". 'book'."</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        else{
            echo "<div style='text-align:center;'>"."No Restaurant"."</div>";
            echo "<div style='text-align:center;'>"."<a href = 'index.html'>".'Return'."</a>"."</div>";
        }
    }

    else{
        $sql = "SELECT rid, rname, description, raddress FROM restaurant
                WHERE rname = '$food' OR description LIKE '%$food%' AND $number <= capacity AND rid NOT IN ( SELECT rid FROM temp WHERE  DATE(btime) = '$date' AND TIME(btime) = MAKETIME($time,00,00) AND curcapacity < $number)";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "  <table align = 'center' border = '1'>
                    <tr>
                    <th>Restaurant</th>
                    <th>Description</th>
                    <th>Address</th>
                    <th>Select</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>". $row["rname"]."</td>";
                echo "<td>". $row['description']."</td>";
                echo "<td>". $row['raddress']."</td>";
                echo "<td><a href = 'history.php?id=\"".$row['rid']."\"'>". 'book'."</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        else{
            echo "<div style='text-align:center;'>"."No Restaurant"."</div>";
            echo "<div style='text-align:center;'>"."<a href = 'index.html'>".'Return'."</a>"."</div>";
        }
    }
}
mysqli_close($conn);
?>