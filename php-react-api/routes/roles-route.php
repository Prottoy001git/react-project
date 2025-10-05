<?php
if ($page == "roles") {
    include_once('view/pages/roles/roles-manage.php');
} elseif ($page == "roles-create") {
    include_once('view/pages/roles/roles-create.php');
} elseif ($page == "roles-edit") {
    include_once('view/pages/roles/roles-edit.php');
} elseif ($page == "roles-details") {
    include_once('view/pages/roles/roles-details.php');
}
?>