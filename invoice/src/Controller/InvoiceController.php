<?php

/**
 * @file
 * Provides basic hello world message functionality.
 */

namespace Drupal\invoice\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class InvoiceController.
 *
 * @package Drupal\invoice\Controller
 */
class InvoiceController extends ControllerBase {

  /**
   * Say Hello.
   *
   * @return array
   *   Markup.
   */
  public function hello() {
    return ['#markup' => $this->t("Invoice")];
  }

}
