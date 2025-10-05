<?php
    function getOrdersByPage($id) {
        // echo "Order working";
        $orders = Orders::readByPage($id);
        echo json_encode($orders);
    }
?>