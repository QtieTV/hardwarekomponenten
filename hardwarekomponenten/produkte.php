<?php
require("includes/common.inc.php");
require("includes/config.inc.php");
require("includes/conn.inc.php");

function produkte_show($IDKategorie) {
    if($IDKategorie>0) {
        echo('<ul>');
        $sql = "
            SELECT
                tbl_produkte.*,
                tbl_lieferbarkeiten.Lieferbarkeit
            FROM tbl_produkte
            INNER JOIN tbl_lieferbarkeiten ON tbl_lieferbarkeiten.IDLieferbarkeit=tbl_produkte.FIDLieferbarkeit
            WHERE(
                tbl_produkte.FIDKategorie=" . $IDKategorie . "
            )
        ";
        $produkte = $GLOBALS["conn"]->query($sql) or die("Fehler in der Query: " . $GLOBALS["conn"]->error . "<br>" . $sql);
        while($produkt = $produkte->fetch_object()) {
            if(!is_null($produkt->Produktfoto)) {
                $img = '<img src="' . $produkt->Produktfoto . '" alt="' . $produkt->Produkt . '">';
            }
            else {
                $img = '<div>kein Foto verfügbar</div>';
            }
            echo('
                <li>
                    ' . $img . '
                    <strong>' . $produkt->Artikelnummer . ' - ' . $produkt->Produkt . '</strong>
                    <div>' . $produkt->Beschreibung . '</div>
                    <div>EUR ' . $produkt->Preis . '</div>
                    <div>' . $produkt->Lieferbarkeit . '</div>
                </li>
            ');
        }
        echo('</ul>');
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Produkte</title>
</head>

<body>
    <?php
    produkte_show(intval($_GET["IDKategorie"]));
    ?>
</body>
</html>