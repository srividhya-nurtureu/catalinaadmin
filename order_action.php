<?php
// Include necessary files and establish a database connection
require_once('DBConnection.php');

if (isset($_GET['action']) && isset($_GET['order_id'])) {
    $action = $_GET['action'];
    $orderId = $_GET['order_id'];

    // Update the order status based on the action in the 'orders' table
    $updateSql = "";
    $successMessage = "";

    if ($action == 'Confirm') {
        $updateSql = "UPDATE orders SET status = 'Confirmed' WHERE order_id = ?";
        $successMessage = "Order confirmed successfully!";
    } elseif ($action == 'Cancel') {
        $updateSql = "UPDATE orders SET status = 'Cancelled' WHERE order_id = ?";
        $successMessage = "Order cancelled successfully!";
    } else {
        echo "Invalid action.";
        exit();
    }

    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $orderId);

    if ($updateStmt->execute()) {
        echo $successMessage;
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $domain = $_SERVER['HTTP_HOST'];
        $redirectUrl = "$protocol://$domain/admin/?page=orders";

        header("refresh:3;url=$redirectUrl");
    } else {
        echo "Error performing action: " . $updateStmt->error;
    }

    // Close the database connection
    $updateStmt->close();
} else {
    echo "Invalid request.";
}
?>
