<?php

/**
 * @file
 * Provides basic hello world message functionality.
 */

namespace Drupal\invoice\Controller;

use \Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class InvoiceController.
 *
 * @package Drupal\invoice\Controller
 */
class InvoiceController extends ControllerBase {

  /**
   * webhook.
   *
   * @return array
   *   Markup.
   */
  public function webhook() {

    // get header
    header('X-SecureShare-Signature: *');

    if (!function_exists('getallheaders')) {
      $this->getallheaders();
    }

    // get data
    //$postdata = file_get_contents('response.xml');
    $postdata = file_get_contents("php://input");

    // parse xml
    if (!empty($postdata)){
      $result = $this->parseXML($postdata);
      $node = Node::create([
        'type'        => 'invoice',
        'title'       => 'Invoice',
        'field_estimatealtid' => ['value' => $result['EstimateAltID']],
        'field_totalamt' => ['value' => $result['TotalAmt1']],
        'field_totalamt_body' => ['value' => $result['TotalAmt2']],
        'field_totalhours_body' => ['value' => $result['TotalHours']],
      ]);

      $node->save();

    }

    // save to xml
    $domxml = new \DOMDocument('1.0');
    $domxml->preserveWhiteSpace = false;
    $domxml->formatOutput = true;
    $domxml->loadXML($postdata);
    $time = time();
    $domxml->save("modules/custom/invoice/xml/estimate{$time}.xml");

    return ['#markup' => $this->t("Invoice")];
  }

  protected function getallheaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
      if (substr($name, 0, 5) == 'HTTP_') {
        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
      }
    }
    return $headers;
  }

  protected function parseXML($postdata){
    $result = [];
    $xml = new \SimpleXMLElement($postdata);
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
    return $result;
  }

}
