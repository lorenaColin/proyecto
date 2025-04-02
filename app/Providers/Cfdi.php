<?php

namespace App\Providers;
use Ramsey\Uuid\Uuid;
class Cfdi {

    protected $directorioComprobante;
    protected $totalImpuestosTraslados = 0;
    protected $totalImpuestosRetenidos = 0;
    
    protected $directorio;
    public function __construct($version, $fecha, $noCertificado, $certificado, $subTotal, $moneda, $total, $tipoComprobante, $lugarExpedicion, $exportacion, $formaPago="", $condicionesPago="", $metodoPago="", $descuento=0, $tipoCambio="", $serie="", $folio="", $directorio)
    {

        $this->xml = new \DOMDocument('1.0', 'UTF-8');
        $this->xml->formatOutput = true;
        $this->comprobante = $this->xml->createElement("cfdi:Comprobante");
        $this->xml->appendChild($this->comprobante);


        $this->comprobante->setAttribute("xmlns:cfdi", "http://www.sat.gob.mx/cfd/4");
        $this->comprobante->setAttribute("xmlns:xsd", "http://www.w3.org/2001/XMLSchema");
        $this->comprobante->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $this->comprobante->setAttribute("xsi:schemaLocation", "http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd");

        //Inicia datos obligatorios
        $this->comprobante->setAttribute("Version", $version);
        $this->comprobante->setAttribute("Fecha", $fecha);
        $this->comprobante->setAttribute("NoCertificado", $noCertificado);
        $this->comprobante->setAttribute("Certificado", $certificado);
        $this->comprobante->setAttribute("SubTotal", $subTotal);
        $this->comprobante->setAttribute("Moneda", $moneda);
        $this->comprobante->setAttribute("Total", $total);
        $this->comprobante->setAttribute("TipoDeComprobante", $tipoComprobante);
        $this->comprobante->setAttribute("LugarExpedicion", $lugarExpedicion);
        $this->comprobante->setAttribute("Exportacion", $exportacion);
        //Termina datos obligatorios

        //Inicia datos opcionales
        if($serie != ""){
            $this->comprobante->setAttribute("Serie", $serie);
        }

        if($folio != ""){
            $this->comprobante->setAttribute("Folio", $folio);
        }
        //Termina datos opcionales

        //Inicia datos condicionales
        if($tipoComprobante == "I" || $tipoComprobante == "E") {
            $this->comprobante->setAttribute("FormaPago", $formaPago);
            if($condicionesPago != ""){
                $this->comprobante->setAttribute("CondicionesDePago", $condicionesPago);
            }
        }

        if($tipoComprobante != "T" && $tipoComprobante != "P"){
            $this->comprobante->setAttribute("MetodoPago", $metodoPago);
        }

        if($tipoComprobante == "I" || $tipoComprobante == "E" || $tipoComprobante == "N"){
            if(floatval($descuento) > 0 ){
                $this->comprobante->setAttribute("Descuento", $descuento);
            }
        }

        if($moneda != "MXN" && $moneda != "XXX"){
            $this->comprobante->setAttribute("TipoCambio", $tipoCambio);
        }
        //Termina datos condicionales

        $this->banderaImpuestos = false;
        $this->directorioComprobante = $directorio;
    }

    public function setSello($sello) {
        $this->comprobante->setAttribute("Sello", $sello);
    }

    public function saveCfdi(){
        $this->xml->save($this->directorioComprobante.'generica.xml');
    }
    public function saveCfdiTimbrado($uuid){
        $this->xml->save($this->directorioComprobante.$uuid.'.xml');
    }

    public function setUuidsRelacionados($relaciones) {

        foreach ($relaciones as $relacion) {
            $this->relacionados = $this->xml->createElement("cfdi:CfdiRelacionados");
            $this->relacionados->setAttribute("TipoRelacion", $relacion['relacion']);
            $this->comprobante->appendChild($this->relacionados);

            foreach ($relacion['uuids'] as $valor) {
                $this->relacionado = $this->xml->createElement("cfdi:CfdiRelacionado");
                $this->relacionados->appendChild($this->relacionado);
                $this->relacionado->setAttribute("UUID", $valor["uuid"]);
            }
        }
    }

    public function setEmisor($rfc, $nombre, $regimenFiscal) {
        $this->emisor = $this->xml->createElement("cfdi:Emisor");
        $this->comprobante->appendChild($this->emisor);
        
        //Inicia datos obligatorios
        $this->emisor->setAttribute("Rfc", $rfc);
        $this->emisor->setAttribute("Nombre", $nombre);
        $this->emisor->setAttribute("RegimenFiscal", $regimenFiscal);
    }

    public function setReceptor($rfc, $nombre, $domicilio, $regimenFiscal, $usoCfdi, $residenciaFiscal="", $numRegIdTrib="") {
        $this->receptor = $this->xml->createElement("cfdi:Receptor");
        $this->comprobante->appendChild($this->receptor);
        //Inicia datos obligatorios
        $this->receptor->setAttribute("Rfc", $rfc);
        $this->receptor->setAttribute("Nombre", $nombre);
        $this->receptor->setAttribute("DomicilioFiscalReceptor", $domicilio);
        $this->receptor->setAttribute("RegimenFiscalReceptor",$regimenFiscal);
        $this->receptor->setAttribute("UsoCFDI", $usoCfdi);
        //Termina datos obligatorios

        //Inicia datos condicionales
        if($rfc == "XEXX010101000"){
            $this->receptor->setAttribute("ResidenciaFiscal", $residenciaFiscal);
            $this->receptor->setAttribute("NumRegIdTrib", $numRegIdTrib);
        }
        //Termina datos condicionales
    }

    public function setConceptos($listaConceptos){
        $this->conceptos = $this->xml->createElement("cfdi:Conceptos");
        $this->comprobante->appendChild($this->conceptos);
        
        $this->impuestosTrasladosTemp = [];
        $this->impuestosRetenidosTemp = [];

        foreach ($listaConceptos as $valor) {
            $this->concepto = $this->xml->createElement("cfdi:Concepto");
            $this->conceptos->appendChild($this->concepto);

            
            //Inicia datos requeridos
            $this->concepto->setAttribute("ClaveProdServ", $valor["name_product"]);
            $this->concepto->setAttribute("ClaveUnidad", $valor["unit_value"]);
            $this->concepto->setAttribute("Cantidad", $valor["quantity"]);
            $this->concepto->setAttribute("Descripcion", $valor["description"]);
            $this->concepto->setAttribute("ValorUnitario", $valor["unit_price"]);
            $this->concepto->setAttribute("Importe", $valor["valorUnitario"]);
            $this->concepto->setAttribute("ObjetoImp", $valor["tax_object"]);
            //Termina datos requeridos
            /*
            //Inicia datos condicionales
            if($valor["unidad"] != ""){
                $this->concepto->setAttribute("Unidad", $valor["unidad"]);
            }

            if(floatval($valor["descuento"]) != 0 ){
                $this->concepto->setAttribute("Descuento", $valor["descuento"]);
            }
            //Termina datos condicionales

            //Inicia datos opcionales
            if($valor["noIdentificacion"] != ""){
                $this->concepto->setAttribute("NoIdentificacion", $valor["noIdentificacion"]);
            }
            //Termina datos opcionales
            */
            //Inicia nodo impuestos translados
            if($valor["tax_object"] == "02"){
                $this->nodoImpuestos  = $this->xml->createElement("cfdi:Impuestos");
                $this->concepto->appendChild($this->nodoImpuestos);

                $this->banderaImpuestos = true;

                $this->traslados = $this->xml->createElement("cfdi:Traslados");
                $this->nodoImpuestos->appendChild($this->traslados);
                foreach ($valor["traslados"] as $impuestoConcepto) {

                    $this->totalImpuestosTraslados += floatval($impuestoConcepto["importe"]);

                    $this->traslado = $this->xml->createElement("cfdi:Traslado");
                    $this->traslados->appendChild($this->traslado);

                    $this->traslado->setAttribute("Base", $valor["base"]);
                    $this->traslado->setAttribute("Impuesto", $impuestoConcepto["impuesto"]);
                    $this->traslado->setAttribute("TipoFactor", $impuestoConcepto["tasaOCuota"] === "Exento" ? "Exento" : "Tasa");
                    
                    if($impuestoConcepto["tasaOCuota"] != "Exento"){
                        $this->traslado->setAttribute("TasaOCuota", $impuestoConcepto["tasaOCuota"]);
                        $this->traslado->setAttribute("Importe", $impuestoConcepto["importe"]);
                    }

                    $banderaImpuesto = true;

                    if(count($this->impuestosTrasladosTemp) > 0){
                        $columnaLlave = array_column($this->impuestosTrasladosTemp, "key");
                        $posicion = array_search($impuestoConcepto["impuesto"].$impuestoConcepto["tasaOCuota"], $columnaLlave, true);               
                        if($posicion !== false){
                            $banderaImpuesto = false;
                            $this->impuestosTrasladosTemp[$posicion]["base"] += floatval($valor["base"]);
                            $this->impuestosTrasladosTemp[$posicion]["importe"] += floatval($impuestoConcepto["importe"]);
                        }
                    }

                    if($banderaImpuesto){
                        array_push($this->impuestosTrasladosTemp, array("key" => $impuestoConcepto["impuesto"].$impuestoConcepto["tasaOCuota"], "base"=> floatval($valor["base"]), "impuesto" => $impuestoConcepto["impuesto"], "tipoFactor" => $impuestoConcepto["tasaOCuota"] === "Exento" ? "Exento" : "Tasa", "tasaOCuota" => $impuestoConcepto["tasaOCuota"], "importe" => floatval($impuestoConcepto["importe"])));
                    }
                }
                /*
                //Inicia nodo impuestos retenidos
                if(count($valor["retenciones"]) > 0){ 
                    $this->retenciones = $this->xml->createElement("cfdi:Retenciones");
                    $this->nodoImpuestos->appendChild($this->retenciones);

                    foreach ($valor["retenciones"] as $retencionConcepto) {
                        $this->retencion = $this->xml->createElement("cfdi:Retencion");
                        $this->retenciones->appendChild($this->retencion);

                        $this->totalImpuestosRetenidos += floatval($retencionConcepto["importe"]);

                        $this->retencion->setAttribute("Base", $valor["base"]);
                        $this->retencion->setAttribute("Impuesto", $retencionConcepto["impuesto"]);
                        $this->retencion->setAttribute("TipoFactor", "Tasa");
                        $this->retencion->setAttribute("TasaOCuota", $retencionConcepto["tasaOCuota"]);
                        $this->retencion->setAttribute("Importe", $retencionConcepto["importe"]);

                        $banderaImpuesto = true;

                        if(count($this->impuestosRetenidosTemp) > 0){
                            $columnaLlave = array_column($this->impuestosRetenidosTemp, "key");
                            $posicion = array_search($retencionConcepto["impuesto"].$retencionConcepto["tasaOCuota"], $columnaLlave, true);               
                            if($posicion !== false){
                                $banderaImpuesto = false;
                                $this->impuestosRetenidosTemp[$posicion]["base"] += floatval($valor["base"]);
                                $this->impuestosRetenidosTemp[$posicion]["importe"] += floatval($retencionConcepto["importe"]);
                            }
                        }

                        if($banderaImpuesto){
                            array_push($this->impuestosRetenidosTemp, array("key" => $retencionConcepto["impuesto"].$retencionConcepto["tasaOCuota"], "base"=> floatval($valor["base"]), "tipo" => $retencionConcepto["impuesto"], "tipoFactor" => "Tasa", "tasaOCuota" => $retencionConcepto["tasaOCuota"], "importe" => floatval($retencionConcepto["importe"])));
                        }
                    }
                }
                //Termina nodo impuestos retenidos
                */
            }
            //Termina nodo impuestos translados
            /*
            //Inicia nodo cuentaPredial
            if(count($listaPrediales) > 0){
                foreach ($listaPrediales as $numeroPredial) {
                    $predial = $this->xml->createElement("cfdi:CuentaPredial");
                    $this->concepto->appendChild($predial);
                    $predial->setAttribute("Numero", $numeroPredial);
                }
            }
            
            //Termina nodo cuentaPredial

            */
        }
            
        if($this->banderaImpuestos){
            $this->setImpuestosComprobante();
        }
    }

    public function setImpuestosComprobante(){

        $impuestos = $this->xml->createElement("cfdi:Impuestos");
        $this->comprobante->appendChild($impuestos);

        if($this->totalImpuestosRetenidos > 0){
            $impuestos->setAttribute("TotalImpuestosRetenidos", number_format($this->totalImpuestosRetenidos, 2));
            $retenciones = $this->xml->createElement("cfdi:Retenciones");
            $impuestos->appendChild($retenciones);
            
            foreach($this->impuestosRetenidosTemp as $impuestoRetenido){
                $impuestoRetencion = $this->xml->createElement("cfdi:Retencion");
                $retenciones->appendChild($impuestoRetencion);
                $impuestoRetencion->setAttribute("Impuesto", $impuestoRetenido["impuesto"]);
                $impuestoRetencion->setAttribute("Importe",  number_format($impuestoRetenido['importe'], 2));
            }
        }

        $impuestosTraslados = $this->xml->createElement("cfdi:Traslados");
        $impuestos->appendChild($impuestosTraslados);
        
        if($this->totalImpuestosTraslados > 0){
            $impuestos->setAttribute("TotalImpuestosTrasladados", str_replace(",","", number_format($this->totalImpuestosTraslados, 2)));
        }

        foreach ($this->impuestosTrasladosTemp as $impuestoTemporal) {
            
            $impuestoTraslado = $this->xml->createElement("cfdi:Traslado");
            $impuestosTraslados->appendChild($impuestoTraslado);

            $impuestoTraslado->setAttribute("Base", $impuestoTemporal["base"]);
            $impuestoTraslado->setAttribute("Impuesto", $impuestoTemporal["impuesto"]);
            $impuestoTraslado->setAttribute("TipoFactor", $impuestoTemporal["tipoFactor"]);

            if($impuestoTemporal["tipoFactor"] != "Exento"){
                $impuestoTraslado->setAttribute("TasaOCuota", $impuestoTemporal["tasaOCuota"]); 
                $impuestoTraslado->setAttribute("Importe", str_replace(",","", number_format(floatval($impuestoTemporal["importe"]), 2))); 
            }
            
        }

    }

    public function setComplemento(){
        $this->complemento = $this->xml->createElement("cfdi:Complemento");
        $this->comprobante->appendChild($this->complemento);
    }

    public function setTimbreFiscal($uuid, $fechaTimbrado, $rfcProveedor, $selloCfdi, $noCertificadoDoc, $selloSat){
        $this->timbre = $this->xml->createElement("tfd:TimbreFiscalDigital");
        $this->complemento->appendChild($this->timbre);

        $this->timbre->setAttribute("xmlns:tfd", "http://www.sat.gob.mx/TimbreFiscalDigital");
        $this->timbre->setAttribute("xsi:schemaLocation", "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd");
        $this->timbre->setAttribute("Version", "1.1");
        $this->timbre->setAttribute("UUID", $uuid);
        $this->timbre->setAttribute("FechaTimbrado",$fechaTimbrado);
        $this->timbre->setAttribute("RfcProvCertif", $rfcProveedor);
        $this->timbre->setAttribute("SelloCFD", $selloCfdi);
        $this->timbre->setAttribute("NoCertificadoSAT", $noCertificadoDoc);
        $this->timbre->setAttribute("SelloSAT", $selloSat);
    }

    public function setPago20(){
        //Cabecera
        $this->comprobante->setAttribute("xmlns:pago20","http://www.sat.gob.mx/Pagos20");
        $this->setComplemento();
        //Nodo Pagos
        $this->pago20 = $this->xml->createElement("pago20:Pagos");
        $this->complemento->appendChild($this->pago20);
        $this->pago20->setAttribute("Version","2.0");


        $totales20Pago = $this->xml->createElement("pago20:Totales");
        $this->pago20->appendChild($totales20Pago);

        //Inicia datos opcionales
        $totales20Pago->setAttribute("TotalRetencionesIVA", "");
        $totales20Pago->setAttribute("TotalRetencionesISR", "");
        $totales20Pago->setAttribute("TotalRetencionesIEPS", "");
        $totales20Pago->setAttribute("TotalTrasladosBaseIVA16", "");
        $totales20Pago->setAttribute("TotalTrasladosImpuestoIVA16", "");
        $totales20Pago->setAttribute("TotalTrasladosBaseIVA8", "");
        $totales20Pago->setAttribute("TotalTrasladosImpuestoIVA8", "");
        $totales20Pago->setAttribute("TotalTrasladosBaseIVA0", "");
        $totales20Pago->setAttribute("TotalTrasladosImpuestoIVA0", "");
        $totales20Pago->setAttribute("TotalTrasladosBaseIVAExento", "");

        //Termina datos opcionales

        //Inicia datos Requeridos
        $totales20Pago->setAttribute("MontoTotalPagos", "");

        
        // foreach ($listaPagos as $valor){
        $pago20Pago = $this->xml->createElement("pago20:Pago");
        $this->pago20->appendChild($pago20Pago);

        //Inicia datos requeridos
        $pago20Pago->setAttribute("FechaPago", "");
        $pago20Pago->setAttribute("FormaDePagoP", "");
        $pago20Pago->setAttribute("MonedaP", "");
        $pago20Pago->setAttribute("Monto", "");
        //Termina datos requeridos

        //Inicia datos condicionales
        $pago20Pago->setAttribute("TipoCambioP", "");
        $pago20Pago->setAttribute("NumOperacion", "");
        $pago20Pago->setAttribute("RfcEmisorCtaOrd", "");
        $pago20Pago->setAttribute("NomBancoOrdExt", "");
        $pago20Pago->setAttribute("CtaOrdenante", "");
        $pago20Pago->setAttribute("RfcEmisorCtaBen", "");
        $pago20Pago->setAttribute("CtaBeneficiario", "");
        $pago20Pago->setAttribute("TipoCadPago", "");
        $pago20Pago->setAttribute("CertPago", "");
        $pago20Pago->setAttribute("CadPago", "");
        $pago20Pago->setAttribute("SelloPago", "");
        //Termina datos condicionales
        

        $pago20Relacionado = $this->xml->createElement("pago20:DoctoRelacionado");
        $pago20Pago->appendChild($pago20Relacionado);

        //Inicia datos requeridos
        $pago20Relacionado->setAttribute("IdDocumento", "");
        $pago20Relacionado->setAttribute("MonedaDR", "");
        $pago20Relacionado->setAttribute("NumParcialidad", "");
        $pago20Relacionado->setAttribute("ImpSaldoAnt", "");
        $pago20Relacionado->setAttribute("ImpPagado", "");
        $pago20Relacionado->setAttribute("ImpSaldoInsoluto", "");
        $pago20Relacionado->setAttribute("ObjetoImpDR", "");
        
        //Termina datos requeridos

        //Inicia datos condicionales
        $pago20Relacionado->setAttribute("EquivalenciaDR", "");
        //Termina datos condicionales
        
        //Inicia datos opcionales
        $pago20Relacionado->setAttribute("Serie", "");
        $pago20Relacionado->setAttribute("Folio", "");
        //Termina datos opcionales

        $impuestos = $this->xml->createElement("pago20:ImpuestosDR");
        $pago20Relacionado->appendChild($impuestos);
        
        $retenciones = $this->xml->createElement("pago20:RetencionesDR");
        $impuestos->appendChild($retenciones);
        
        $retencion = $this->xml->createElement("pago20:RetencionDR");
        $retenciones->appendChild($retencion);

        //Inicia datos requeridos
        $retencion->setAttribute("BaseDR", "");
        $retencion->setAttribute("ImpuestoDR", "");
        $retencion->setAttribute("TipoFactorDR", "");
        $retencion->setAttribute("TasaOCuotaDR", "");
        $retencion->setAttribute("ImporteDR", "");
        //Termina datos requeridos
        
        $traslados = $this->xml->createElement("pago20:TrasladosDR");
        $impuestos->appendChild($traslados);
        
        $traslado = $this->xml->createElement("pago20:TrasladoDR");
        $traslados->appendChild($traslado);

        //Inicia datos requeridos
        $traslado->setAttribute("BaseDR", "");
        $traslado->setAttribute("ImpuestoDR", "");
        $traslado->setAttribute("TipoFactorDR", "");
        //Termina datos requeridos

        //Inicia datos condicionales
        $traslado->setAttribute("TasaOCuotaDR", "");
        $traslado->setAttribute("ImporteDR", "");


        $impuestosP = $this->xml->createElement("pago20:ImpuestosP");
        $this->pago20->appendChild($impuestosP);

        
        $retencionesP = $this->xml->createElement("pago20:RetencionesP");
        $impuestosP->appendChild($retencionesP);

        $retencionP = $this->xml->createElement("pago20:RetencionP");
        $retencionesP->appendChild($retencionP);
        $retencionP->setAttribute("ImpuestoP", "");
        $retencionP->setAttribute("ImporteP", "");

        $trasladosP = $this->xml->createElement("pago20:TrasladosP");
        $impuestosP->appendChild($trasladosP);

        $trasladoP = $this->xml->createElement("pago20:TrasladoP");
        $trasladosP->appendChild($trasladoP);
        $trasladoP->setAttribute("BaseP", "");
        $trasladoP->setAttribute("ImpuestoP", "");
        $trasladoP->setAttribute("TipoFactorP", "");
        $trasladoP->setAttribute("TasaOCuotaP", "");
        $trasladoP->setAttribute("ImporteP", "");
        
        // }
    }


    public function setCartaPorte31(){
        $this->setComplemento();

        $this->cartaPorte31 = $this->xml->createElement("cartaporte31:CartaPorte");
        $this->complemento->appendChild($this->cartaPorte31);
        $this->cartaPorte31->setAttribute("Version", "3.1");
        $this->cartaPorte31->setAttribute("IdCCP", $this->getIdCCP());

        $this->cartaPorte31->setAttribute("TotalDistRec", "");
        $this->cartaPorte31->setAttribute("TranspInternac", "");
        
        $registroIstmo = "";

        if($registroIstmo == 'Sí'){
            $this->cartaPorte31->setAttribute("RegistroISTMO", $registroIstmo);
            $this->cartaPorte31->setAttribute("UbicacionPoloOrigen", $ubicacionPoloOrigen);
            $this->cartaPorte31->setAttribute("UbicacionPoloDestino", $ubicacionPoloDestino);
        }

        $this->setcartaPorte31Ubicaciones();
        $this->setCartaPorte31Mercancias();
        $this->setCartaPorte31AutoTransporte();

    }


    public function setcartaPorte31Ubicaciones(){
        $cartaPorte31Ubicaciones = $this->xml->createElement("cartaporte31:Ubicaciones");
        $this->cartaPorte31->appendChild($cartaPorte31Ubicaciones);

        $listaUbicaciones = [];
        foreach ($listaUbicaciones as $key => $ubicacion) {

            $cartaPorte31Ubicacion = $this->xml->createElement("cartaporte31:Ubicacion");
            $cartaPorte31Ubicaciones->appendChild($cartaPorte31Ubicacion);

            //Inicia datos requeridos
            $cartaPorte31Ubicacion->setAttribute("TipoUbicacion", $ubicacion["tipoUbicacion"]);
            $cartaPorte31Ubicacion->setAttribute("RFCRemitenteDestinatario", $ubicacion["rfcRemitenteDestinatario"]);
            $cartaPorte31Ubicacion->setAttribute("FechaHoraSalidaLlegada", $ubicacion["fechaHoraSalidaLlegada"].":00");
            //Termina datos requeridos

            //Inicia datos opcionales
            if($ubicacion["nombreRemitenteDestinatario"] != ""){
                $cartaPorte31Ubicacion->setAttribute("NombreRemitenteDestinatario", $ubicacion["nombreRemitenteDestinatario"]);
            }
            //Termina datos opcionales

            //Inicia datos condicionales
            if($ubicacion["idUbicacion"] != ""){
                $cartaPorte31Ubicacion->setAttribute("IDUbicacion", $ubicacion["idUbicacion"]);
            }

            if($ubicacion["rfcRemitenteDestinatario"] == 'XEXX010101000'){
                $cartaPorte31Ubicacion->setAttribute("NumRegIdTrib", $ubicacion["numRegIdTrib"]);
                $cartaPorte31Ubicacion->setAttribute("ResidenciaFiscal", $ubicacion["residenciaFiscal"]);
            }

            if($ubicacion["tipoUbicacion"] == "Destino"){
                $cartaPorte31Ubicacion->setAttribute("DistanciaRecorrida", $ubicacion["distanciaRecorrida"]);
            }
            //Termina datos condicionales

            $cartaPorte31UbicacionDomicilio = $this->xml->createElement("cartaporte31:Domicilio");
            $cartaPorte31Ubicacion->appendChild($cartaPorte31UbicacionDomicilio);

            //Inicia datos requeridos
            $cartaPorte31UbicacionDomicilio->setAttribute("Pais", $ubicacion["pais"]);
            $cartaPorte31UbicacionDomicilio->setAttribute("CodigoPostal", $ubicacion["codigoPostal"]);
            $cartaPorte31UbicacionDomicilio->setAttribute("Estado", $ubicacion["estado"]);

            //Inicia datos opcionales
            if($ubicacion["calle"] != ""){
                $this->cartaPorteUbicacionDomicilio->setAttribute("Calle", $ubicacion["calle"]);
            }
            if($ubicacion["numeroExterior"] != ""){
                $this->cartaPorteUbicacionDomicilio->setAttribute("NumeroExterior", $ubicacion["numeroExterior"]);
            }
            if($ubicacion["numeroInterior"] != ""){
                $this->cartaPorteUbicacionDomicilio->setAttribute("NumeroInterior", $ubicacion["numeroInterior"]);
            }
            if($ubicacion["colonia"] != ""){
                $this->cartaPorteUbicacionDomicilio->setAttribute("Colonia", $ubicacion["colonia"]);
            }
            if($ubicacion["localidad"] != ""){
                $this->cartaPorteUbicacionDomicilio->setAttribute("Localidad", $ubicacion["localidad"]);
            }
            if($ubicacion["referencia"] != ""){
                $this->cartaPorteUbicacionDomicilio->setAttribute("Referencia", $ubicacion["referencia"]);
            }
            if($ubicacion["municipio"] != ""){
                $this->cartaPorteUbicacionDomicilio->setAttribute("Municipio", $ubicacion["municipio"]);
            }
            //Termina datos opcionales
        }
    }

    public function setCartaPorte31Mercancias(){
        $this->mercancias = $this->xml->createElement("cartaporte31:Mercancias");
        $this->cartaPorte31->appendChild($this->mercancias);

        //Inicia datos requeridos
        $this->mercancias->setAttribute("PesoBrutoTotal", "");
        $this->mercancias->setAttribute("UnidadPeso", "");
        $this->mercancias->setAttribute("NumTotalMercancias", "");

        //Inicia datos condicionales
        $logisticaInversaRecoleccionDevolucion = "";
        if($logisticaInversaRecoleccionDevolucion == "Sí"){
            $this->mercancias->setAttribute("LogisticaInversaRecoleccionDevolucion", $logisticaInversaRecoleccionDevolucion);
        }
        //Termina datos condicionales

        $listaMercancias = [];
        foreach ($listaMercancias as $key => $mercancia) {
            $mercancia = $this->xml->createElement("cartaporte31:Mercancia");
            $this->mercancias->appendChild($mercancia);

            //Inicia datos requeridos
            $mercancia->setAttribute("BienesTransp", $mercancia["bienesTransp"]);
            $mercancia->setAttribute("Descripcion", $mercancia["descripcion"]);
            $mercancia->setAttribute("Cantidad", $mercancia["cantidad"]);
            $mercancia->setAttribute("ClaveUnidad", $mercancia["claveUnidad"]);
            //Termina datos requeridos


            //Inicia datos opcionales
            $mercancia->setAttribute("Unidad", $mercancia["unidad"]);
            //Termina datos opcionales


            //Inicia datos condicionales
            if($mercancia["dimensiones"] != ""){
                $mercancia->setAttribute("Dimensiones", $mercancia["dimensiones"]);
            }
            if($mercancia["materialPeligroso"] == "1" || $mercancia["materialPeligroso"] == "0,1"){
                $mercancia->setAttribute("MaterialPeligroso", $mercancia["materialPeligroso"]);
                if($mercancia["cveMaterialPeligroso"] != ""){
                    $mercancia->setAttribute("CveMaterialPeligroso", $mercancia["cveMaterialPeligroso"]);
                    $mercancia->setAttribute("Embalaje", $mercancia["embalaje"]);
                    $mercancia->setAttribute("DescripEmbalaje", $mercancia["descripEmbalaje"]);
                }
                
            }
            //Termina datos condicionales
        }
    }

    public function setCartaPorte31AutoTransporte(){
        $autoTransporte = $this->xml->createElement("cartaporte31:Autotransporte");
        $this->mercancias->appendChild($autoTransporte);
        
        //Inicia datos requeridos
        $autoTransporte->setAttribute("PermSCT", "");
        $autoTransporte->setAttribute("NumPermisoSCT", "");
        //Termina datos requeridos

        $identificacionVehicular = $this->xml->createElement("cartaporte31:IdentificacionVehicular");
        $autoTransporte->appendChild($identificacionVehicular);

        //Inicia datos requeridos
        $identificacionVehicular->setAttribute("ConfigVehicular", "");
        $identificacionVehicular->setAttribute("PesoBrutoVehicular", "");
        $identificacionVehicular->setAttribute("PlacaVM", "");
        $identificacionVehicular->setAttribute("AnioModeloVM", "");
        //Termina datos requeridos

        $seguros = $this->xml->createElement("cartaporte31:Seguros");
        $autoTransporte->appendChild($seguros);
        //Inicia datos requeridos
        $seguros->setAttribute("AseguraRespCivil", "");
        $seguros->setAttribute("PolizaRespCivil", "");
        //Termina datos requeridos

        //Inicia datos condicionales
        $aseguraMedAmbiente = "";
        if($aseguraMedAmbiente != ""){
            $seguros->setAttribute("AseguraMedAmbiente", "");
            $seguros->setAttribute("PolizaMedAmbiente", "");
        }

        $aseguraCarga = "";
        if($aseguraCarga != ""){
            $seguros->setAttribute("AseguraCarga", "");
            $seguros->setAttribute("PolizaCarga", "");
        }

        $primaSeguro = "";
        if($primaSeguro != ""){
            $seguros->setAttribute("PrimaSeguro", $primaSeguro);
        }

        //Termina datos condicionales

        //Inicia datos opcionales

        //Termina datos opcionales


    }


    public function getIdCCP(){
        return "CCC".substr(Uuid::uuid4(), 3);
    }



}
