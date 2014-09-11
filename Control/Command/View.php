<?php

/**
 * ContactForm
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/ContactForm
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\ContactForm\Control\Command;

use phpManufaktur\Basic\Control\kitCommand\Basic;
use Silex\Application;
use phpManufaktur\ContactForm\Data\Form\Definition as DataDefinition;
use phpManufaktur\Contact\Control\Pattern\Form\Contact as ContactForm;

class View extends Basic
{
    protected static $parameter = null;
    protected $dataDefinition = null;

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Basic\Control\kitCommand\Basic::initParameters()
     */
    protected function initParameters(Application $app, $parameter_id=-1)
    {
        parent::initParameters($app, $parameter_id);

        self::$parameter = $this->getCommandParameters();

        // check the CMS GET parameters
        $GET = $this->getCMSgetParameters();
        if (isset($GET['command']) && ($GET['command'] == 'form') && isset($GET['action'])) {
            foreach ($GET as $key => $value) {
                if ($key == 'command') {
                    continue;
                }
                self::$parameter[$key] = $value;
            }
            $this->setCommandParameters(self::$parameter);
        }

        // grant that the 'action' value is a lower string
        self::$parameter['action'] = isset(self::$parameter['action']) ? strtolower(self::$parameter['action']) : 'none';

        $this->dataDefinition = new DataDefinition($app);
    }

    protected function BuildContactFormFields($definition)
    {
        $data = unserialize($definition['data']);

        $fields = array(
            'predefined' => array(
                'contact_type'
            ),
            'visible' => array(
                'special_fields'
            ),
            'hidden' => array(
                'special_fields'
            ),
            'readonly' => array(

            ),
            'tags' => array(

            ),
            'special' => array(
                'form_id' => array(
                    'name' => 'form_id',
                    'enabled' => true,
                    'type' => 'hidden',
                    'data' => $definition['form_id'],
                )
            ),
        );

        foreach ($data['field']['contact'] as $item) {
            if ($item['hidden']) {
                $fields['hidden'][] = $item['name'];
            }
            else {
                $fields['visible'][] = $item['name'];
            }
            if ($item['required']) {
                $fields['required'][] = $item['name'];
            }
            if (!is_null($item['data'])) {
                if ($item['name'] === 'tags') {
                    $fields['tags'] = $item['data'];
                }
                else {
                    $fields['default_values'][$item['name']] = $item['data'];
                }
            }
        }

        return $fields;
    }

    public function Controller(Application $app)
    {
        $this->initParameters($app);

        if (!isset(self::$parameter['id'])) {
            $this->setAlert('Missing the parameter <b>%parameter%</b>, please check the kitCommand expression!',
                array('%parameter%' => 'id'), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }

        if (false === ($form_id = filter_var(self::$parameter['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))))) {
            $this->setAlert('Invalid ID, expect integer greater than 0.', array(), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }

        if ((false === ($definition = $this->dataDefinition->select($form_id))) || ($definition['form_status'] !== 'ACTIVE')) {
            $this->setAlert('The form with the ID <strong>%form_id%</strong> does not exists or is not active.',
                array('%form_id%' => $form_id), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }


        $fields = $this->BuildContactFormFields($definition);

        // get the contact form
        $ContactForm = new ContactForm($app);
/*echo "<pre>";
        print_r($fields);
        echo "</pre>";
        */
        if (false === ($form = $ContactForm->getFormContact(array(), $fields))) {
            return $this->promptAlert();
        }

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/ContactForm/Template', 'command/form.twig'),
            array(
                'basic' => $this->getBasicSettings(),
                //'config' => self::$config,
                'form' => $form->createView()
            ));
    }

    public function ControllerCheck(Application $app)
    {
        $this->initParameters($app);

        return __METHOD__;
    }
}
