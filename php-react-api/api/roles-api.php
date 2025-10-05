<?php
function getRoles() {
    echo json_encode(Roles::readAll());
}
function createRole($data){
    $role = new Roles(null, $data["name"]);
    echo json_encode($role->create());
}
?>