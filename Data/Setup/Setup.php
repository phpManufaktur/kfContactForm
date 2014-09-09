<?php

/**
 * ContactForm
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/ContactForm
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\ContactForm\Data\Setup;

use Silex\Application;
use phpManufaktur\Basic\Control\CMS\InstallAdminTool;
use phpManufaktur\ContactForm\Control\Configuration;
use phpManufaktur\ContactForm\Data\Form\Definition;

class Setup
{
    protected $app = null;
    protected static $configuration = null;


    /**
     * Execute all steps needed to setup ContactForm
     *
     * @param Application $app
     * @throws \Exception
     * @return string with result
     */
    public function Controller(Application $app)
    {
        try {
            $this->app = $app;

            $Configuration = new Configuration($app);
            self::$configuration = $Configuration->getConfiguration();

            // setup ContactForm as Add-on in the CMS
            $admin_tool = new InstallAdminTool($app);
            $admin_tool->exec(MANUFAKTUR_PATH.'/ContactForm/extension.json', '/form/cms');

            $dataDefinition = new Definition($app);
            $dataDefinition->createTable();

            return $app['translator']->trans('Successfull installed the extension %extension%.',
                array('%extension%' => 'ContactForm'));

        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
