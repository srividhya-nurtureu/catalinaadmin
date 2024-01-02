<?php
// Include necessary files and establish a database connection
require_once('DBConnection.php');
error_reporting(E_ERROR | E_PARSE);

if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    // Fetch order details based on the order ID from the 'orders' table
    $orderSql = "SELECT * FROM orders WHERE order_id = ?";
    $orderStmt = $conn->prepare($orderSql);
    $orderStmt->bind_param("i", $orderId);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();
    $orderDetails = $orderResult->fetch_assoc();

    // Check if order data is found
    if ($orderDetails) {
        // Fetch transaction details based on the receipt number from the 'transaction_list' table
        $transactionSql = "SELECT * FROM transaction_list WHERE receipt_no = ?";
        $transactionStmt = $conn->prepare($transactionSql);
        $transactionStmt->bind_param("s", $orderDetails['receipt_no']);
        $transactionStmt->execute();
        $transactionResult = $transactionStmt->get_result();
        $transactionDetails = $transactionResult->fetch_assoc();

        // Fetch items based on the transaction ID from the 'transaction_items' table
        $itemsSql = "SELECT ti.*, pl.name as product_name
                     FROM transaction_items ti
                     JOIN product_list pl ON ti.id = pl.id
                     WHERE ti.transaction_id = ?";
        $itemsStmt = $conn->prepare($itemsSql);
        $itemsStmt->bind_param("i", $transactionDetails['transaction_id']);
        $itemsStmt->execute();
        $itemsResult = $itemsStmt->get_result();
        $items = $itemsResult->fetch_all(MYSQLI_ASSOC);

        // Display the order details, transaction details, and items in a structured format
        echo "<div class='order-details-container'>";
        echo "<p><strong>Order ID:</strong> " . $orderDetails['order_id'] . "</p>";
        echo "<p><strong>Receipt No:</strong> " . $orderDetails['receipt_no'] . "</p>";
        echo "<p><strong>Status:</strong> " . $orderDetails['status'] . "</p>";
        echo "<p><strong>User Name:</strong> " . $orderDetails['user_name'] . "</p>";
        echo "<p><strong>Phone:</strong> " . $orderDetails['phone'] . "</p>";
        echo "<p><strong>Email:</strong> " . $orderDetails['email'] . "</p>";
        echo "<p><strong>Address:</strong> " . $orderDetails['address'] . "</p>";
        echo "<p><strong>Total Price:</strong> " . $orderDetails['total_price'] . "</p>";
        echo "<p><strong>Order Date:</strong> " . $orderDetails['order_date'] . "</p>";

        if ($transactionDetails) {
            echo "<p><strong>Transaction ID:</strong> " . $transactionDetails['transaction_id'] . "</p>";
            echo "<p><strong>Total:</strong> " . $transactionDetails['total'] . "</p>";
            echo "<p><strong>Tendered Amount:</strong> " . $transactionDetails['tendered_amount'] . "</p>";
            echo "<p><strong>Change:</strong> " . $transactionDetails['change'] . "</p>";
            echo "<p><strong>User ID:</strong> " . $transactionDetails['user_id'] . "</p>";
            echo "<p><strong>Date Added:</strong> " . $transactionDetails['date_added'] . "</p>";

            if ($items) {
                echo "<p><strong>Items:</strong></p>";
                echo "<ul>";
                foreach ($items as $item) {
                    echo "<li><strong>Product Name:</strong> " . $item['product_name'] . ", <strong>Quantity:</strong> " . $item['quantity'] . ", <strong>Price:</strong> " . $item['price'] . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No items found for this order.</p>";
            }
        } else {
            echo "<p>No transaction details found for this order.</p>";
        }

        echo "</div>";
    } else {
        echo "<p>No order found with the specified ID.</p>";
    }
} else {
    echo "<p>Invalid order ID.</p>";
}

// Close the database connection
$orderStmt->close();
$transactionStmt->close();
$itemsStmt->close();
?>
