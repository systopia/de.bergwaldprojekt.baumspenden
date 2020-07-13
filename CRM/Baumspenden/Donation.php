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

class CRM_Baumspenden_Donation
{

    /**
     * @var array $contribution
     *   The CiviCRM contribution.
     */
    protected $contribution;

    /**
     * CRM_Baumspenden_Donation constructor.
     *
     * @param int $contribution_id
     *
     * @throws \CiviCRM_API3_Exception
     */
    public function __construct($contribution_id = null)
    {
        if (!empty($contribution_id)) {
            $financial_type = civicrm_api3(
                'FinancialType',
                'getsingle',
                ['name' => CRM_Baumspenden_Configuration::FINANCIAL_TYPE_NAME]
            );
            $contribution = civicrm_api3(
                'Contribution',
                'getsingle',
                [
                    'id' => $contribution_id,
                    'financial_type_id' => $financial_type['id'],
                ]
            );
            $this->contribution = $contribution;
        }
    }

    /**
     * Returns the contribution array.
     *
     * @return array
     *   The CiviCRM contribution.
     */
    public function getContribution()
    {
        return $this->contribution;
    }

    /**
     * Returns the value of the contribution's property with the given name.
     * This may be any property natively available on contributions or custom
     * field names in the custom group "baumspende", which will be resolved to
     * the "custom_X" notation,
     *
     * @param string $property
     *   The property name.
     *
     * @return mixed
     *   The property value.
     *
     * @throws \Exception
     *   When the property could not be retrieved from the contribution.
     */
    public function get($property)
    {
        if (array_key_exists($property, $this->contribution)) {
            $value = $this->contribution[$property];
        } elseif (array_key_exists(
            $custom_field_key = CRM_Baumspenden_CustomData::getCustomFieldKey(
                'baumspende',
                'baumspende_' . $property
            ),
            $this->contribution
        )) {
            $value = $this->contribution[$custom_field_key];
        } else {
            throw new Exception('Property ' . $property . ' not found.');
        }

        return $value;
    }

    /**
     * Validates API parameters used for creating Baumspende contributions.
     *
     * @param $params
     *   API parameters as defined for the BWPBaumspende.Submit API
     *
     * @throws Exception
     *   When validating API parameters fails.
     */
    public static function validate(&$params) {
        // Prepare parameters.
        if (!empty($params['as_present']) && is_array(
                $params['as_present']
            )) {
            $params['as_present'] = reset($params['as_present']);
        }
        if (!empty($params['newsletter']) && is_array(
                $params['newsletter']
            )) {
            $params['newsletter'] = reset($params['newsletter']);
        }

        if (!empty($params['as_present'])) {
            if (
                empty($params['presentee_first_name'])
                || empty($params['presentee_last_name'])
            ) {
                throw new Exception(
                    E::ts(
                        'Missing mandatory parameter(s): one of presentee_first_name, presentee_last_name'
                    )
                );
            }
        }

        // Require e-mail address(es) when shipping_mode is "email"
        if ($params['shipping_mode'] == 'email') {
            if (
                !empty($params['as_present'])
                && empty($params['presentee_email'])
            ) {
                throw new Exception(
                    E::ts('Mandatory parameter missing: presentee_email')
                );
            }
        }

        // Require postal address(es) when shipping_mode is "postal"
        if ($params['shipping_mode'] == 'postal') {
            if (!empty($params['as_present'])) {
                if (
                    empty($params['presentee_street_address'])
                    || empty($params['presentee_postal_code'])
                    || empty($params['presentee_city'])
                ) {
                    throw new Exception(
                        E::ts(
                            'Missing mandatory parameter(s): one of presentee_street_adress, presentee_postal_code, presentee_city'
                        )
                    );
                }
            } elseif (
                empty($params['street_address'])
                || empty($params['postal_code'])
                || empty($params['city'])
            ) {
                throw new Exception(
                    E::ts(
                        'Missing mandatory parameter(s): one of street_adress, postal_code, city'
                    )
                );
            }
        }
    }

    /**
     * Creates a new Baumspende contribution from API parameters.
     *
     * @param $params
     *   API parameters as defined for the BWPBaumspende.Submit API
     *
     * @return self
     *
     * @throws Exception
     * @see _civicrm_api3_b_w_p_baumspende_submit_spec()
     *
     */
    public static function create($params)
    {
        $donation = new self();
        try {
            // Prepare and validate parameters.
            self::validate($params);

            // Retrieve initiator and presentee contacts.
            $initiator_contact_id = self::retrieveContact($params);
            if (!empty($params['as_present'])) {
                $presentee_contact_id = self::retrieveContact(
                    [
                        'first_name' => $params['presentee_first_name'],
                        'last_name' => $params['presentee_last_name'],
                        'email' => (!empty($params['presentee_email']) ? $params['presentee_email'] : null),
                        'street_address' => (!empty($params['presentee_street_address']) ? $params['presentee_street_address'] : null),
                        'supplemental_address_1' => (!empty($params['presentee_supplemental_address_1']) ? $params['presentee_supplemental_address_1'] : null),
                        'postal_code' => (!empty($params['presentee_postal_code']) ? $params['presentee_postal_code'] : null),
                        'city' => (!empty($params['presentee_city']) ? $params['presentee_city'] : null),
                        // Opt-out, do-not-mail, do-not-mail.
                        'is_opt_out' => 1,
                        'do_not_email' => 1,
                        'do_not_mail' => 1,
                        'source' => CRM_Baumspenden_Configuration::CONTACT_SOURCE_PRESENTEE,
                    ]
                );
                // Set parameter for custom field retrieval.
                $params['presentee'] = $presentee_contact_id;
            }

            // Create contribution.
            $donation->contribution = self::createContribution(
                $params,
                $initiator_contact_id
            );

            // Create contribution activity.
            $donation->createContributionActivity();

            // Create activity of type "Schenkung Baumspende", if requested.
            if (!empty($params['as_present'])) {
                $donation->createPresentActivity($presentee_contact_id);
            }

            // Add newsletter subscription, if requested.
            if (!empty($params['newsletter'])) {
                $donation->createNewsletterSubscription();
            }

            return $donation;
        } catch (Exception $exception) {
            // Rollback current base transaction in order to not rollback the
            // creation of the "failed" activity.
            if (($frame = \Civi\Core\Transaction\Manager::singleton()->getFrame(
                )) !== null) {
                $frame->forceRollback();
            }

            // Check whether the initiator contact exists or its creation has
            // been rolled back.
            try {
                $initiator_contact_id = $donation->get('contact_id');
                civicrm_api3(
                    'Contact',
                    'getsingle',
                    ['id' => $initiator_contact_id]
                );
            } catch (Exception $excpetion) {
                $initiator_contact_id = null;
            }

            // Create activity of type "fehlgeschlagene_baumspende".
            $failed_activity_type_id = CRM_Core_PseudoConstant::getKey(
                'CRM_Activity_BAO_Activity',
                'activity_type_id',
                'fehlgeschlagene_baumspende'
            );
            civicrm_api3(
                'Activity',
                'create',
                [
                    'source_contact_id' => CRM_Core_Session::singleton()
                        ->getLoggedInContactID(),
                    'target_id' => (isset($initiator_contact_id) ? $initiator_contact_id : null),
                    'activity_type_id' => $failed_activity_type_id,
                    'status_id' => 'Scheduled',
                    'subject' => CRM_Baumspenden_Configuration::ACTIVITY_SUBJECT_FAILED,
                    'details' => '<p>' . $exception->getMessage() . '</p>'
                        . '<pre>' . json_encode(
                            $params,
                            JSON_PRETTY_PRINT
                        ) . '</pre>',
                ]
            );

            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Create an activity for the contribution, faking the contribution status
     * to be "Completed", so that the activity is always being created,
     * independently from the contribution status.
     *
     * @throws Exception
     */
    protected function createContributionActivity()
    {
        $contribution_bao_params = [
            'id' => $this->get('id'),
        ];
        // Fetch the contribution BAO.
        $contribution_bao = CRM_Contribute_BAO_Contribution::retrieve(
            $contribution_bao_params
        );
        // Fake the contribution status to force create an activity for this
        // pending contribution.
        $contribution_bao->contribution_status_id = CRM_Core_PseudoConstant::getKey(
            'CRM_Contribute_BAO_Contribution',
            'contribution_status_id',
            'Completed'
        );
        // Add the activity using core's logic.
        CRM_Activity_BAO_Activity::addActivity(
            $contribution_bao,
            'Contribution'
        );
    }

    /**
     * Creates an activity "Baumspende as present" with an optionally associated
     * presentee contact.
     *
     * @param null $presentee_contact_id
     *   The CiviCRM contact ID of the presentee.
     *
     * @throws Exception
     */
    protected function createPresentActivity($presentee_contact_id = null)
    {
        $present_activity_type_id = CRM_Core_PseudoConstant::getKey(
            'CRM_Activity_BAO_Activity',
            'activity_type_id',
            'schenkung_baumspende'
        );

        // Create the activity.
        civicrm_api3(
            'Activity',
            'create',
            [
                'source_contact_id' => $this->get('contact_id'),
                'activity_type_id' => $present_activity_type_id,
                'subject' => CRM_Baumspenden_Configuration::ACTIVITY_SUBJECT_PRESENT,
                'target_id' => $presentee_contact_id,
            ]
        );
    }

    /**
     * Creates a MailingEventSubscribe for the contributor in the newsletter
     * group.
     *
     * @throws Exception
     */
    protected function createNewsletterSubscription()
    {
        // Find the initiator contact's e-mail address to use for subscription.
        try {
            // Either the bulk e-mail address, ...
            $initiator_email = civicrm_api3(
                'Email',
                'getsingle',
                [
                    'contact_id' => $this->get('contact_id'),
                    'is_bulkmail' => 1,
                ]
            );
        } catch (Exception $exception) {
            // ... or the primary e-mail address.
            $initiator_email = civicrm_api3(
                'Email',
                'getsingle',
                [
                    'contact_id' => $this->get('contact_id'),
                    'is_primary' => 1,
                ]
            );
        }
        civicrm_api3(
            'MailingEventSubscribe',
            'create',
            [
                'contact_id' => $this->get('contact_id'),
                'email' => $initiator_email['email'],
                'group_id' => CRM_Baumspenden_Configuration::GROUP_ID_NEWSLETTER,
            ]
        );
    }

    /**
     * @param array $params
     *
     * @return int
     *   The CiviCRM contact ID.
     *
     * @throws \Exception
     */
    public static function retrieveContact($params)
    {
        $contact_data = array_intersect_key(
            $params,
            array_fill_keys(
                [
                    'first_name',
                    'last_name',
                    'street_address',
                    'supplemental_address_1',
                    'postal_code',
                    'city',
                    'email',
                    'source',
                    'is_opt_out',
                    'do_not_email',
                    'do_not_mail',
                ],
                true
            )
        );
        $xcm_result = civicrm_api3(
            'Contact',
            'getorcreate',
            $contact_data + [
                'xcm_profile' => CRM_Baumspenden_Configuration::XCM_PROFILE,
            ]
        );
        if ($xcm_result['is_error']) {
            throw new Exception($xcm_result['error_message']);
        }

        return (int)$xcm_result['id'];
    }

    /**
     * @param array $params
     * @param int $contact_id
     *   The CiviCRM contact ID to create the contribution for.
     *
     * @return array
     * @throws \CiviCRM_API3_Exception
     */
    protected static function prepareContributionData(
        $params,
        $contact_id
    ) {
        $financial_type = civicrm_api3(
            'FinancialType',
            'getsingle',
            [
                'name' => CRM_Baumspenden_Configuration::FINANCIAL_TYPE_NAME,
            ]
        );
        $contribution_data = [
            'contact_id' => $contact_id,
            'amount' => $params['unit_price'] * $params['amount'],
            'total_amount' => $params['unit_price'] * $params['amount'],
            'financial_type_id' => $financial_type['id'],
            'source' => CRM_Baumspenden_Configuration::CONTRIBUTION_SOURCE,
        ];
        // Include custom field data.
        if (empty($params['certificate_name'])) {
            $params['certificate_name'] = "{$params['first_name']} {$params['last_name']}";
        }
        foreach (
            [
                'unit_price' => false,
                'amount' => false,
                'plant_region' => true,
                'plant_period' => true,
                'plant_tree' => true,
                'certificate_name' => false,
                'presentee' => false,
            ] as $custom_field_name => $is_option_group
        ) {
            $custom_field = CRM_Baumspenden_CustomData::getCustomField(
                'baumspende',
                'baumspende_' . $custom_field_name
            );

            // Resolve or add option values for the custom fields.
            if ($is_option_group) {
                try {
                    $option_value = civicrm_api3(
                        'OptionValue',
                        'getsingle',
                        [
                            'option_group_id' => 'baumspenden_' . $custom_field_name,
                            'value' => $params[$custom_field_name],
                        ]
                    );
                } catch (Exception $exception) {
                    $option_value = civicrm_api3(
                        'OptionValue',
                        'create',
                        [
                            'option_group_id' => 'baumspenden_' . $custom_field_name,
                            'name' => $params[$custom_field_name . '_label'],
                            'value' => $params[$custom_field_name],
                        ]
                    );
                    $option_value = reset($option_value['values']);
                }
                $value = $option_value['value'];
            } else {
                $value = (isset($params[$custom_field_name]) ? $params[$custom_field_name] : null);
            }

            $contribution_data['custom_' . $custom_field['id']] = $value;
        }

        return $contribution_data;
    }

    /**
     * Creates a CiviCRM contribution entity.
     *
     * @param array $params
     *
     * @return array
     *   The CiviCRM Contribution API result.
     *
     * @throws Exception
     */
    protected static function createContribution($params, $initiator_contact_id)
    {
        $contribution_data = self::prepareContributionData(
            $params,
            $initiator_contact_id
        );

        // Accept different payment instruments, SEPA being one of them.
        switch ($params['payment_method']) {
            case 'sepa_direct_debit':
                $mandate_data = $contribution_data + [
                        'type' => 'OOFF',
                        'iban' => $params['iban'],
                        'bic' => $params['bic'],
                    ];
                $mandate = civicrm_api3(
                    'SepaMandate',
                    'createfull',
                    $mandate_data
                );
                if ($mandate['is_error']) {
                    throw new Exception($mandate['error_message']);
                }
                $contribution = civicrm_api3(
                    'Contribution',
                    'getsingle',
                    [
                        'id' => $mandate['values'][$mandate['id']]['entity_id'],
                    ]
                );
                break;
            case 'payment_request':
                $contribution_data['payment_instrument_id'] = CRM_Baumspenden_Configuration::PAYMENT_INSTRUMENT_ID_PAYMENT_REQUEST;
                // Donors have to issue the payment themselves, therefore it is
                // pending.
                $contribution_data['contribution_status_id'] = 'Pending';
                break;
            case 'paypal':
                $contribution_data['payment_instrument_id'] = CRM_Baumspenden_Configuration::PAYMENT_INSTRUMENT_ID_PAYPAL;
                // The payment has been initialized by the payment processor,
                // therefore it is in progress already.
                $contribution_data['contribution_status_id'] = 'In Progress';
                break;
            case 'credit_card':
                $contribution_data['payment_instrument_id'] = CRM_Baumspenden_Configuration::PAYMENT_INSTRUMENT_ID_CREDIT_CARD;
                // The payment has been initialized by the payment processor,
                // therefore it is in progress already.
                $contribution_data['contribution_status_id'] = 'In Progress';
                break;
            default:
                throw new Exception(E::ts('Unsupported payment method'));
        }
        if (!isset($contribution)) {
            $contribution = civicrm_api3(
                'Contribution',
                'create',
                $contribution_data
            );
            if ($contribution['is_error']) {
                throw new Exception($contribution['error_message']);
            }
        }

        return $contribution;
    }
}
