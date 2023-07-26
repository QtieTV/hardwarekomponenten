<?php
require("includes/common.inc.php");
require("includes/config.inc.php");
require("includes/conn.inc.php");

if(count($_POST)==0) {
    $_POST["Suche"] = "";
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Individual-PCs</title>
</head>

<body>
    <h1>Individual-PCs</h1>
    <form method="post">
        <fieldset>
            <legend>Komponente</legend>
            <label>
                Name der Komponente oder der Artikelnummer (auch Teile des Namens/der Artikelnummer sind möglich):
                <input type="text" name="Suche" value="<?php echo($_POST["Suche"]); ?>">
            </label>
        </fieldset>
        <input type="submit" value="filtern">
    </form>
    <?php
    $arr_W = ["tbl_produkte.FIDKategorie=2"];
    
    if(count($_POST)>0) {
        if(strlen($_POST["Suche"])>0) {
            $arr_W[] = "
                (
                    SELECT COUNT(tbl_pr.IDProdukt) AS cnt FROM tbl_konfigurator
                    INNER JOIN tbl_produkte AS tbl_pr ON tbl_konfigurator.FIDKomponente=tbl_pr.IDProdukt
                    WHERE(
                        (
                            tbl_pr.Produkt LIKE '%" . $_POST["Suche"] . "%' OR
                            tbl_pr.Artikelnummer LIKE '%" . $_POST["Suche"] . "%'
                        ) AND
                        tbl_konfigurator.FIDPC=tbl_produkte.IDProdukt
                    )
                )>0
            ";
        }
    }
    
    $sql = "
        SELECT
            tbl_produkte.*,
            tbl_lieferbarkeiten.Lieferbarkeit
        FROM tbl_produkte
        INNER JOIN tbl_lieferbarkeiten ON tbl_lieferbarkeiten.IDLieferbarkeit=tbl_produkte.FIDLieferbarkeit
        WHERE(
            " . implode(" AND ",$arr_W) . "
        )
    ";
    $pcs = $GLOBALS["conn"]->query($sql) or die("Fehler in der Query: " . $GLOBALS["conn"]->error . "<br>" . $sql);
        echo('<ul>');
        while($pc = $pcs->fetch_object()) {
            echo('
                <li>
                    ' . $pc->Produkt . ':
                    <ul>
            ');
            $sql = "
                SELECT
                    tbl_produkte.*,
                    tbl_lieferbarkeiten.Lieferbarkeit
                FROM tbl_konfigurator
                INNER JOIN tbl_produkte ON tbl_produkte.IDProdukt=tbl_konfigurator.FIDKomponente
                INNER JOIN tbl_lieferbarkeiten ON tbl_lieferbarkeiten.IDLieferbarkeit=tbl_produkte.FIDLieferbarkeit
                WHERE(
                    FIDPC=" . $pc->IDProdukt . "
                )
                ORDER BY tbl_produkte.Produkt ASC
            ";
            $komponenten = $GLOBALS["conn"]->query($sql) or die("Fehler in der Query: " . $GLOBALS["conn"]->error . "<br>" . $sql);
            $ges = 0;
            while($produkt = $komponenten->fetch_object()) {
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
                
                $ges+= $produkt->Preis;
            }
            
            echo('        
                    </ul>
                    Gesamtpreis: ' . $ges . '
                </li>
            ');
        }
        echo('</ul>');
    ?>
</body>
</html>