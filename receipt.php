<?php
session_start();
require_once 'includes/db_connect.php';

/* =========================
    Auth check
========================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* =========================
Prevent duplicates
========================= */
if (isset($_SESSION['processing_payment']) && $_SESSION['processing_payment'] === true) {
    die("Payment is already being processed.");
}
$_SESSION['processing_payment'] = true;

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    unset($_SESSION['processing_payment']);
    die("Cart is empty.");
}

/* =========================
   Fetch user address
========================= */
$userQuery = $conn->prepare("SELECT full_name, email, address FROM users WHERE user_id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$user = $userQuery->get_result()->fetch_assoc();

$delivery_address = $user['address'] ?? '';

/* =========================
   Calculate totals
========================= */
$delivery_charge = 10.00;
$total = 0;

foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
$grand_total = $total + $delivery_charge;

/* =========================
   Payment selection
========================= */
$payment_method = $_POST['payment_method'] ?? 'Card';

/* =========================
   Simulate payment
========================= */
$payment_success = true; // replace with gateway response

if (!$payment_success) {
    unset($_SESSION['processing_payment']);
    die("Payment failed. Please try again.");
}

/* =========================
   Create order
========================= */
$conn->begin_transaction();

try {
    $orderStmt = $conn->prepare(
        "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'Paid')"
    );
    $orderStmt->bind_param("id", $user_id, $grand_total);
    $orderStmt->execute();
    $order_id = $orderStmt->insert_id;

    $itemStmt = $conn->prepare(
        "INSERT INTO order_items (order_id, product_id, quantity, price)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($cart as $item) {
        $itemStmt->bind_param(
            "iiid",
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        );
        $itemStmt->execute();
    }

    $txnStmt = $conn->prepare(
        "INSERT INTO transactions (order_id, payment_method) VALUES (?, ?)"
    );
    $txnStmt->bind_param("is", $order_id, $payment_method);
    $txnStmt->execute();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    unset($_SESSION['processing_payment']);
    die("Order processing error.");
}

/* =========================
   Generate PDF
========================= */
require_once __DIR__ . '/fpdf/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Sarawak Scents - Payment Receipt', 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, "Order ID: $order_id", 0, 1);
$pdf->Cell(0, 8, "Delivery Address: $delivery_address", 0, 1);
$pdf->Ln(5);

foreach ($cart as $item) {
    $line = $item['name'] . " x " . $item['quantity'] . " = RM " . number_format($item['price'] * $item['quantity'], 2);
    $pdf->Cell(0, 8, $line, 0, 1);
}

$pdf->Ln(5);
$pdf->Cell(0, 8, "Delivery: RM " . number_format($delivery_charge, 2), 0, 1);
$pdf->Cell(0, 8, "Total: RM " . number_format($grand_total, 2), 0, 1);

$pdf_path = "receipts/receipt_$order_id.pdf";
$pdf->Output('F', $pdf_path);

/* =========================
Email receipt
========================= */
$to = $user['email'];
$subject = "Your Sarawak Scents Order Receipt";
$message = "Thank you for your order.\nOrder ID: $order_id\nTotal: RM " . number_format($grand_total, 2);
$headers = "From: no-reply@sarawakscents.com";

mail($to, $subject, $message, $headers);

/* =========================
   Cleanup
========================= */
unset($_SESSION['cart']);
unset($_SESSION['processing_payment']);
?>

<!-- =========================
 Display receipt
========================= -->
<!DOCTYPE html>
<html>

<head>
    <title>Payment Receipt</title>
</head>

<body>
    <h2>Order Confirmed</h2>
    <p>Your order has been successfully placed.</p>

    <p><strong>Order ID:</strong> <?= $order_id ?></p>
    <p><strong>Total Paid:</strong> RM <?= number_format($grand_total, 2) ?></p>

    <a href="<?= $pdf_path ?>" target="_blank">Download Receipt (PDF)</a>
</body>

</html>