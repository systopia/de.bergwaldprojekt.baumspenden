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
 * BWPBaumspende.send_certificate API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_b_w_p_baumspende_send_certificate_spec(&$spec)
{
    $spec['contribution_id'] = [
        'name' => 'contribution_id',
        'title' => 'Contribution ID',
        'type' => CRM_Utils_Type::T_INT,
        'api.required' => 1,
        'description' => 'The CiviCRM ID of the contribution to generate a certificate for.',
    ];
    $spec['mode'] = [
        'name' => 'mode',
        'title' => 'Certificate mode',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'api.default' => 'email',
        'description' => 'The mode of delivery for the certificate. One of "digital" or "postal".',
    ];
}

/**
 * BWPBaumspende.send_certificate API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 */
function civicrm_api3_b_w_p_baumspende_send_certificate($params)
{
    try {
        $certificate = new CRM_Baumspenden_Certificate(
            $params['contribution_id'],
            $params['mode']
        );

        $certificate->send();

        return civicrm_api3_create_success($certificate_file);
    } catch (Exception $exception) {
        return civicrm_api3_create_error($exception->getMessage());
    }
}
