<?php

namespace Drupal\estimate;

class EstimateParseXML {

  public function parseXML($postdata){
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

    // More fields
    if($xml->AdminInfo && $xml->AdminInfo->RepairFacility && $xml->AdminInfo->RepairFacility->Party->OrgInfo){
      $other_info = $xml->AdminInfo->RepairFacility->Party->OrgInfo;
      $result['CompanyName'] = $this->searchFieldXML($other_info,'CompanyName');
    }

    if($xml->EventInfo && $xml->EventInfo->RepairEvent){
      $other_info = $xml->EventInfo->RepairEvent;
      $result['ActualPickUpDateTime'] = $this->searchFieldXML($other_info,'ActualPickUpDateTime');
      $result['ROClosed'] = !empty($result['ActualPickUpDateTime']);
    }

    // Field Group: Insurance
    if($xml->AdminInfo && $xml->AdminInfo->InsuranceCompany && $xml->AdminInfo->InsuranceCompany->Party->OrgInfo) {
      $other_info = $xml->AdminInfo->InsuranceCompany->Party->OrgInfo;
      $result['InsuranceCompany'] = $this->searchFieldXML($other_info, 'CompanyName');
      $result['InsuranceCompanyID'] = $this->searchFieldXML($other_info->IDInfo,'IDNum');

    }
    // Field Group: Estimator
    if($xml->EstimatorIDs && $xml->EstimatorIDs->CurrentEstimatorID){
      $other_info = $xml->EstimatorIDs;
      $result['EstimatorName'] = $this->searchFieldXML($other_info, 'CurrentEstimatorID');
    }

    if($xml->AdminInfo && $xml->AdminInfo->Estimator && $xml->AdminInfo->Estimator->Party->PersonInfo->PersonName && $xml->AdminInfo->Estimator->Party->PersonInfo->IDInfo){
      $other_info = $xml->AdminInfo->Estimator->Party->PersonInfo;
      $result['EstimatorFirstName'] = $this->searchFieldXML($other_info->PersonName, 'FirstName');
      $result['EstimatorLastName'] = $this->searchFieldXML($other_info->PersonName, 'LastName');
      $result['EstimatorID'] = $this->searchFieldXML($other_info->IDInfo,'IDNum');
    }

    // Field Group: Cycle Time
    if($xml->EventInfo && $xml->EventInfo->RepairEvent){
      $other_info = $xml->EventInfo->RepairEvent;
      $result['ArrivalDateTime'] = $this->searchFieldXML($other_info,'ArrivalDateTime');
    }

    // Field Group: Sales Parts
    if($xml->RepairTotalsInfo && $xml->RepairTotalsInfo->PartsTotalsInfo){
      $other_info = $xml->RepairTotalsInfo->PartsTotalsInfo;
      foreach ($other_info as $info){
        switch ($info->TotalType) {
          case "PAA":
            $result['PAA_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "PAG":
            $result['PAG_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "PAL":
            $result['PAL_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "PAM":
            $result['PAM_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "PAN":
            $result['PAN_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "PAND":
            $result['PAND_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "PAO":
            $result['PAO_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "PAP":
            $result['PAP_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "PAR":
            $result['PAR_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "PAS":
            $result['PAS_TotalAmt'] = (float) $info->TotalAmt;
            break;
        }
      }
    }

    // Field Group: Sales Other
    if($xml->RepairTotalsInfo && $xml->RepairTotalsInfo->OtherChargesTotalsInfo){
      $other_info = $xml->RepairTotalsInfo->OtherChargesTotalsInfo;
      foreach ($other_info as $info){
        switch ($info->TotalType) {
          case "MAHW":
            $result['MAHW_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "MAPA":
            $result['MAPA_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "MASH":
            $result['MASH_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "OT1":
            $result['OT1_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "OT2":
            $result['OT2_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "OT3":
            $result['OT3_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "OT4":
            $result['OT4_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "OTST":
            $result['OTST_TotalAmt'] = (float) $info->TotalAmt;
            break;
          case "OTTW":
            $result['OTTW_TotalAmt'] = (float) $info->TotalAmt;
            break;
        }
      }
    }

    return $result;
  }

  protected function searchFieldXML($other_info, $infoName)
  {
    $result = "";
    $data = (array)$other_info;

    if (isset($data[$infoName])) {
      $result = (string)$data[$infoName];
    }

    return $result;
  }
}


