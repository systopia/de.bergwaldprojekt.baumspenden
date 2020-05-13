<?php
/*-------------------------------------------------------+
| Bergwaldprojekt Baumspenden                            |
| Copyright (C) 2020 SYSTOPIA                            |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

use CRM_Baumspenden_ExtensionUtil as E;

/**
 * Class CRM_Baumspenden_Submission
 */
class CRM_Baumspenden_Submission {

  // TODO: Initialize with correct payment_instrument_id values.
  public const PAYMENT_INSTRUMENT_ID_PAYMENT_REQUEST = 1;
  public const PAYMENT_INSTRUMENT_ID_PAYPAL = 1;
  public const PAYMENT_INSTRUMENT_ID_CREDIT_CARD = 1;

}
