<?php

$dir = 'files';
$files = scandir($dir);
unset($files[0], $files[1]); // remove . and ..

$parser = xml_parser_create();
$result = [];

foreach ($files as $file){
    $xml = simplexml_load_file($dir . '/' .$file, "SimpleXMLElement", LIBXML_NOCDATA);
    //$xml = simplexml_load_string($XML_STR, "SimpleXMLElement", LIBXML_NOCDATA);

    if($xml->DocumentInfo && $xml->DocumentInfo->ReferenceInfo && $xml->DocumentInfo->ReferenceInfo->OtherReferenceInfo){
        $other_info = $xml->DocumentInfo->ReferenceInfo->OtherReferenceInfo;
        foreach ($other_info as $info){
            if($info->OtherReferenceName == 'EstimateAltID'){
                $result['EstimateAltID'] = (string) $info->OtherRefNum;
                break;
            }
        }
    }

    if($xml->RepairTotalsInfo && $xml->RepairTotalsInfo->SummaryTotalsInfo){
        $other_info = $xml->RepairTotalsInfo->SummaryTotalsInfo;
        foreach ($other_info as $info){
            if($info->TotalSubType == 'T2'){
                $result['TotalAmt1'] = (float) $info->TotalAmt;
                break;
            }
        }
    }

    if($xml->RepairTotalsInfo && $xml->RepairTotalsInfo->LaborTotalsInfo){
        $other_info = $xml->RepairTotalsInfo->LaborTotalsInfo;
        foreach ($other_info as $info){
            if($info->TotalType == 'LAB'){
                $result['TotalHours'] = (float) $info->TotalHours;
                $result['TotalAmt2'] = (float) $info->TotalAmt;
                break;
            }
        }
    }
    echo '<pre>'.
    print_r($result, 1)
    .'</pre></br>';
}