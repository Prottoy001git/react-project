<?php
$database_host = "localhost";
$database_username = "root";
$database_password = "";
$database_name = "new_ecom";
$database_table_prefix = "ecom_";

$db = new mysqli($database_host, $database_username, $database_password, $database_name);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Helpers
function tableToClassName($table) {
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));
}
function tableToFolder($table) {
    return strtolower(str_replace('_', '-', $table));
}
function ucColumn($columnName) {
    return ucwords(str_replace('_', ' ', $columnName));
}
function hyphenToSpace($value) {
    return str_replace('-', ' ', $value);
}
function removePrefix($value, $prefix = '') {
    return str_starts_with($value, $prefix) ? substr($value, strlen($prefix)) : $value;
}

// === Template (optional) ===
// Default template wraps page in backticks and full HTML scaffolding. Use <?php {TOP_PHP} close php tag, {TITLE_SECTION} and {CONTENT_SECTION}
// To disable wrapping/template, set this to "".
$globalWrapperTemplate = "
<?php
{TOP_PHP}
?>
<div class='content-wrapper'>
  <div class='content-header'>
    <div class='container-fluid'>
      <div class='row mb-2'>
        <div class='col-sm-6'>
          <h1 class='m-0'>{TITLE_SECTION}</h1>
        </div>
      </div>
    </div>
  </div>
  <section class='content'>
    <div class='container-fluid'>
      {CONTENT_SECTION}
    </div>
  </section>
</div>
";

// Apply template or fallback if template is empty/disabled
function applyWrapperTemplate(string $template, string $topPhp, string $titleSection, string $contentSection): string {
    $trimmed = trim($template);
    if ($trimmed === '' || $trimmed === '``') {
        $titleHtml = $titleSection ? "<h1>{$titleSection}</h1>\n" : '';
        return "<?php\n" . $topPhp . "\n?>\n" . $titleHtml . $contentSection;
    }
    return str_replace(
        ['{TOP_PHP}', '{TITLE_SECTION}', '{CONTENT_SECTION}'],
        [$topPhp, $titleSection, $contentSection],
        $template
    );
}

// Generate logic on POST
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table'])) {
    $table = $_POST['table'];
    $className = tableToClassName(removePrefix($table, $database_table_prefix));
    $folderName = tableToFolder(removePrefix($table, $database_table_prefix));

    // Validate table exists
    $escapedTable = $db->real_escape_string($table);
    $res = $db->query("SHOW TABLES LIKE '$escapedTable'");
    if (!$res || $res->num_rows === 0) {
        $message = "❌ Table '$table' not found.";
    } else {
        // Get columns
        $columns = [];
        $res = $db->query("DESCRIBE `$table`");
        while ($row = $res->fetch_assoc()) {
            $columns[] = $row['Field'];
        }

        // === MODEL GENERATION ===
        if (!is_dir('models')) mkdir('models');
        $modelPath = "models/{$folderName}.class.php";
        $modelCode = "<?php\n\n";
        $modelCode .= "class " . $className . " {\n";
        foreach ($columns as $col) {
            $modelCode .= "    public \$$col;\n";
        }
        $modelCode .= "\n    public function __construct(" . implode(', ', array_map(function($c){ return "\$_$c"; }, $columns)) . ") {\n";
        foreach ($columns as $col) {
            $modelCode .= "        \$this->$col = \$_$col;\n";
        }
        $modelCode .= "    }\n";

        // CREATE
        $modelCode .= "\n    public function create() {\n";
        $insertColumns = implode(',', array_map(fn($c) => "$c", $columns));
        $insertValues = [];
        foreach ($columns as $col) {
            $insertValues[] = "'{\$this->{$col}}'";
        }
        $modelCode .= "        global \$db;\n";
        $modelCode .= "        \$sql = \"INSERT INTO {$table} ({$insertColumns}) VALUES (" . implode(', ', $insertValues) . ")\";\n";
        $modelCode .= "        if (\$db->query(\$sql)) {\n";
        $modelCode .= "          return \$db->insert_id;\n";
        $modelCode .= "        } else {\n";
        $modelCode .= "          return \"Query failed: \" . \$db->error;\n";
        $modelCode .= "        }\n    }\n";

        // READ ALL
        $modelCode .= "\n    public static function readAll() {\n";
        $modelCode .= "        global \$db;\n";
        $modelCode .= "        \$sql = \"SELECT * FROM {$table}\";\n";
        $modelCode .= "        \$res = \$db->query(\$sql);\n";
        $modelCode .= "        if (\$res) {\n";
        $modelCode .= "          return \$res->fetch_all(MYSQLI_ASSOC);\n";
        $modelCode .= "        } else {\n";
        $modelCode .= "          return \"Query failed: \" . \$db->error;\n";
        $modelCode .= "        }\n    }\n";

        // READ BY ID
        $modelCode .= "\n    public static function readById(\$id) {\n";
        $modelCode .= "        global \$db;\n";
        $modelCode .= "        \$id = (int)\$id;\n";
        $modelCode .= "        \$sql = \"SELECT * FROM {$table} WHERE id = \$id\";\n";
        $modelCode .= "        \$res = \$db->query(\$sql);\n";
        $modelCode .= "        if (\$res) {\n";
        $modelCode .= "          return \$res->fetch_assoc();\n";
        $modelCode .= "        } else {\n";
        $modelCode .= "          return \"Query failed: \" . \$db->error;\n";
        $modelCode .= "        }\n    }\n";

        // UPDATE
        $modelCode .= "\n    public function update(\$id) {\n";
        $setValues = [];
        foreach ($columns as $col) {
            $setValues[] = "$col='{\$this->{$col}}'";
        }
        $modelCode .= "        global \$db;\n";
        $modelCode .= "        \$sql = \"UPDATE {$table} SET " . implode(', ', $setValues) . " WHERE id = \$id\";\n";
        $modelCode .= "        if (\$db->query(\$sql)) {\n";
        $modelCode .= "          if (\$db->affected_rows > 0) {\n";
        $modelCode .= "            return \"Update successful.\";\n";
        $modelCode .= "          } else {\n";
        $modelCode .= "            return \"No changes made or record not found.\";\n";
        $modelCode .= "          }\n";
        $modelCode .= "        } else {\n";
        $modelCode .= "          return \"Update failed: \" . \$db->error;\n";
        $modelCode .= "        }\n    }\n";

        // DELETE
        $modelCode .= "\n    public static function delete(\$id) {\n";
        $modelCode .= "        global \$db;\n";
        $modelCode .= "        \$sql = \"DELETE FROM {$table} WHERE id = \$id\";\n";
        $modelCode .= "        if (\$db->query(\$sql)) {\n";
        $modelCode .= "          if (\$db->affected_rows > 0) {\n";
        $modelCode .= "            return \"Delete successful.\";\n";
        $modelCode .= "          } else {\n";
        $modelCode .= "            return \"No record found with ID \$id.\";\n";
        $modelCode .= "          }\n";
        $modelCode .= "        } else {\n";
        $modelCode .= "          return \"Delete failed: \" . \$db->error;\n";
        $modelCode .= "        }\n    }\n";

        $modelCode .= "}\n";
        file_put_contents($modelPath, $modelCode);

        // === VIEW FOLDER & FILES ===
        $viewFolder = "view/pages/$folderName";
        if (!is_dir('view/pages')) mkdir('view/pages', 0777, true);
        if (!is_dir($viewFolder)) mkdir($viewFolder);
        $baseFiles = ['manage', 'create', 'edit', 'details'];

        foreach ($baseFiles as $file) {
            $filePath = "$viewFolder/{$folderName}-$file.php";

            $topPhp = "";
            $titleSection = "";
            $contentSection = "";

            if ($file === "manage") {
                $topPhp = "require_once(\"models/{$folderName}.class.php\");\n";
                $topPhp .= "\$msg = \"\";\n";
                $topPhp .= "if(isset(\$_POST['delete_id'])) {\n";
                $topPhp .= "  \$id = \$_POST['delete_id'];\n";
                $topPhp .= "  \$msg = {$className}::delete(\$id);\n";
                $topPhp .= "}\n";

                $titleSection = "Manage " . ucColumn(hyphenToSpace($folderName));

                $contentSection = "<a href=\"{$folderName}-create\" class=\"btn btn-primary mb-3\">Add New</a>\n\n";
                $contentSection .= "<?php if(\$msg) { ?>\n";
                $contentSection .= "<div class=\"alert alert-info alert-dismissible fade show\" role=\"alert\">\n";
                $contentSection .= "  <?php echo \$msg; ?>\n";
                $contentSection .= "  <button type=\"button\" class=\"btn-close close\" data-dismiss=\"alert\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>\n";
                $contentSection .= "</div>\n";
                $contentSection .= "<?php } ?>\n\n";
                $contentSection .= "<table class=\"table table-striped\">\n";
                $contentSection .= "  <thead>\n";
                $contentSection .= "  <tr>\n";
                foreach ($columns as $col) {
                    $contentSection .= "    <th>" . ucColumn($col) . "</th>\n";
                }
                $contentSection .= "    <th>Actions</th>\n";
                $contentSection .= "  </tr>\n";
                $contentSection .= "  </thead>\n";
                $contentSection .= "  <tbody>\n";
                $contentSection .= "  <?php\n";
                $contentSection .= "    \$items = {$className}::readAll();\n";
                $contentSection .= "    foreach(\$items as \$item){\n";
                $contentSection .= "      echo \"<tr>\";\n";
                foreach ($columns as $col) {
                    $contentSection .= "      echo \"<td>\".\$item['{$col}'].\"</td>\";\n";
                }
                $contentSection .= "  ?>\n";
                $contentSection .= "    <td>\n";
                $contentSection .= "      <form action=\"{$folderName}-details\" method=\"get\">\n";
                $contentSection .= "        <input type=\"hidden\" name=\"id\" value=\"<?php echo \$item['id']; ?>\">\n";
                $contentSection .= "        <input type=\"submit\" class=\"btn btn-info\" value=\"Details\">\n";
                $contentSection .= "      </form>\n";
                $contentSection .= "      <form action=\"{$folderName}-edit\" method=\"get\">\n";
                $contentSection .= "        <input type=\"hidden\" name=\"id\" value=\"<?php echo \$item['id']; ?>\">\n";
                $contentSection .= "        <input type=\"submit\" class=\"btn btn-primary\" value=\"Edit\">\n";
                $contentSection .= "      </form>\n";
                $contentSection .= "      <form method=\"post\">\n";
                $contentSection .= "        <input type=\"hidden\" name=\"delete_id\" value=\"<?php echo \$item['id']; ?>\">\n";
                $contentSection .= "        <input type=\"submit\" class=\"btn btn-danger\" value=\"Delete\">\n";
                $contentSection .= "      </form>\n";
                $contentSection .= "    </td>\n";
                $contentSection .= "  <?php\n";
                $contentSection .= "      echo \"</tr>\";\n";
                $contentSection .= "    }\n";
                $contentSection .= "  ?>\n";
                $contentSection .= "  </tbody>\n";
                $contentSection .= "</table>\n";

                $finalContent = applyWrapperTemplate($globalWrapperTemplate, $topPhp, $titleSection, $contentSection);
                file_put_contents($filePath, $finalContent);
            } elseif ($file === "create") {
                $topPhp = "require_once(\"models/{$folderName}.class.php\");\n";
                $topPhp .= "\$msg = \"\";\n";
                $topPhp .= "if (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n";
                $createVars = [];
                $constructorArgs = [];
                foreach ($columns as $col) {
                    if ($col === "id") {
                        $createVars[] = "    \$$col = null; // Assuming auto-increment";
                        $constructorArgs[] = "null";
                    } else {
                        $createVars[] = "    \$$col = \$_POST['$col'];";
                        $constructorArgs[] = "\$$col";
                    }
                }
                $topPhp .= implode("\n", $createVars) . "\n";
                $topPhp .= "    \$obj = new $className(" . implode(", ", $constructorArgs) . ");\n";
                $topPhp .= "    \$msg = \$obj->create();\n";
                $topPhp .= "}\n";

                $titleSection = "Create " . ucColumn(hyphenToSpace($folderName));

                $contentSection = "<a href=\"{$folderName}\" class=\"btn btn-primary mb-3\">Back to Manage</a>\n\n";
                $contentSection .= "<?php if(\$msg) { ?>\n";
                $contentSection .= "<div class=\"alert alert-info alert-dismissible fade show\" role=\"alert\">\n";
                $contentSection .= "  <?php echo \$msg; ?>\n";
                $contentSection .= "  <button type=\"button\" class=\"btn-close close close\" data-dismiss=\"alert\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>\n";
                $contentSection .= "</div>\n";
                $contentSection .= "<?php } ?>\n";
                $contentSection .= "<form method=\"post\">\n";
                $contentSection .= "  <input type=\"hidden\" name=\"id\">\n";
                $contentSection .= "  <div class=\"card-body\">\n";
                foreach ($columns as $col) {
                    if ($col === "id") continue;
                    $contentSection .= "    <div class=\"form-group mb-3\">\n";
                    $contentSection .= "      <label for=\"$col\">" . ucColumn($col) . "</label>\n";
                    $contentSection .= "      <input type=\"text\" class=\"form-control\" name=\"$col\" id=\"$col\">\n";
                    $contentSection .= "    </div>\n";
                }
                $contentSection .= "  </div>\n";
                $contentSection .= "  <div class=\"card-footer\">\n";
                $contentSection .= "    <button type=\"submit\" class=\"btn btn-success\">Submit</button>\n";
                $contentSection .= "  </div>\n";
                $contentSection .= "</form>\n";

                $finalContent = applyWrapperTemplate($globalWrapperTemplate, $topPhp, $titleSection, $contentSection);
                file_put_contents($filePath, $finalContent);
            } elseif ($file === "edit") {
                $topPhp = "require_once(\"models/{$folderName}.class.php\");\n";
                $topPhp .= "\$msg = \"\";\n";
                $topPhp .= "\$res = [];\n";
                $topPhp .= "if (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n";
                $editVars = [];
                $constructorArgs = [];
                foreach ($columns as $col) {
                    $editVars[] = "    \$$col = \$_POST['$col'];";
                    $constructorArgs[] = "\$$col";
                }
                $topPhp .= implode("\n", $editVars) . "\n";
                $topPhp .= "    \$obj = new $className(" . implode(", ", $constructorArgs) . ");\n";
                $topPhp .= "    \$msg = \$obj->update(\$id);\n";
                $topPhp .= "}\n";
                $topPhp .= "if (isset(\$_GET['id'])) {\n";
                $topPhp .= "    \$res = {$className}::readById(\$_GET['id']);\n";
                $topPhp .= "}\n";

                $titleSection = "Edit " . ucColumn(hyphenToSpace($folderName));

                $contentSection = "<a href=\"{$folderName}\" class=\"btn btn-primary mb-3\">Back to Manage</a>\n\n";
                $contentSection .= "<?php if(\$msg) { ?>\n";
                $contentSection .= "<div class=\"alert alert-info alert-dismissible fade show\" role=\"alert\">\n";
                $contentSection .= "  <?php echo \$msg; ?>\n";
                $contentSection .= "  <button type=\"button\" class=\"btn-close close\" data-dismiss=\"alert\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>\n";
                $contentSection .= "</div>\n";
                $contentSection .= "<?php } ?>\n";
                $contentSection .= "<?php if(!empty(\$res)) { ?>\n";
                $contentSection .= "<div class=\"card\">\n";
                $contentSection .= "  <form method=\"post\">\n";
                $contentSection .= "    <div class=\"card-body\">\n";
                $contentSection .= "      <input type=\"hidden\" name=\"id\" value=\"<?php echo \$res['id']; ?>\">\n";
                foreach ($columns as $col) {
                    if ($col === "id") continue;
                    $contentSection .= "      <div class=\"form-group mb-3\">\n";
                    $contentSection .= "        <label for=\"$col\">" . ucColumn($col) . "</label>\n";
                    $contentSection .= "        <input type=\"text\" class=\"form-control\" name=\"$col\" id=\"$col\" value=\"<?php echo htmlspecialchars(\$res['$col']); ?>\">\n";
                    $contentSection .= "      </div>\n";
                }
                $contentSection .= "    </div>\n";
                $contentSection .= "    <div class=\"card-footer\">\n";
                $contentSection .= "      <button type=\"submit\" class=\"btn btn-success\">Update</button>\n";
                $contentSection .= "    </div>\n";
                $contentSection .= "  </form>\n";
                $contentSection .= "</div>\n";
                $contentSection .= "<?php } ?>\n";

                $finalContent = applyWrapperTemplate($globalWrapperTemplate, $topPhp, $titleSection, $contentSection);
                file_put_contents($filePath, $finalContent);
            } elseif ($file === "details") {
                $topPhp = "require_once(\"models/{$folderName}.class.php\");\n";
                $topPhp .= "\$item = [];\n";
                $topPhp .= "if (isset(\$_GET[\"id\"])) {\n";
                $topPhp .= "    \$item = {$className}::readById(\$_GET[\"id\"]);\n";
                $topPhp .= "}\n";

                $titleSection = "Details of " . ucColumn(hyphenToSpace($folderName));

                $contentSection = "<a href=\"{$folderName}\" class=\"btn btn-primary mb-3\">Back to Manage</a>\n\n";
                $contentSection .= "<?php if (!empty(\$item)) { ?>\n";
                $contentSection .= "<table class=\"table table-striped\">\n";
                foreach ($columns as $col) {
                    $contentSection .= "  <tr>\n";
                    $contentSection .= "    <th>" . ucColumn($col) . "</th>\n";
                    $contentSection .= "    <td><?php echo htmlspecialchars(\$item['$col']); ?></td>\n";
                    $contentSection .= "  </tr>\n";
                }
                $contentSection .= "</table>\n";
                $contentSection .= "<?php } else { echo \"<p>No data found.</p>\"; } ?>\n";

                $finalContent = applyWrapperTemplate($globalWrapperTemplate, $topPhp, $titleSection, $contentSection);
                file_put_contents($filePath, $finalContent);
            }
        }

        // === MENU FILE GENERATION ===
        $menuFolder = "view/layout/menus";
        if (!is_dir("view/layout")) mkdir("view/layout", 0777, true);
        if (!is_dir($menuFolder)) mkdir($menuFolder);

        $menuFilePath = "$menuFolder/{$folderName}-menu.php";
        $menuLabel = ucColumn(hyphenToSpace($folderName));

        $menuContent = "<li class=\"nav-item\">\n";
        $menuContent .= "  <a href=\"$folderName\" class=\"nav-link\">\n";
        $menuContent .= "    <i class=\"nav-icon far fa-circle\"></i>\n";
        $menuContent .= "    <p>\n";
        $menuContent .= "      $menuLabel\n";
        $menuContent .= "    </p>\n";
        $menuContent .= "  </a>\n";
        $menuContent .= "</li>\n";

        file_put_contents($menuFilePath, $menuContent);

        // === ROUTE FILE GENERATION ===
        $routeFolder = "routes";
        if (!is_dir($routeFolder)) mkdir($routeFolder);
        $routeFilePath = "$routeFolder/{$folderName}-route.php";

        $routeCode = "<?php\n";
        $routeCode .= "if (\$page == \"$folderName\") {\n";
        $routeCode .= "    include_once('view/pages/{$folderName}/{$folderName}-manage.php');\n";
        $routeCode .= "} elseif (\$page == \"{$folderName}-create\") {\n";
        $routeCode .= "    include_once('view/pages/{$folderName}/{$folderName}-create.php');\n";
        $routeCode .= "} elseif (\$page == \"{$folderName}-edit\") {\n";
        $routeCode .= "    include_once('view/pages/{$folderName}/{$folderName}-edit.php');\n";
        $routeCode .= "} elseif (\$page == \"{$folderName}-details\") {\n";
        $routeCode .= "    include_once('view/pages/{$folderName}/{$folderName}-details.php');\n";
        $routeCode .= "}\n";
        $routeCode .= "?>";

        file_put_contents($routeFilePath, $routeCode);

        $message = "✅ php scaffolder successfully generated model, views, menu, and route for <strong>$table</strong>.";
    }
}

// Fetch tables
$tables = [];
$res = $db->query("SHOW TABLES");
while ($row = $res->fetch_array()) {
    $tables[] = $row[0];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Auto Generator</title>
    <meta charset="utf-8" />
</head>
<body style="text-align: center;">
    <h2>Generate Models and Views</h2>
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    <form method="post" style="display: inline-block;">
        <table border=1 cellspacing="0" cellpadding="15">
            <thead>
                <tr>
                    <th colspan="2">Database: <b><?php echo $database_name; ?></b></th>
                </tr>
                <tr>
                    <th>Table Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $table): ?>
                <tr>
                    <td><?= htmlspecialchars($table) ?></td>
                    <td><button type="submit" name="table" value="<?= htmlspecialchars($table) ?>">Generate</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</body>
</html>
