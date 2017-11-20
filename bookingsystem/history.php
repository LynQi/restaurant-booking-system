<?php
 
$conn = new mysqli('localhost', 'root', '', 'restaurant_booking');
 
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
else{
    session_start();
    
    $name = $_SESSION["name"];
    $number = $_SESSION["number"];
    $date = $_SESSION["date"];
    $time = $_SESSION["time"];
    $cphone = $_SESSION["cphone"];
    $restid = $_GET["id"];

    $sql = "SELECT cname FROM customer WHERE cname = '$name'";
    $result = $conn->query($sql);
    if ($result->num_rows == 0){
        $sql = "INSERT INTO customer (cid, cname, phone)
                SELECT MAX(cid) + 1, '$name', '$cphone'
                FROM customer";
        $conn->query($sql);
    }

    $sql = "INSERT INTO booking (bid, cid, rid, btime, quantity)
            SELECT MAX(bid) + 1, customer.cid, $restid, TIMESTAMP('$date', MAKETIME($time,00,00)), $number
            FROM customer, booking
            WHERE cname = '$name'";
    $conn->query($sql);
    $sql = "SELECT rname, btime, raddress, quantity
            FROM customer NATURAL JOIN booking NATURAL JOIN restaurant
            WHERE cname = '$name'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
            echo "  <table align = 'center' border = '1'>
                    <tr>
                    <th>Restaurant</th>
                    <th>Time</th>
                    <th>Address</th>
                    <th>Quantity</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>". $row["rname"]."</td>";
                echo "<td>". $row['btime']."</td>";
                echo "<td>". $row['raddress']."</td>";
                echo "<td>". $row['quantity']."</td>";;
                echo "</tr>";
            }
            echo "</table>";
    }
    else{
        echo "<div style='text-align:center;'>"."No History Booking"."</div>";
    }
}
mysqli_close($conn);

echo "<div style='text-align:center;'>"."<a href = 'index.html'>".'Finish'."</a>"."</div>";
?>