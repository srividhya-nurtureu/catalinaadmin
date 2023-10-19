<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('DBConnection.php');

$sessionType = isset($_SESSION['type']) ? $_SESSION['type'] : null;

$dfrom = isset($_GET['date_from']) ? $_GET['date_from'] : date("Y-m-d", strtotime(date("Y-m-d") . " -1 week"));
$dto = isset($_GET['date_to']) ? $_GET['date_to'] : date("Y-m-d");

// Modify the SQL query to fetch order receipt data within the specified date range
$sql = "SELECT * FROM orders WHERE order_date >= ? AND order_date <= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $dfrom, $dto); // Bind the parameters
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="card rounded-0 shadow">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Order Receipts</h3>
    </div>
    <!--- <div class="card-body">
        <h5>Filter</h5>
        <div class="row align-items-end">
            <div class="form-group col-md-2">
                <label for="date_from" class="control-label">Date From</label>
                <input type="date" name="date_from" id="date_from" value="<?php echo $dfrom ?>" class="form-control rounded-0">
            </div>
            <div class="form-group col-md-2">
                <label for="date_to" class="control-label">Date To</label>
                <input type="date" name="date_to" id="date_to" value="<?php echo $dto ?>" class="form-control rounded-0">
            </div>
            <div class="form-group col-md-4 d-flex">
                <div class="col-auto">
                    <button class="btn btn-primary rounded-0" id="filter" type="button"><i class="fa fa-filter"></i> Filter</button>
                    <button class="btn btn-success rounded-0" id="print" type="button"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
        </div> -->
        <hr> 
        <div class="clearfix mb-2"></div>
        <div id="outprint">
            <table class="table table-hover table-striped table-bordered">
                <colgroup>
                    <col width="5%">
                <col width="15%">
                <col width="25%">
                <col width="10%">
                <col width="15%">
                <col width="15%">
                <col width="15%">
                <col width="15%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center p-0">Order ID</th>
                        <th class="text-center p-0">Receipt No</th>
                        <th class="text-center p-0">User Name</th>
                        <th class="text-center p-0">Phone Number</th>
                        <th class="text-center p-0">Email</th>
                        <th class="text-center p-0">Total Amount</th>
                        <th class="text-center p-0">Processed By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through the $orders array and display order receipt data in the table
                    foreach ($orders as $order) {
                        echo "<tr>";
                        echo "<td>" . $order['order_id'] . "</td>";
                        echo "<td>" . $order['receipt_no'] . "</td>";
                        echo "<td>" . $order['user_name'] . "</td>";
                        echo "<td>" . $order['phone'] . "</td>";
                        echo "<td>" . $order['email'] . "</td>";
                        echo "<td>" . $order['total_price'] . "</td>";
                        echo "<td>Dexter</td>"; // Replace with the actual admin username
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
