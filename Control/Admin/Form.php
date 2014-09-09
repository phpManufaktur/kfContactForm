<?php

/**
 * ContactForm
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/ContactForm
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\ContactForm\Control\Admin;

use Silex\Application;
use phpManufaktur\Contact\Data\Contact\ExtraType;
use phpManufaktur\ContactForm\Data\Form\Definition;

class Form extends Admin
{
    protected $dataDefinition = null;

    protected function initialize(Application $app)
    {
        parent::initialize($app);

        $this->dataDefinition = new Definition($app);
    }

    /**
     * Create the Form Form ...
     *
     * @param array $data
     */
    protected function getContactForm($data=array())
    {
        // create the form
        $form = $this->app['form.factory']->createBuilder('form', null,
            array('csrf_protection' => self::$config['form']['csrf']));

        $form->add('form_id', 'hidden', array(
            'data' => isset($data['form_id']) ? $data['form_id'] : -1
        ));
        $form->add('form_name', 'text', array(
            'data' => isset($data['form_name']) ? $data['form_name'] : '',
            'required' => true,
        ));
        $form->add('form_description', 'textarea', array(
            'data' => isset($data['form_description']) ? $data['form_description'] : '',
            'required' => false
        ));

        $status = array();
        foreach ($this->dataDefinition->getStatusTypes() as $type) {
            $status[$type] = $this->app['utils']->humanize($type);
        }
        $form->add('form_status', 'choice', array(
            'data' => isset($data['form_status']) ? $data['form_status'] : 'LOCKED',
            'choices' => $status,
            'required' => true,
            'empty_value' => false
        ));

        foreach (self::$config['form']['field']['list'] as $field) {
            // checkbox to select the field
            $form->add('field_'.$field, 'checkbox', array(
                'data' => (isset($data['field']['contact'][$field]['data']) || in_array($field, self::$config['form']['field']['must_have'])),
                'required' => ($field === 'communication_email'),
                'attr' => array(
                    'class' => 'field-select',
                    'title' => $this->app['translator']->trans('Check to use this field in the form')
                ),
                'disabled' => ($field === 'communication_email')
            ));
            // checkbox to set the field as required
            $form->add('required_'.$field, 'checkbox', array(
                'data' => ((isset($data['field']['contact'][$field]['required']) && $data['field']['contact'][$field]['required']) || in_array($field, self::$config['form']['field']['required'])),
                'required' => ($field === 'communication_email'),
                'attr' => array(
                    'class' => 'field-require',
                    'title' => $this->app['translator']->trans('Check to mark this field as required')
                ),
                'disabled' => ($field === 'communication_email')
            ));
            // checkbox to hide the field
            if (in_array($field, self::$config['form']['field']['hidden'])) {
                $form->add('hidden_'.$field, 'checkbox', array(
                    'data' => (isset($data['field']['contact'][$field]['hidden']) && $data['field']['contact'][$field]['hidden']),
                    'required' => false,
                    'attr' => array(
                        'class' => 'field-hidden',
                        'title' => $this->app['translator']->trans('Check to hide this field in the form')
                    )
                ));
            }
            else {
                $form->add('hidden_'.$field, 'hidden');
            }
        }

        // add contact types
        $form->add('contact_types', 'choice', array(
            'choices' => array(
                'PERSON' => 'Person',
                'COMPANY' => 'Company',
                'MIXED' => 'Mixed'
            ),
            'expanded' => false,
            'empty_value' => false,
            'data' => isset($data['field']['contact']['contact_type']['data']) ? $data['field']['contact']['contact_type']['data'] : 'MIXED'
        ));

        // add contact categories
        $categories = $this->app['contact']->getCategoryArrayForTwig();
        $form->add('contact_categories', 'choice', array(
            'choices' => $categories,
            'expanded' => false,
            'empty_value' => false,
            'data' => isset($data['field']['contact']['contact_category']['data']) ? $data['field']['contact']['contact_category']['data'] : null
        ));

        // add contact tags
        $tags = $this->app['contact']->getTagArrayForTwig();
        $form->add('contact_tags', 'choice', array(
            'choices' => $tags,
            'expanded' => true,
            'multiple' => true,
            'data' => isset($data['field']['contact']['contact_tags']['data']) ? $data['field']['contact']['contact_tags']['data'] : null
        ));

        $dataExtraType = new ExtraType($this->app);
        $extra_fields = $dataExtraType->selectAll();
        if (is_array($extra_fields)) {
            foreach ($extra_fields as $field) {
                $field_name = 'extra_'.strtolower($field['extra_type_name']);
                $form->add('field_'.$field_name, 'checkbox', array(
                    'data' => isset($data['field']['contact'][$field_name]['data']),
                    'required' => false,
                    'attr' => array(
                        'class' => 'field-select',
                        'title' => $this->app['translator']->trans('Check to use this field in the form')
                    )
                ));
                $form->add('required_'.$field_name, 'checkbox', array(
                    'data' => (isset($data['field']['contact'][$field_name]['required']) && $data['field']['contact'][$field_name]['required']),
                    'required' => false,
                    'attr' => array(
                        'class' => 'field-required',
                        'title' => $this->app['translator']->trans('Check to mark this field as required')
                    )
                ));
                $form->add('hidden_'.$field_name, 'hidden');
            }
        }

        return $form->getForm();
    }

    public function ControllerCheck(Application $app)
    {
        $this->initialize($app);

        $form = $this->getContactForm();
        $form->bind($this->app['request']);

        if ($form->isValid()) {
            // the form is valid
            $form_data = $form->getData();

            $data = array();
            foreach ($form_data as $item_key => $item_value) {
                if (strpos($item_key, 'field_') === 0) {
                    $field = substr($item_key, strlen('field_'));
                    if ($item_value) {
                        switch ($field) {
                            case 'contact_type':
                                $value = $form_data['contact_types'];
                                break;
                            case 'contact_category':
                                $value = $form_data['contact_categories'];
                                break;
                            case 'contact_tags':
                                $value = $form_data['contact_tags'];
                                break;
                            default:
                                $value = $item_value;
                                break;
                        }
                        $data['field']['contact'][$field] = array(
                            'name' => $field,
                            'data' => $value,
                            'required' => isset($form_data['required_'.$field]) ? $form_data['required_'.$field] : false,
                            'hidden' => isset($form_data['hidden_'.$field]) ? $form_data['hidden_'.$field] : false
                        );
                    }
                }
            }

            if (!empty($data)) {
                if (!isset($data['field']['contact']['communication_email'])) {
                    $data['field']['contact']['communication_email'] = array(
                        'name' => 'communication_email',
                        'data' => true,
                        'required' => true,
                        'hidden' => false
                    );
                }
                $form_id = isset($form_data['form_id']) ? $form_data['form_id'] : -1;
                $definition = array(
                    'form_name' => (isset($form_data['form_name']) && !is_null($form_data['form_name'])) ? $form_data['form_name'] : '',
                    'form_description' => (isset($form_data['form_description']) && !is_null($form_data['form_description'])) ? $form_data['form_description'] : '',
                    'form_status' => isset($form_data['form_status']) ? $form_data['form_status'] : 'LOCKED',
                    'data' => serialize($data)
                );
                if ($form_id > 0) {
                    // update existing definition
                    $this->dataDefinition->update($form_id, $data);
                    $this->setAlert('The record with the ID %id% was successfull updated.',
                        array('%id%' => $form_id), self::ALERT_TYPE_SUCCESS);
                }
                else {
                    // insert a new definition
                    $form_id = $this->dataDefinition->insert($data);
                    $this->setAlert('The record with the ID %id% was successfull inserted.',
                        array('%id%' => $form_id), self::ALERT_TYPE_SUCCESS);
                }
                return $this->Controller($app, $form_id);
            }
            else {
                // no valid definition
                $this->setAlert('The form definition does not contain any field and is not valid.', array(), self::ALERT_TYPE_WARNING);
                return $this->Controller($app, -1);
            }
        }
        else {
            // general error (timeout, CSFR ...)
            $this->setAlert('The form is not valid, please check your input and try again!', array(),
                self::ALERT_TYPE_DANGER, true, array('form_errors' => $form->getErrorsAsString(),
                    'method' => __METHOD__, 'line' => __LINE__));
            return $this->Controller($app, -1);
        }
    }

    public function Controller(Application $app, $form_id)
    {
        $this->initialize($app);

        $form = $this->getContactForm();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/ContactForm/Template', 'admin/edit.form.twig'),
            array(
                'alert' => $this->getAlert(),
                'config' => self::$config,
                'usage' => self::$usage,
                'toolbar' => $this->getToolbar('edit'),
                'form' => $form->createView()
            ));
    }
}
