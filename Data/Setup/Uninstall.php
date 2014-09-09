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
use phpManufaktur\ContactForm\Data\Form\Definition;

class Uninstall
{
    protected $app = null;

    /**
     * Execute the update for the miniShop
     *
     * @param Application $app
     */
    public function Controller(Application $app)
    {
        $this->app = $app;

        $dataDefinition = new Definition($app);
        $dataDefinition->dropTable();

        return $app['translator']->trans('Successfull uninstalled the extension %extension%.',
            array('%extension%' => 'ContactForm'));
    }
}
