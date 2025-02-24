<?php
require_once 'api/helpers/convertDate.php';

function OutputClients($clients){
    foreach($clients as $client){
        $id = $client['id'];
        $clients_name = $client['name'];
        $email = $client['email'];
        $phone = $client['phone'];
        $birthday = $client['birthday'];
        $created_at = $client['created_at'];

        $birthday = convertDate($birthday);
        $created_at = convertDateTime($created_at);
        echo "<tr>
        <td>$id</td>
        <td>$clients_name</td>
        <td>$email</td>
        <td>$phone</td>
        <td>$birthday</td>
        <td>$created_at</td>
        <td>
        <form method='GET' action='api/clients/ClientHistory.php'>
        <input value='$id' name='id' hidden>
        <div style='display: flex; flex-direction: column;'>
                <label for='from'>От:</label>
                <input type='date' id='from' name='from'>
                <label for='to'>До:</label>
                <input type='date' id='to' name='to'>
            </div>
        <button
            style='
                display: block;
                padding: 8px 16px;
                border: none;
                border-radius: 5px;
                background-color: #007bff;
                color: white;
                cursor: pointer;
            '
            type='submit'
            >Сформировать</button>
            </form>
        </td>
        <td onclick='MicroModal.show(\"edit-modal\")'><i class='fa fa-pencil' aria-hidden='true'></i></td>
        <td><a href='api/clients/DeleteClient.php?id=$id'><i class='fa fa-trash' aria-hidden='true'></i></a></td>
    </tr>";
}}

?>
