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
use phpManufaktur\ContactForm\Data\Form\Definition as DataDefinition;

class DefinitionList extends Admin
{
    protected $dataDefinition = null;

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\ContactForm\Control\Admin\Admin::initialize()
     */
    protected function initialize(Application $app)
    {
        parent::initialize($app);

        $this->dataDefinition = new DataDefinition($app);
    }

    /**
     * Controller to show all available forms
     *
     * @param Application $app
     */
    public function Controller(Application $app)
    {
        $this->initialize($app);

        $definitions = $this->dataDefinition->selectAll();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/ContactForm/Template', 'admin/list.definition.twig'),
            array(
                'alert' => $this->getAlert(),
                'config' => self::$config,
                'usage' => self::$usage,
                'toolbar' => $this->getToolbar('definition_list'),
                'definitions' => $definitions
            ));
    }
}
