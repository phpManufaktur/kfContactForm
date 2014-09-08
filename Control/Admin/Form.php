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

class Form extends Admin
{


    protected function getContactForm($fields=array(), $data=array())
    {
        // create the form
        $form = $this->app['form.factory']->createBuilder('form', null,
            array('csrf_protection' => self::$config['form']['csrf']));

        foreach (self::$config['form']['field']['list'] as $field) {
            // checkbox to select the field
            $form->add('field_'.$field, 'checkbox', array(
                'data' => (isset($data[$field]['field']) || in_array($field, self::$config['form']['field']['must_have'])),
                'required' => ($field === 'communication_email'),
                'attr' => array('class' => 'field-select'),
                'disabled' => ($field === 'communication_email')
            ));
            // checkbox to set the field as required
            $form->add('required_'.$field, 'checkbox', array(
                'data' => (isset($data[$field]['required']) || in_array($field, self::$config['form']['field']['required'])),
                'required' => ($field === 'communication_email'),
                'attr' => array('class' => 'field-require'),
                'disabled' => ($field === 'communication_email')
            ));
            // checkbox to hide the field
            $form->add('hidden_'.$field, 'checkbox', array(
                'data' => isset($data[$field]['hidden']),
                'required' => false,
                'attr' => array('class' => 'field-hidden'),
                'disabled' => !in_array($field, self::$config['form']['field']['hidden'])
            ));
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
            'data' => null
        ));

        // add contact categories
        $categories = $this->app['contact']->getCategoryArrayForTwig();
        $form->add('contact_categories', 'choice', array(
            'choices' => $categories,
            'expanded' => false,
            'empty_value' => false,
            'data' => null
        ));

        // add contact tags
        $tags = $this->app['contact']->getTagArrayForTwig();
        $form->add('contact_tags', 'choice', array(
            'choices' => $tags,
            'expanded' => true,
            'multiple' => true,
            'data' => null
        ));

        return $form->getForm();
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
