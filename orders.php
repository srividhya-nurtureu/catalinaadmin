<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('DBConnection.php');

$sessionType = isset($_SESSION['type']) ? $_SESSION['type'] : null;

$dfrom = isset($_GET['date_from']) ? $_GET['date_from'] : date("Y-m-d", strtotime(date("Y-m-d") . " -1 week"));
$dto = isset($_GET['date_to']) ? $_GET['date_to'] : date("Y-m-d");

// Modify the SQL query to fetch order receipt data within the specified date range
// $sql = "SELECT * FROM orders WHERE order_date >= ? AND order_date <= ?";
$sql = "SELECT * FROM orders ORDER BY order_date desc";
$stmt = $conn->prepare($sql);
// $stmt->bind_param("ss", $dfrom, $dto); // Bind the parameters
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

ini_set('display_errors', 1);

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
                <!-- <colgroup>
                    <col width="5%">
                <col width="15%">
                <col width="25%">
                <col width="10%">
                <col width="15%">
                <col width="15%">
                <col width="15%">
                <col width="15%">
                </colgroup> -->
                <thead>
                    <tr>
                        <th class="text-center p-0">Order ID</th>
                        <th class="text-center p-0">Receipt No</th>
                        <th class="text-center p-0">User Name</th>
                        <th class="text-center p-0">Phone Number</th>
                        <th class="text-center p-0">Email</th>
                        <th class="text-center p-0">Total Amount</th>
                        <th class="text-center p-0">Status</th>
                        <th class="text-center p-0">Action</th>
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
                        echo "<td>" . $order['status'] . "</td>";

                        if ($order['status'] == "Pending") { ?>
                            <td>
                                <a href="javascript:;" class="view_order_details" data-id="<?php echo $order['order_id']; ?>">View order</a>
                                <a href="order_action.php?action=Confirm&order_id=<?php echo $order['order_id']; ?>" onclick="return confirmAction('Confirm')">Confirm</a>
                                <a href="order_action.php?action=Cancel&order_id=<?php echo $order['order_id']; ?>" onclick="return confirmAction('Cancel')">Cancel</a>

                            </td>
                        <?php }else{ ?>
                            <td>
                                <a href="javascript:;" class="view_order_details" data-id="<?php echo $order['order_id']; ?>">View order</a>
                            </td>
                        <?php }

                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function confirmAction(action) {
        var confirmation = confirm("Are you sure you want to " + action + " this order?");
        return confirmation;
    }

    // Add an event listener to handle the "View order" click event
    document.addEventListener('DOMContentLoaded', function () {
        var viewOrderLinks = document.querySelectorAll('.view_order_details');
        viewOrderLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                var orderId = link.getAttribute('data-id');
                fetchOrderDetails(orderId);
            });
        });

        function fetchOrderDetails(orderId) {
            // Use AJAX to fetch order details
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    // Display the order details in the modal
                    document.getElementById('orderDetailsContent').innerHTML = this.responseText;
                    // Show the modal
                    $('#orderModal').modal('show');
                }
            };
            xhttp.open("GET", "fetch_order_details.php?order_id=" + orderId, true);
            xhttp.send();
        }
    });
    $(document).ready(function () {
        $('#orderModal').modal('hide');
    });
</script>
<!-- Add this modal structure at the end of your HTML -->
<div class="modal" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Order details will be displayed here -->
                <div id="orderDetailsContent"></div>
            </div>
        </div>
    </div>
</div>