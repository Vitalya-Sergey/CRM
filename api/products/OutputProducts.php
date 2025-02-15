<?php
require_once 'api/helpers/convertDate.php';

function OutputProducts($products){
    foreach($products as $product){
        $id = $product['id'];
        $products_name = $product['name'];
        $description = $product['description'];
        $price = $product['price'];
        $stock = $product['stock'];

        echo "<tr>
        <td>$id</td>
        <td>$products_name</td>
        <td>$description</td>
        <td>$price</td>
        <td>$stock</td>
        <td onclick='MicroModal.show(\"edit-modal\")'><i class='fa fa-pencil' aria-hidden='true'></i></td>
        <td><a href='api/products/DeleteProduct.php?id=$id'><i class='fa fa-trash' aria-hidden='true'></i></a></td>
        <td><a href='api/products/generateQR.php?id=$id'><i class='fa fa-qrcode' aria-hidden='true'></i></a></td>
        </tr>";
}}

?>