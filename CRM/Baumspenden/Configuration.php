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

class CRM_Baumspenden_Configuration
{
    /**
     * TODO: Set to actual payment instrument ID
     */
    public const PAYMENT_INSTRUMENT_ID_PAYMENT_REQUEST = 0;

    /**
     * TODO: Set to actual payment instrument ID
     */
    public const PAYMENT_INSTRUMENT_ID_PAYPAL = 0;

    /**
     * TODO: Set to actual payment instrument ID
     */
    public const PAYMENT_INSTRUMENT_ID_CREDIT_CARD = 0;

    public const XCM_PROFILE = 'baumspenden';

    public const FINANCIAL_TYPE_NAME = 'Baumspende';

    public const CONTRIBUTION_SOURCE = 'Formular Baumspende';

    public const ACTIVITY_SUBJECT_PRESENT = 'Schenkung Baumspende';

    public const ACTIVITY_SUBJECT_FAILED = 'Fehlgeschlagene Baumspende';

    /**
     * TODO: Set to actual message template ID.
     */
    public const CERTIFICATE_MESSAGE_TEMPLATE_ID = 78;

    public const GROUP_ID_NEWSLETTER = 19;
}
