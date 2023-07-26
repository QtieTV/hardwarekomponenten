<?php
require("includes/common.inc.php");
require("includes/config.inc.php");
require("includes/conn.inc.php");

function kategorien_show($fid=null) {
    if(is_null($fid)) {
        $where = "
            WHERE(
                FIDKategorie IS NULL
            )
        ";
    }
    else {
        $where = "
            WHERE(
                FIDKategorie=" . $fid . "
            )
        ";
    }
    
    $sql = "
        SELECT * FROM tbl_kategorien
        " . $where . "
        ORDER BY Kategorie ASC
    ";
    
    echo('<ul>');
    $kats = $GLOBALS["conn"]->query($sql) or die("Fehler in der Query: " . $GLOBALS["conn"]->error . "<br>" . $sql);
    while($kat = $kats->fetch_object()) {
        echo('
            <li>
                <a href="produkte.php?IDKategorie=' . $kat->IDKategorie . '">' . $kat->Kategorie . '</a>
        ');
        kategorien_show($kat->IDKategorie);
        echo('
            </li>
        ');
    }
    echo('</ul>');
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Produktkategorien</title>
</head>

<body>
    <h1>Produktkategorien</h1>
    <?php
    kategorien_show();
    ?>
</body>
</html>