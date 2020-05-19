<?php
/**
 * 'Helper Class' que realiza el proceso de exportación tanto de las
 * colecciones como de los ítems.
 *
 */
 Class CirExporter {

    /**
     * Devuelve el texto con formato xml asociado a un ítem.
     *
     *@param int $itemID EL identificador del ítem a exportar
     *@return string $xml El contenido del fichero xml
     */
    public function exportItem($itemID) {
        ob_start();
        $this->_generateCIR($itemID,false);
        return ob_get_clean();
    }

    /**
     * Genera el contenido del fichero xml asociado a una colección (Sólo la parte de metadatos de la colección).
     *
     *@param int $collectionID Identificador de la colección a exportar
     *@return void
     */
    public function exportCollectionMeta($collectionID) {        
        $this->_generateCirHeader();
        $this->_generateCirBody($collectionID,"Collection",false);
        $this->_generateCirFooter(false);
    }
    
    /**
     * Genera el contenido del fichero xml asociado a una colección.
     *
     *@param int $collectionID Identificador de la colección a exportar
     *@return void
     */
    public function exportCollectionFull($collectionID) {
        $items = get_records('Item',array('collection'=>$collectionID),999);

        $this->_generateCirHeader();
        $this->_generateCirBody($collectionID,"Collection");

        foreach($items as $item) {
            $this->_generateCirBody($item->id,"Item");
        }

        $this->_generateCirFooter();
    }

    /**
     * Exporta la colección al completo en un único fichero comprimido (.zip)
     * donde se almacenan todos los ítems que pertenecen a esa colección.
     *
     *@param int $collectionID Identificador de la colección a exportar
     *@return void
     */
    public function exportCollectionZip($collectionID) {
        /* Librería que nos permite generar un fichero comprimido (zip) */
        include_once(dirname(dirname(__FILE__)).'/libraries/zipstream-php-0.2.2/zipstream.php');

        $collection = get_record_by_id("collection",$collectionID);
        $items = get_records('Item',array('collection'=>$collectionID),999); // ítems de la colección

        error_reporting(0); // se desactiva toda notificación de error

        $zip = new ZipStream('Collection_'.$collection->id.'.zip');

        foreach($items as $item) { // añadimos el xml generado para cada item al zip
            ob_start();
            $this->_generateCIR($item->id,false);
            $zip->add_file("Item_".$item->id.".cir.xml", ob_get_clean() );
        }

        $zip->finish();
    }


    private function _generateCirHeader() {
        echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        //--------------------
        // HEADER
        //--------------------
        echo '<Collections ';
        echo 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';
        echo 'xmlns:dcterms="http://purl.org/dc/terms/" ';
        echo 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        echo 'xmlns:cir="http://www.cir.cenieh.es" ';
        echo ">\n";

    }
  
    private function _generateCirBody($recordID,$recordType, $full = True) {
        $record = get_record_by_id($recordType,$recordID);

        //--------------------
        //METADATA
        //--------------------
        echo ($recordType == "Collection") ? "\n\t<collection>\n" : "\t\t\t<record>\n";

        $elementArray = $record->getAllElements();

        foreach($elementArray as $elementSetName => $elements) {
            //Si se corresponde al monitor de estados, ignora
            if($elementSetName == 'Monitor') continue;
            ob_start();
            
            $flag = false;

            $eSSlug=$this->_getElementSetSlug($elementSetName);

            echo ($recordType == "Collection") ? "\t\t<metadata>\n" : "\t\t\t\t<metadata>\n";

            if($eSSlug!=="") $eSSlug .= ":";

            $unqualified = array(
                'title', 'creator', 'subject', 'description', 'publisher',
                'contributor', 'date', 'type', 'format', 'identifier',
                'source', 'language', 'relation', 'coverage', 'rights'
            );

            foreach($elements as $element) {
                $eSlug = $this->_getElementSlug($element->name,$elementSetName);
                $elementTexts =  $record->getElementTexts($elementSetName,$element->name);

        	      if(empty($elementTexts)) continue;

                $flag = true;

        	      foreach($elementTexts as $elementText) {
                    $eSlugPlus = false;
                    $preElement = ($recordType == "Collection") ? "\t\t\t<" : "\t\t\t\t\t<";
                    if ($eSlug == "language") $eSlugPlus = $eSlug.' xsi:type="dcterms:ISO639-1"';
                    if ($eSlug == "spatial"){
                        if (preg_match('/;/', $elementText->text)) {
                            if(count(explode(';',$elementText->text)) == 2){
                                $eSlugPlus = $eSlug.' xsi:type="dcterms:BOX"';
                            }
                        } else{
                            $eSlugPlus = $eSlug.' xsi:type="dcterms:POINT"';
                        }
                    }
                    echo (in_array($eSlug, $unqualified)) ? ($preElement.$eSSlug.(($eSlugPlus) ? $eSlugPlus : $eSlug).">") : ($preElement."dcterms:".( ($eSlugPlus) ? $eSlugPlus : $eSlug).">");
          		      echo htmlspecialchars($elementText->text);
                    echo (in_array($eSlug, $unqualified)) ? "</".$eSSlug.$eSlug.">\n" : "</"."dcterms:".$eSlug.">\n";
        	      }
      	    }

      	    echo ($recordType == "Collection") ? "\t\t</metadata>\n" : "\t\t\t\t</metadata>\n";

            $this->_free_buffer($flag);
        }
        if($full) {
          echo ($recordType == "Collection") ? "\t\t<records>\n" : "\t\t\t</record>\n";
        }
    }

    private function _generateCirFooter($full = True) {
        if($full) echo "\t\t</records>";
        echo "\n\t</collection>\n";
        echo "</Collection>\n";
    }

    /**
     * Genera e imprime el contenido del fichero xml asociado a un ítem.
     *
     *@param int $itemID Identificador del ítem
     */
    private function _generateCir($itemID) {
        if(!is_numeric($itemID)) {
            echo "ERROR: Invalid item ID";
            return;
        }

        $item = get_record_by_id("item",$itemID);

        if($item === null || empty($item)) {
            echo "ERROR: Invalid item ID";
            return;
        }

        $unqualified = array(
            'title', 'creator', 'subject', 'description', 'publisher',
            'contributor', 'date', 'type', 'format', 'identifier',
            'source', 'language', 'relation', 'coverage', 'rights' );

        echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        //--------------------
        // HEADER
        //--------------------
        echo '<Record ';
        echo 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';
        echo 'xmlns:dcterms="http://purl.org/dc/terms/" ';
        echo 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        echo 'xmlns:cir="http://www.cir.cenieh.es" ';
        echo ">\n";
        //--------------------
        //METADATA
        //--------------------
        $elementArray = $item->getAllElements();

        foreach($elementArray as $elementSetName => $elements) {
            ob_start();
            $flag = false;

            $eSSlug=$this->_getElementSetSlug($elementSetName);

            echo "\t<metadata>\n";

            if($eSSlug!=="") $eSSlug .= ":";

            foreach($elements as $element) {
                $eSlug = $this->_getElementSlug($element->name,$elementSetName);

                $elementTexts =  $item->getElementTexts($elementSetName,$element->name);

                if(empty($elementTexts)) continue;
                $flag = true;

                foreach($elementTexts as $elementText) {
                    $eSlugPlus = false;
                    if ($eSlug == "language") $eSlugPlus = $eSlug.' xsi:type="dcterms:ISO639-1"';
                    if ($eSlug == "spatial"){
                        if (preg_match('/;/', $elementText->text)) {
                            if(count(explode(';',$elementText->text)) == 2){
                                $eSlugPlus = $eSlug.' xsi:type="dcterms:BOX"';
                            }
                        } else{
                            $eSlugPlus = $eSlug.' xsi:type="dcterms:POINT"';
                        }
                    }
                    echo (in_array($eSlug, $unqualified)) ? ("\t\t\t<".$eSSlug.(($eSlugPlus) ? $eSlugPlus : $eSlug).">") : ("\t\t\t<"."dcterms:".( ($eSlugPlus) ? $eSlugPlus : $eSlug).">");
                    echo htmlspecialchars($elementText->text);
                    echo (in_array($eSlug, $unqualified)) ? "</".$eSSlug.$eSlug.">\n" : "</"."dcterms:".$eSlug.">\n";
        	      }
            }

            $this->_free_buffer($flag);
        }

        echo "\t</metadata>\n";
        echo "</Record>\n";
    }

    private function _free_buffer($flag = True){
        if($flag) {
      	    return ob_end_flush();
      	}
        return ob_end_clean();
    }
    /**
     *Devuelve el slug para un conjunto de datos determinado
     *
     *@param string $elementSetName Nombre del conjunto de datos
     *@return string El identificador del conjunto de datos o "unknow" si
     * es desconocido (no está instalado en Omeka)
     */
    private function _getElementSetSlug($elementSetName) {
        switch($elementSetName) {
            case 'Dublin Core':
                return 'dc';
            default:
                $elementSetName = str_replace(' ', '', $elementSetName);
                return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $elementSetName));
        }
    }


    /**
     * Devuelve el 'slug' asociado a un elemento del conjunto de metadatos
     * en función del nombre.
     *
     *@param string $elementName Nombre del elemento
     *@return string Nombre del conjunto al que pertenece
     */
    private function _getElementSlug($elementName) {
        $dces = new DublinCoreExtendedPlugin;
        foreach ($dces->getElements() as $elementDces) {
            if ($elementName == $elementDces['label']) {
              return $elementDces['name'];
            }
        }
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $elementName));
    }



    
    
 }

?>
