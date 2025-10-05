<?php
function getUsers(){
    // echo "Function Users";
    $users = Users::readAll();
    echo json_encode($users);
}
function createuser($data,$files){
    $image = imgUpload($files["photo"],"../uploads/users");
    if(isset($image["success"])){
        $photo = $image["success"];
    }else{
        $photo = "";
        echo json_encode(["success" =>false, "message" => $image["error"]]);
        exit;
    }
    $user = new Users(null, $data["name"], $data["email"], "", $data["role_id"], $data["address"], $photo );
    echo json_encode($user->create());

}

?>