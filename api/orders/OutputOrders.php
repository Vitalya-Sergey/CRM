<?php
require_once 'api/helpers/convertDate.php';

function OutputOrders($orders){
    foreach($orders as $order){
        $id = $order['id'];
        $client_name = $order['name'];
        $order_date = $order['order_date'];
        $total = $order['total'];
        $product_names = $order['product_names'];
        $status = '';
        $admin_name = $order['admin_name'];

        if($order['status']==='0'){
            $status='Не активен';
        }else{
            $status='Активен';
        }


        $product_names = str_replace(',', '<br/>', $product_names);

        $order_date=convertDateTime($order_date);

        echo "<tr>
        <td>$id</td>
        <td>$client_name</td>
        <td>$order_date</td>
        <td>$total</td>
        <td>$product_names</td>
        <td>$admin_name</td>
        <td>$status</td>
        <td onclick='MicroModal.show(\"edit-modal\")'><i class='fa fa-pencil' aria-hidden='true'></i></td>
        <td><a href='api/orders/DeleteOrder.php?id=$id'><i class='fa fa-trash' aria-hidden='true'></i></a></td>
        <td><a href='api/orders/generateCheack.php?id=$id'><i class='fa fa-qrcode' aria-hidden='true'></i></a></td>
        <td onclick='MicroModal.show(\"history-modal\")'><i class='fa fa-info-circle' aria-hidden='true'></i></td>
        </tr>";
}}

?>