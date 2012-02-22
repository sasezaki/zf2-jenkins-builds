<?php
require_once __DIR__.'/library/Zend/Loader/StandardAutoloader.php';
$loader = new Zend\Loader\StandardAutoloader;
$loader->register();

$builds = __DIR__.'/builds';


$build_components = array();
foreach (new DirectoryIterator($builds) as $d) {
    if ($d->isDot()) continue;


    //echo $builds.'/'.$d.'/build/coverage/index.html', PHP_EOL;
    if (is_readable($c = $builds.'/'.$d.'/build/coverage/index.html')) {
        //echo $c, "..".$d, PHP_EOL;
        $build_components[(string)$d] = 
            array('coverage_per' => get_total_percentage($c),
                  'coverage_index' => ((string)$d).'/build/coverage/index.html');
    }
}

ksort($build_components);
//var_dump($build_components);die;

function get_total_percentage($coverage_html_path) {
    $dom = new Zend\Dom\Query(file_get_contents($coverage_html_path), 'UTF-8');
    $total = $dom->queryXpath('//table[@cellpadding="2"]//tr[3]//td[3]');
    return $total->current()->nodeValue;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Zend Framework 2 builds</title>
  <style>
    ::-moz-selection { background: #fe57a1; color: #fff; text-shadow: none; }
    ::selection { background: #fe57a1; color: #fff; text-shadow: none; }
    html { padding: 30px 10px; font-size: 20px; line-height: 1.4; color: #737373; 
        background-color: #f0dfff;
        background-image: url("zf-logo-mark340.png");
        background-position: left top;
        background-repeat:no-repeat;
        -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    html, input { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; }
    body { max-width: 500px; _width: 500px; padding: 30px 20px 50px; border: 1px solid #b3b3b3; 
           border-radius: 4px; margin: 0 auto; box-shadow: 0 1px 10px #a7a7a7, inset 0 1px 0 #fff; background: #fcfcfc; }
    h1 { margin: 0 10px; font-size: 30px; text-align: center; color: #000;}
    h1 span { color: #bbb; }
    h3 { margin: 1.5em 0 0.5em; }
    p { margin: 1em 0; }
    ul { padding: 0 0 0 40px; margin: 1em 0; }
    .container { max-width: 680px; _width: 380px; margin: 0 auto; 
        background-image: url("jenkins.png");
        background-position: right bottom;
        background-repeat:no-repeat;
    }
    table { border-collapse: collapse; border-spacing: 0; }
    th {font-size: 12px;}
    td { vertical-align: top; padding: 8px; margin: 20px;
           border-radius: 1px; margin: 0 auto; box-shadow: 0 1px 10px #a7a7a7, inset 0 1px 0 #fff; background: #fcfcfc; }
    td a {color: #33f; text-decoration: none;}
    td a:visited {color: #339;}
    /* google search */
    #goog-fixurl ul { list-style: none; padding: 0; margin: 0; }
    #goog-fixurl form { margin: 0; }
    #goog-wm-qt, #goog-wm-sb { border: 1px solid #bbb; font-size: 16px; line-height: normal; vertical-align: top; color: #444; border-radius: 2px; }
    #goog-wm-qt { width: 220px; height: 20px; padding: 5px; margin: 5px 10px 0 0; box-shadow: inset 0 1px 1px #ccc; }
    #goog-wm-sb { display: inline-block; height: 32px; padding: 0 10px; margin: 5px 0 0; white-space: nowrap; cursor: pointer; background-color: #f5f5f5; background-image: -webkit-linear-gradient(rgba(255,255,255,0), #f1f1f1); background-image: -moz-linear-gradient(rgba(255,255,255,0), #f1f1f1); background-image: -ms-linear-gradient(rgba(255,255,255,0), #f1f1f1); background-image: -o-linear-gradient(rgba(255,255,255,0), #f1f1f1); -webkit-appearance: none; -moz-appearance: none; appearance: none; *overflow: visible; *display: inline; *zoom: 1; }
    #goog-wm-sb:hover, #goog-wm-sb:focus { border-color: #aaa; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1); background-color: #f8f8f8; }
    #goog-wm-qt:focus, #goog-wm-sb:focus { border-color: #105cb6; outline: 0; color: #222; }
    input::-moz-focus-inner { padding: 0; border: 0; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Zend Framework 2 project builds</h1>
    <p>ZF2 build results page (code coverage &amp; others)</p>
    </ul>
    <table>
        <tr>
            <th>component</th>
            <th>coverage</th>
        </tr>
        <!--<tr><td>Acl</td><td><a href="http://aa.com/">86.2%</a></td></tr>-->
        <?php foreach($build_components as $k => $c):?>
            <tr><td><?php echo $k;?></td><td><a href="<?php echo $c['coverage_index']?>"><?php echo $c['coverage_per'];?></a></td></tr>
        <?php endforeach ?>
    </table>
  </div>

