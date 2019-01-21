<?php

require 'lib/nusoap.php';
$client = new nusoap_client('http://www.banguat.gob.gt/variables/ws/TipoCambio.asmx?WSDL', true);
$error = $client->getError();
if($error){
    echo $error;
}else{
    $result = $client->call('TipoCambioDia');

 //esto va a traer el tipo de cambio dentro de los array 
$Cambio = $result['TipoCambioDiaResult']['CambioDolar']['VarDolar']['referencia'];
$TipoCambio = substr($Cambio, 0, 4);


function bloque_tipo_de_cambio(){
//contenido a mostrar 
	global $TipoCambio;
	$htmlStart = '<span style="font-size: 13px; color: #264988; font-weight: 500;">';
	$htmlEnd = '</span>';
	$title = 'Tipo de cambio ';
	$dolar = '1.00 USD = ';
	$qtz = ' QTZ';
	$bloque_de_texto = $title.$dolar.$TipoCambio.$qtz;
	return  $bloque_de_texto;
}
	add_shortcode('tipo_de_cambio_header', 'bloque_tipo_de_cambio');
}
?>