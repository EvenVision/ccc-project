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

    // get data
    $postdata = file_get_contents("php://input");

    // parse xml
    $message = 'Ok';
    if (!empty($postdata)){
      try {
        $result = $this->parseXML($postdata);
        $node = Node::create([
          'type'                  => 'invoice',
          'title'                 => 'Invoice',
          'field_estimatealtid'   => ['value' => $result['EstimateAltID']],
          'field_totalamt'        => ['value' => $result['TotalAmt1']],
          'field_totalamt_body'   => ['value' => $result['TotalAmt2']],
          'field_totalhours_body' => ['value' => $result['TotalHours']],
        ]);

        $node->save();
        // save to xml
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($postdata);
        $time = time();
        $path = realpath(__DIR__.'/../../');
        $domxml->save($path."/xml/estimate{$time}.xml");
        $message = $this->t($message);
      } catch (\Throwable $e){
        \Drupal::logger('php')->error('Invoice Error: ' . $e->getMessage());
        $message = $e->getMessage();
      }
    } else {
      $message = "Empty data";
    }

    return ['#markup' => $message];
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
