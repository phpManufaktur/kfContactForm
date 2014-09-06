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
use phpManufaktur\ContactForm\Control\Configuration;

class Update
{
    protected $app = null;
    protected $Configuration = null;

    /**
     * Execute the update for the miniShop
     *
     * @param Application $app
     */
    public function Controller(Application $app)
    {
        $this->app = $app;
        $this->Configuration = new Configuration($app);

        return $app['translator']->trans('Successfull updated the extension %extension%.',
            array('%extension%' => 'ContactForm'));
    }
}
