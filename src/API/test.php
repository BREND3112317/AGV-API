<?php
$time = -microtime(true);
require_once(__DIR__ . "/../../vendor/autoload.php");

use BREND\Constants\STATUS;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BREND\AGV\Models\AGV_request;
use BREND\AGV\Models\AGV_response;
use BREND\AGV\Controllers\AGVController;
use BREND\AGV\Algorithms\AGV_DFS;
use BREND\AGV\Algorithms\point;

$AGV = new AGVController("ITRI_3-1");
$Data = $AGV->getData(1);

$dfs = new AGV_DFS();
$dfs->setStartPoint($Data['Config']['Attitude']['Code'], $Data['Config']['Attitude']['Yaw']);
$dfs->Run();
$paths = $dfs->getAGVPreviewPath();
var_dump($paths);

$time += microtime(true);

echo "<br/><h2>Do php in $time seconds<h2>\n";
?>