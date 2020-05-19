<?php

use CRM_Baumspenden_ExtensionUtil as E;

/**
 * BWPBaumspende.generate_certificate API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_b_w_p_baumspende_generate_certificate_spec(&$spec)
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
        'api.default' => 'digital',
        'description' => 'The mode of delivery for the certificate. One of "digital" or "postal".',
    ];
    $spec['download'] = [
        'name' => 'download',
        'title' => 'Download the file',
        'type' => CRM_Utils_Type::T_BOOLEAN,
        'api.required' => 0,
        'api.default' => true,
        'description' => 'Whether to download the file. If set to false, the generated file contents will be returned.',
    ];
}

/**
 * BWPBaumspende.generate_certificate API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @throws API_Exception
 * @see civicrm_api3_create_success
 *
 */
function civicrm_api3_b_w_p_baumspende_generate_certificate($params)
{
    try {
        $certificate = new CRM_Baumspenden_Certificate(
            $params['contribution_id'],
            $params['mode']
        );

        $certificate->render();
        $certificate_file = $certificate->convertToPDF($params['download']);

        return civicrm_api3_create_success($certificate_file);
    } catch (Exception $exception) {
        return civicrm_api3_create_error($exception->getMessage());
    }
}
