<?php

function getProducts(){
    echo json_encode(Products::readAll());
}
function getProductById($_id){
    echo json_encode(Products::readById($_id));
}
function getProductByCategory($_id){
    echo json_encode(Products::readByCategory($_id));
}

?>