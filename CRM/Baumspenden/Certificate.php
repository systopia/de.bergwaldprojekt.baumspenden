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

class CRM_Baumspenden_Certificate
{
    /**
     * @var int $contact_id
     *   The ID of the CiviCRM Contact this certificate is issued for.
     */
    protected $contact_id;

    /**
     * @var int $amount
     *   The amount of trees.
     */
    protected $amount;

    /**
     * @var string $mode
     *   Either "digital" or "postal".
     */
    protected $mode;

    /**
     * @var string $name
     *   The name to appear on the certificate.
     */
    protected $name;

    /**
     * CRM_Baumspenden_Certificate constructor.
     *
     * @param int $contact_id
     * @param int $amount
     * @param string $mode
     * @param string $name
     */
    public function __construct(
        $contact_id,
        $amount = 1,
        $mode = "digital",
        $name = null
    ) {
        // Check if contact exists.
        $contact = civicrm_api3(
            'Contact',
            'getsingle',
            [
                'id' => $contact_id,
                'return' => ['display_name'],
            ]
        );
        $this->contact_id = $contact['id'];

        // Validate and set mode.
        if (!in_array($mode, ['digital', 'postal'])) {
            throw new Exception(
                E::ts('Invalid certificate mode %1'),
                [1 => $mode]
            );
        }
        $this->mode = $mode;

        // Set name to the contact's display name if not set.
        $this->name = (isset($name) ? $name : $contact['display_name']);
    }
}
