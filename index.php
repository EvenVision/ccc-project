<?php
$xmlStr = file_get_contents('response.xml');
$xml = new SimpleXMLElement($xmlStr);
$res = $xml->xpath('/VehicleDamageEstimateAddRq/DocumentInfo/DocumentID');
print_r($res);
