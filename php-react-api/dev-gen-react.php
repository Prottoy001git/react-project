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
function tableToClassName($table)
{
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));
}
function tableToFolder($table)
{
    return strtolower(str_replace('_', '-', $table));
}
function removeSpace($text)
{
    return str_replace(' ', '', $text);
}
function ucColumn($columnName)
{
    return ucwords(str_replace('_', ' ', $columnName));
}
function hyphenToSpace($value)
{
    return str_replace('-', ' ', $value);
}
function removePrefix($value, $prefix = '')
{
    return str_starts_with($value, $prefix) ? substr($value, strlen($prefix)) : $value;
}
function tableWithoutPrefix($value, $prefix = '')
{
    return str_starts_with($value, $prefix) ? substr($value, strlen($prefix)) : $value;
}
// Convert column to camelCase
function columnToCamelCase($column, $prefix = '')
{
    $column = str_starts_with($column, $prefix) ? substr($column, strlen($prefix)) : $column;
    $words = explode('_', $column);
    $first = array_shift($words);
    $words = array_map('ucfirst', $words);
    return $first . implode('', $words);
}
function tableToSingularCamelCase($tableName, $prefix = '')
{
    // Remove prefix if present
    if (str_starts_with($tableName, $prefix)) {
        $tableName = substr($tableName, strlen($prefix));
    }
    // Singularize last word
    $parts = explode('_', $tableName);
    $last = array_pop($parts);
    $last = singularize($last); // Apply singularization
    // Convert to camelCase
    $parts[] = $last;
    $first = array_shift($parts);
    $parts = array_map('ucfirst', $parts);
    return $first . implode('', $parts);
}

// Basic singularization helper (expandable)
function singularize($word)
{
    if (preg_match('/ies$/', $word)) {
        return preg_replace('/ies$/', 'y', $word); // parties → party
    } elseif (preg_match('/sses$/', $word)) {
        return preg_replace('/es$/', '', $word); // addresses → address
    } elseif (preg_match('/xes$/', $word)) {
        return preg_replace('/es$/', '', $word); // boxes → box
    } elseif (preg_match('/ses$/', $word)) {
        return preg_replace('/es$/', '', $word); // processes → process
    } elseif (preg_match('/s$/', $word) && !preg_match('/ss$/', $word)) {
        return preg_replace('/s$/', '', $word); // customers → customer
    }
    return $word;
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
function applyWrapperTemplate(string $template, string $topPhp, string $titleSection, string $contentSection): string
{
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
        $modelCode .= "\n    public function __construct(" . implode(', ', array_map(function ($c) {
            return "\$_$c";
        }, $columns)) . ") {\n";
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


        // === API & ROUTE FILE GENERATION ===
        $apiFolder = "api";
        if (!is_dir($apiFolder)) mkdir($apiFolder, 0777, true);
        $apiFilePath = "$apiFolder/{$folderName}-api.php";

        // --- API FILE ---
        $apiCode = "<?php\n\n";
        // GET ALL
        $apiCode .= "function get{$className}s() {\n";
        $apiCode .= "    echo json_encode({$className}::readAll());\n";
        $apiCode .= "}\n\n";

        // GET BY ID
        $apiCode .= "function get{$className}ById(\$_id) {\n";
        $apiCode .= "    echo json_encode({$className}::readById(\$_id));\n";
        $apiCode .= "}\n\n";

        // CREATE
        $apiCode .= "function create{$className}(\$data) {\n";
        $apiCode .= "    global \$db;\n";
        $apiCode .= "    \$obj = new {$className}(";
        $constructorArgs = [];
        foreach ($columns as $col) {
            $constructorArgs[] = ($col === 'id') ? "null" : "\$data['$col'] ?? ''";
        }
        $apiCode .= implode(", ", $constructorArgs);
        $apiCode .= ");\n";
        $apiCode .= "    echo json_encode(['result' => \$obj->create()]);\n";
        $apiCode .= "}\n\n";

        // UPDATE
        $apiCode .= "function update{$className}(\$_id, \$data) {\n";
        $apiCode .= "    global \$db;\n";
        $apiCode .= "    \$obj = new {$className}(";
        $constructorArgs = [];
        foreach ($columns as $col) {
            $constructorArgs[] = ($col === 'id') ? "\$_id" : "\$data['$col'] ?? ''";
        }
        $apiCode .= implode(", ", $constructorArgs);
        $apiCode .= ");\n";
        $apiCode .= "    echo json_encode(['result' => \$obj->update(\$_id)]);\n";
        $apiCode .= "}\n\n";

        // DELETE
        $apiCode .= "function delete{$className}(\$_id) {\n";
        $apiCode .= "    echo json_encode(['result' => {$className}::delete(\$_id)]);\n";
        $apiCode .= "}\n\n";
        $apiCode .= "?>";

        file_put_contents($apiFilePath, $apiCode);

        // --- ROUTE FILE ---
        $routeFolder = "api/routes";
        if (!is_dir($routeFolder)) mkdir($routeFolder, 0777, true);
        $routeFilePath = "$routeFolder/{$folderName}-route.php";

        $singular = singularize($folderName);  // e.g. 'customers' → 'customer'

        $routeCode = "<?php\n\n";
        $routeCode .= "if (\$endpoint == '{$folderName}' && \$request == 'GET') {\n";
        $routeCode .= "    get{$className}s();\n";
        $routeCode .= "} elseif (\$endpoint == '{$singular}' && \$request == 'GET') {\n";
        $routeCode .= "    get{$className}ById(\$_GET['id'] ?? 0);\n";
        $routeCode .= "} elseif (\$endpoint == 'create-{$singular}' && \$request == 'POST') {\n";
        $routeCode .= "    \$data = json_decode(file_get_contents('php://input'), true);\n";
        $routeCode .= "    create{$className}(\$data);\n";
        $routeCode .= "} elseif (\$endpoint == 'edit-{$singular}' && \$request == 'PUT') {\n";
        $routeCode .= "    \$data = json_decode(file_get_contents('php://input'), true);\n";
        $routeCode .= "    update{$className}(\$_GET['id'] ?? 0, \$data);\n";
        $routeCode .= "} elseif (\$endpoint == 'delete-{$singular}' && \$request == 'DELETE') {\n";
        $routeCode .= "    delete{$className}(\$_GET['id'] ?? 0);\n";
        $routeCode .= "}\n\n";
        $routeCode .= "?>";

        file_put_contents($routeFilePath, $routeCode);

        // --- REACT PAGE ROUTE FILE ---
$reactRouteFile = "react/src/page-route-main.tsx";
$singularLower = strtolower($singular); // e.g. "customer"
$pluralLower = strtolower($folderName); // e.g. "customers"

// React component names (PascalCase)
$manageComponent = "Manage".singularize($className);
$createComponent = "Create".singularize($className);
$editComponent = "Edit".singularize($className);
$detailsComponent = "Details".singularize($className);

// --- IMPORTS ---
$importBasePath = "./components/pages/{$pluralLower}";
$importStatements = <<<IMPORTS
import {$manageComponent} from '{$importBasePath}/{$manageComponent}.tsx';
import {$createComponent} from '{$importBasePath}/{$createComponent}.tsx';
import {$editComponent} from '{$importBasePath}/{$editComponent}.tsx';
import {$detailsComponent} from '{$importBasePath}/{$detailsComponent}.tsx';
IMPORTS;

// --- ROUTES ---
$routeEntries = <<<ROUTES
// {$className} Routes
{ path: '/{$pluralLower}', element: <{$manageComponent}/> },
{ path: '/{$folderName}/create', element: <{$createComponent}/> },
{ path: '/{$folderName}/edit/:id', element: <{$editComponent}/> },
{ path: '/{$folderName}/details/:id', element: <{$detailsComponent}/> },
ROUTES;

// --- WRITE NEW FILE ---
$updatedContent = <<<CONTENT
$importStatements

$routeEntries

CONTENT;

// Overwrite the file completely
file_put_contents($reactRouteFile, $updatedContent);


        // === BUILD REACT INTERFACE ===
        $reactFolder = "react/src/interfaces";

        // Ensure folder exists
        if (!is_dir($reactFolder)) mkdir($reactFolder, 0777, true);

        // Use $table from POST
        $tableName = $table; // make sure $table is defined

        // Remove prefix if needed
        $cleanTableName = str_starts_with($tableName, $database_table_prefix) ? substr($tableName, strlen($database_table_prefix)) : $tableName;

        $cleanTableName = singularize(removeSpace(ucColumn($cleanTableName)));

        // Format class and variable names
        $className = ucfirst($cleanTableName);
        $defaultName = lcfirst($className) . "Default"; // e.g., roleDefault

        // Fetch columns with NULL info
        $columnsData = [];
        $res = $db->query("DESCRIBE `$tableName`");
        while ($row = $res->fetch_assoc()) {
            $columnsData[] = $row;
        }

        // Interface file path
        $interfaceFilePath = "$reactFolder/" . singularize(lcfirst($className)) . ".interface.tsx";

        // === INTERFACE HEADER ===
        $code  = "export interface {$className} {\n";

        foreach ($columnsData as $colData) {
            $col = columnToCamelCase($colData['Field'], $database_table_prefix);
            $optional = strtoupper($colData['Null']) === 'YES' ? '?' : '';
            $type = "string";

            // Determine type
            if ($colData['Field'] === 'id' || str_ends_with($colData['Field'], '_id')) {
                $type = "number";
            } elseif (in_array($colData['Field'], ['photo', 'file', 'image'])) {
                $type = "File | null";
            }

            $code .= "  {$col}{$optional}: {$type},\n";
        }

        $code .= "}\n\n";

        // === DEFAULT OBJECT ===
        $code .= "const {$defaultName}: {$className} = {\n";

        foreach ($columnsData as $colData) {
            $col = columnToCamelCase($colData['Field'], $database_table_prefix);

            if ($colData['Field'] === 'id' || str_ends_with($colData['Field'], '_id')) {
                $defaultValue = "0";
            } elseif (in_array($colData['Field'], ['photo', 'file', 'image'])) {
                $defaultValue = "null";
            } else {
                $defaultValue = "\"\"";
            }

            $code .= "  {$col}: {$defaultValue},\n";
        }

        $code .= "};\n\n";
        $code .= "export default {$defaultName};\n";

        // Write to file
        file_put_contents($interfaceFilePath, $code);


        // === REACT COMPONENT GENERATION ===
        $reactBasePath = 'react/src/components/pages/';
        $camelCaseName = tableToSingularCamelCase($table, $database_table_prefix);
        $pascalCaseName = ucfirst($camelCaseName);
        $componentName = "Manage" . ucfirst($camelCaseName);
        $folderName = strtolower(str_replace('_', '-', tableWithoutPrefix($table, $database_table_prefix)));
        $interfaceName = ucfirst($camelCaseName); // e.g., User
        $componentTitle = ucColumn(hyphenToSpace($folderName));

        $reactFolderPath = $reactBasePath . $folderName;
        $reactFilePath = $reactFolderPath . "/{$componentName}.tsx";

        // Create folder if it doesn't exist
        if (!is_dir($reactFolderPath)) {
            mkdir($reactFolderPath, 0777, true);
        }

        // Generate React component code
        $reactCode = <<<TSX
import { useEffect, useState } from "react";
import api from "../../../config";
import type { {$interfaceName} } from "../../../interfaces/{$camelCaseName}.interface";
import { Link } from "react-router-dom";

function {$componentName}() {
  const [{$camelCaseName}s, set{$pascalCaseName}s] = useState<{$interfaceName}[]>([]);
  const [{$camelCaseName}Id, set{$pascalCaseName}Id] = useState<number | undefined>(0);

  useEffect(() => {
    document.title = "Manage {$componentTitle}";
    get{$pascalCaseName}s();
  }, []);

  const get{$pascalCaseName}s = () => {
    api.get("{$folderName}")
    .then((res) => {
      console.log(res.data);
      set{$pascalCaseName}s(res.data);
    })
    .catch((err) => {
      console.error(err);
    });
  };

  function handleDelete(id: number) {
    api.delete(`delete-{$singular}`, {
      params: {
        id: id,
      }
    })
    .then((res) => {
      console.log(res.data);
      alert(res.data);
      get{$pascalCaseName}s();
    })
    .catch((err) => {
      console.error(err);
    });
  }

  return (
    <>
    <div className="container-xxl flex-grow-1 container-p-y">
      <h4 className="fw-bold py-3 mb-4"><Link to="/{$folderName}" className="text-muted fw-light">{$componentTitle} </Link> / Manage</h4>
      <Link to="/{$folderName}/create" className="btn btn-primary">Add New</Link>
      <div className="card mt-3">
        <div className="table-responsive">
            <table className="table table-striped">
                <thead>
                    <tr>
                    
TSX;

        // Generate headers
        foreach ($columns as $col) {
            $reactCode .= "                       <th>" . ucColumn($col) . "</th>\n";
        }
        $reactCode .= "                        <th>Actions</th>\n";
        $reactCode .= "                    </tr>\n";
        $reactCode .= "                </thead>\n";
        $reactCode .= "                <tbody>\n";
        $reactCode .= "                    {\n";
        $reactCode .= "                        {$camelCaseName}s.map((item) => (\n";
        $reactCode .= "                            <tr key={item.id}>\n";

        foreach ($columns as $col) {
            $reactCode .= "                                <td>{item.$col}</td>\n";
        }

        $reactCode .= <<<ACTIONS
                                <td>
                                    <div className="d-flex gap-1">
                                        <Link to={`/{$folderName}/details/\${item.id}`} className="btn btn-icon btn-outline-info">
                                            <span className="tf-icons bx bx-search"></span>
                                        </Link>
                                        <Link to={`/{$folderName}/edit/\${item.id}`} className="btn btn-icon btn-outline-primary">
                                            <span className="tf-icons bx bx-edit"></span>
                                        </Link>
                                        <button type="button" className="btn btn-icon btn-outline-danger" onClick={() => set{$pascalCaseName}Id(item.id)} data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <span className="tf-icons bx bx-trash"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))
                    }
                </tbody>
            </table>
        </div>
      </div>
    </div>

    {/* Delete modal */}
    <div className="modal" id="deleteModal" tabIndex={-1}>
      <div className="modal-dialog">
        <div className="modal-content">
          <div className="modal-header">
            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div className="modal-body text-center">
            <span className="bx bx-trash fs-1 text-danger mb-3"></span>
            <h5 className="text-center mb-0">Are you sure you want to delete?</h5>
          </div>
          <div className="modal-footer justify-content-center">
            <button type="button" className="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" className="btn btn-danger" data-bs-dismiss="modal" onClick={() => handleDelete({$camelCaseName}Id!)}>Delete</button>
          </div>
        </div>
      </div>
    </div>
    </>
  );
}

export default {$componentName};
ACTIONS;

        // Save the React file
        file_put_contents($reactFilePath, $reactCode);

        $createComponentName = "Create" . ucfirst($camelCaseName);
        $createFilePath = $reactFolderPath . "/{$createComponentName}.tsx";

        // === Generate Create Component ===
        $createCode = <<<TSX
import { Link } from "react-router-dom";
import api from "../../../config";
import { useEffect, useState } from "react";
import type { {$interfaceName} } from "../../../interfaces/{$camelCaseName}.interface";
import {$camelCaseName}Default from "../../../interfaces/{$camelCaseName}.interface";

function {$createComponentName}() {
    const [{$camelCaseName}, set{$pascalCaseName}] = useState<{$interfaceName}>({$camelCaseName}Default);

    useEffect(() => {
        document.title = "Create {$componentTitle}";
    }, []);

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();

        api.post("create-{$singular}", {$camelCaseName})
        .then((res) => {
            console.log(res.data);
        })
        .catch((err) => {
            console.log(err);
        });
    }

    return (
        <>
        <div className="container-xxl flex-grow-1 container-p-y">
          <h4 className="fw-bold py-3 mb-4">
            <Link to="/{$folderName}" className="text-muted fw-light">{$componentTitle} /</Link> Create
          </h4>
          <div className="card mt-3">
            <h5 className="card-header">Create {$pascalCaseName}</h5>
            <div className="card-body">
                <form onSubmit={handleSubmit}>
TSX;

        // === Generate form inputs dynamically ===
        foreach ($columns as $col) {
            $label = ucColumn($col);

            // skip `id` or primary key if it's auto-increment
            if ($col === 'id') continue;

            // Special handling
            if (str_contains($col, 'photo') || str_contains($col, 'image') || $col === 'file') {
                $createCode .= <<<TSX

                    <div className="mb-3">
                        <label className="form-label">{$label}</label>
                        <input type="file" name="{$col}" className="form-control"
                        onChange={(e) => {
                            if (e.target.files !== null) set{$pascalCaseName}({...{$camelCaseName}, {$col}: e.target.files[0]});
                        }} />
                    </div>
TSX;
            } elseif (str_contains($col, 'address') || str_contains($col, 'description')) {
                $createCode .= <<<TSX

                    <div className="mb-3">
                        <label className="form-label">{$label}</label>
                        <textarea name="{$col}" className="form-control" rows={4}
                        value={{$camelCaseName}.{$col}} 
                        onChange={(e) => set{$pascalCaseName}({...{$camelCaseName}, {$col}: e.target.value})}></textarea>
                    </div>
TSX;
            } elseif (str_ends_with($col, '_id')) {
                $rel = rtrim($col, '_id');
                $relPlural = $rel . "s";
                $relPascal = ucfirst($rel);
                $relCamel = $rel;
                $relInterface = ucfirst($rel);

                // Add related state and useEffect block
                $createCode = str_replace(
                    'useState<' . $interfaceName . '>(' . $camelCaseName . 'Default);',
                    "useState<{$interfaceName}>({$camelCaseName}Default);\n    const [{$relPlural}, set{$relPascal}] = useState<{$relInterface}[]>([]);",
                    $createCode
                );

                $createCode = str_replace(
                    '}, []);',
                    <<<JS
        get{$relPascal}s();
    }, []);

    const get{$relPascal}s = () => {
        api.get("{$relPlural}")
        .then((res) => {
            set{$relPascal}(res.data);
        })
        .catch((err) => {
            console.error(err);
        });
JS,
                    $createCode
                );

                $createCode .= <<<TSX

                    <div className="mb-3">
                        <label className="form-label">{$label}</label>
                        <select name="{$col}" className="form-select" onChange={(e) => set{$pascalCaseName}({...{$camelCaseName}, {$col}: parseInt(e.target.value)})}>
                            {
                                {$relPlural}.map((item) =>
                                    <option value={item.id} key={item.id}>{item.name}</option>
                                )
                            }
                        </select>
                    </div>
TSX;
            } else {
                $createCode .= <<<TSX

                    <div className="mb-3">
                        <label className="form-label">{$label}</label>
                        <input type="text" name="{$col}" className="form-control" 
                        value={{$camelCaseName}.{$col}} 
                        onChange={(e) => set{$pascalCaseName}({...{$camelCaseName}, {$col}: e.target.value})} />
                    </div>
TSX;
            }
        }

        // === Submit button and closing tags
        $createCode .= <<<TSX

                    <button type="submit" className="btn btn-primary">Submit</button>
                </form>
            </div>
          </div>
        </div>
        </>
    );
}

export default {$createComponentName};
TSX;

        // Write the file
        file_put_contents($createFilePath, $createCode);

        $editComponentName = "Edit" . ucfirst($camelCaseName);
        $editFilePath = $reactFolderPath . "/{$editComponentName}.tsx";

        $editCode = <<<TSX
import { Link, useParams } from "react-router-dom";
import api from "../../../config";
import { useEffect, useState } from "react";
import type { {$interfaceName} } from "../../../interfaces/{$camelCaseName}.interface";
import {$camelCaseName}Default from "../../../interfaces/{$camelCaseName}.interface";

function {$editComponentName}() {
    const [{$camelCaseName}, set{$pascalCaseName}] = useState<{$interfaceName}>({$camelCaseName}Default);
    const { id } = useParams();

    useEffect(() => {
        document.title = "Edit {$componentTitle}";
        getDataById();
TSX;

        // Check if any related foreign tables (e.g., role_id)
        foreach ($columns as $col) {
            if (str_ends_with($col, '_id')) {
                $rel = rtrim($col, '_id');
                $relPascal = ucfirst($rel);
                $editCode .= "\n        get{$relPascal}s();";
            }
        }
        $editCode .= "\n    }, []);\n";

        // Get existing record
        $editCode .= <<<TSX

    const getDataById = () => {
        api.get("{$singular}?id=" + id)
        .then((res) => {
            set{$pascalCaseName}(res.data);
            alert(res.data);
        })
        .catch((err) => {
            console.error(err);
        });
    };
TSX;

        // Related dropdowns
        foreach ($columns as $col) {
            if (str_ends_with($col, '_id')) {
                $rel = rtrim($col, '_id');
                $relPascal = ucfirst($rel);
                $relPlural = $rel . 's';
                $editCode .= <<<TSX

    const [{$relPlural}, set{$relPascal}] = useState([]);
    const get{$relPascal}s = () => {
        api.get("{$relPlural}")
        .then((res) => {
            set{$relPascal}(res.data);
        })
        .catch((err) => {
            console.error(err);
        });
    };
TSX;
            }
        }

        // Handle submit
        $editCode .= <<<TSX

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        api.put("edit-{$singular}?id=" + id, {$camelCaseName})
        .then((res) => {
            console.log(res.data);
        })
        .catch((err) => {
            console.error(err);
        });
    };
TSX;

        // JSX Start
        $editCode .= <<<TSX

    return (
        <>
        <div className="container-xxl flex-grow-1 container-p-y">
          <h4 className="fw-bold py-3 mb-4">
            <Link to="/{$folderName}" className="text-muted fw-light">{$componentTitle} /</Link> Edit
          </h4>
          <div className="card mt-3">
            <h5 className="card-header">Edit {$componentTitle}</h5>
            <div className="card-body">
                <form onSubmit={handleSubmit}>
TSX;

        // Loop through fields to generate form inputs
        foreach ($columns as $col) {
            if ($col === 'id') continue;

            $label = ucColumn($col);

            if (str_contains($col, 'photo') || str_contains($col, 'image')) {
                $editCode .= <<<TSX

                    <div className="mb-3">
                        <label className="form-label">{$label}</label>
                        <input type="file" name="{$col}" className="form-control"
                        onChange={(e) => {
                            if (e.target.files !== null) set{$pascalCaseName}({...{$camelCaseName}, {$col}: e.target.files[0]});
                        }} />
                    </div>
TSX;
            } elseif (str_contains($col, 'address') || str_contains($col, 'description')) {
                $editCode .= <<<TSX

                    <div className="mb-3">
                        <label className="form-label">{$label}</label>
                        <textarea name="{$col}" className="form-control" rows={4}
                        value={{$camelCaseName}.{$col}}
                        onChange={(e) => set{$pascalCaseName}({...{$camelCaseName}, {$col}: e.target.value})}></textarea>
                    </div>
TSX;
            } elseif (str_ends_with($col, '_id')) {
                $rel = rtrim($col, '_id');
                $relPlural = $rel . "s";
                $editCode .= <<<TSX

                    <div className="mb-3">
                        <label className="form-label">{$label}</label>
                        <select name="{$col}" className="form-select"
                        value={{$camelCaseName}.{$col}}
                        onChange={(e) => set{$pascalCaseName}({...{$camelCaseName}, {$col}: parseInt(e.target.value)})}>
                            {
                                {$relPlural}.map((item) =>
                                    <option value={item.id} key={item.id}>{item.name}</option>
                                )
                            }
                        </select>
                    </div>
TSX;
            } else {
                $editCode .= <<<TSX

                    <div className="mb-3">
                        <label className="form-label">{$label}</label>
                        <input type="text" name="{$col}" className="form-control"
                        value={{$camelCaseName}.{$col}}
                        onChange={(e) => set{$pascalCaseName}({...{$camelCaseName}, {$col}: e.target.value})} />
                    </div>
TSX;
            }
        }

        $editCode .= <<<TSX

                    <button type="submit" className="btn btn-primary">Update</button>
                </form>
            </div>
          </div>
        </div>
        </>
    );
}

export default {$editComponentName};
TSX;

        // Save Edit file
        file_put_contents($editFilePath, $editCode);


        $detailsComponentName = "Details" . ucfirst($camelCaseName);
        $detailsFilePath = $reactFolderPath . "/{$detailsComponentName}.tsx";

        $detailsCode = <<<TSX
import { Link, useParams } from "react-router-dom";
import api from "../../../config";
import { useEffect, useState } from "react";
import type { {$interfaceName} } from "../../../interfaces/{$camelCaseName}.interface";
import {$camelCaseName}Default from "../../../interfaces/{$camelCaseName}.interface";

function {$detailsComponentName}() {
  const [{$camelCaseName}, set{$pascalCaseName}] = useState<{$interfaceName}>({$camelCaseName}Default);
  const { id } = useParams();

  useEffect(() => {
    document.title = "Details {$componentTitle}";
    api.get("{$singular}?id=" + id)
      .then((res) => {
        set{$pascalCaseName}(res.data);
      })
      .catch((err) => {
        console.error(err);
      });
  }, [id]);

  return (
    <div className="container-xxl flex-grow-1 container-p-y">
      <h4 className="fw-bold py-3 mb-4">
        <Link to="/{$folderName}" className="text-muted fw-light">{$componentTitle} /</Link> Details
      </h4>
      <div className="card">
        <div className="table-responsive text-nowrap">
          <table className="table table-bordered">
            <tbody>
TSX;

        // Generate rows for each column
        foreach ($columns as $col) {
            $label = ucColumn($col);
            $detailsCode .= <<<TSX

              <tr>
                <th>{$label}</th>
                <td>{{$camelCaseName}.{$col}}</td>
              </tr>
TSX;
        }

        $detailsCode .= <<<TSX

            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}

export default {$detailsComponentName};
TSX;

        file_put_contents($detailsFilePath, $detailsCode);


        echo "✅ React interface, components, routes and php api created successfully for <strong>$table</strong>.<br>";

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