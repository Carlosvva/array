
<?php
// nusoap-0.9.5
require 'lib/nusoap.php';
$client = new nusoap_client('http://www.banguat.gob.gt/variables/ws/TipoCambio.asmx?WSDL', true);
$error = $client->getError();
if($error){
    echo $error;
}else{
    $result = $client->call('TipoCambioDia');
echo($result['TipoCambioDiaResult']['CambioDolar']['VarDolar']['referencia']);
}

