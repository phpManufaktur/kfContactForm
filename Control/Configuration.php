<?php

/**
 * ContactForm
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/ContactForm
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\ContactForm\Control;

use Silex\Application;

class Configuration
{
    protected $app = null;
    protected static $config = null;
    protected static $config_path = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        self::$config_path = MANUFAKTUR_PATH.'/ContactForm/config.form.json';
        $this->readConfiguration();
    }


    /**
     * Return the default configuration array for the ContactForm
     *
     * @return array
     */
    public function getDefaultConfigArray()
    {
        return array(
            'nav_tabs' => array(
                'order' => array(
                    'edit',
                    'about'
                ),
                'default' => 'about'
            ),
            'form' => array(
                'csrf' => true,
                'field' => array(
                    'hidden' => array(
                        'contact_id',
                        'contact_category',
                        'contact_tag',
                        'contact_type',
                        'address_primary_id',
                        'address_company_id',
                        'address_delivery_id',
                        'address_billing_id'
                    ),
                    'required' => array(
                        'communication_email'
                    ),
                    'must_have' => array(
                        'communication_email'
                    ),
                    'list' => array(
                        'contact_type',
                        'contact_category',
                        'contact_tags',
                        'contact_note',

                        'company_name',
                        'company_department',

                        'person_gender',
                        'person_title',
                        'person_first_name',
                        'person_last_name',
                        'person_nick_name',
                        'person_birthday',

                        'communication_email',
                        'communication_phone',
                        'communication_cell',
                        'communication_fax',
                        'communication_url',

                        'address_private_street',
                        'address_private_zip',
                        'address_private_city',
                        'address_private_area',
                        'address_private_state',
                        'address_private_country_code',

                        'address_business_street',
                        'address_business_zip',
                        'address_business_city',
                        'address_business_area',
                        'address_business_state',
                        'address_business_country_code',

                        'address_delivery_street',
                        'address_delivery_zip',
                        'address_delivery_city',
                        'address_delivery_area',
                        'address_delivery_state',
                        'address_delivery_country_code',

                        'address_delivery_street',
                        'address_delivery_zip',
                        'address_delivery_city',
                        'address_delivery_area',
                        'address_delivery_state',
                        'address_delivery_country_code',

                        'address_billing_street',
                        'address_billing_zip',
                        'address_billing_city',
                        'address_billing_area',
                        'address_billing_state',
                        'address_billing_country_code',
                    )
                )
            )
        );
    }

    /**
     * Read the configuration file
     */
    protected function readConfiguration()
    {
        if (!file_exists(self::$config_path)) {
            self::$config = $this->getDefaultConfigArray();
            $this->saveConfiguration();
        }
        self::$config = $this->app['utils']->readConfiguration(self::$config_path);
    }

    /**
     * Save the configuration file
     */
    public function saveConfiguration()
    {
        // write the formatted config file to the path
        file_put_contents(self::$config_path, $this->app['utils']->JSONFormat(self::$config));
        $this->app['monolog']->addDebug('Save configuration to '.basename(self::$config_path));
    }

    /**
     * Get the configuration array
     *
     * @return array
     */
    public function getConfiguration()
    {
        return self::$config;
    }

    /**
     * Set the configuration array
     *
     * @param array $config
     */
    public function setConfiguration($config)
    {
        self::$config = $config;
    }

}
