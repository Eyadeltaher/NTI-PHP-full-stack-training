<form method="GET" action="result.php">
    Product Name: <input type="text" name="product"><br>
    Price: <input type="number" name="price"><br>
    <button type="submit">Submit</button>
</form>


<?php
$product = $_GET['product'];
$price = $_GET['price'];

$discount = 0;

if ($price > 1000) {
    $discount = 0.15;
} else {
    $discount = 0.10;
}

$finalPrice = $price - ($price * $discount);

echo "Product: $product <br>";
echo "Final Price: $finalPrice";
?>
