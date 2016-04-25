<?php

interface printManager {

    /**
     * Druckt alle Spiele welche sich aus den Teams ergeben
     * @param $teams array mit den Teamnamen
     * @param $bewerb
     * @param $gruppe
     * @param $info
     * @param $printBackground True wenn der Hintergrund gedruckt werden soll
     * @param $offsetX Offset in X-Richtung
     * @param $offsetY Offset in Y-Richtung
     * @return mixed
     */
    public function printGames($teams,$bewerb,$gruppe,$info,$printBackground,$offsetX,$offsetY);

    /**
     * Druckt einen Gruppen Raster
     * @param $teams array mit den Teamnamen
     * @param $bewerb
     * @param $gruppe
     * @param $format Druckgröße des Rasters. A4 oder A3
     * @param $printBackground True wenn der Hintergrund gedruckt werden soll
     * @param $rasterGroesse die groesse des Rasters, also 4er, 5er oder 6er Gruppe
     * @param $offsetX Offset in X-Richtung
     * @param $offsetY Offset in Y-Richtung
     * @return mixed
     */
    public function printRaster($teams,$bewerb,$gruppe,$format, $printBackground,$rasterGroesse,$offsetX,$offsetY);
}