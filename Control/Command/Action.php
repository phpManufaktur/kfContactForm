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

class Action extends Basic
{
    protected static $parameter = null;

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
    }

    /**
     * Action handler for the kitCommand ~~ form ~~
     *
     * @param Application $app
     */
    public function Controller(Application $app)
    {
        $this->initParameters($app);

        switch (self::$parameter['action']) {
            case 'view':
                // show an DogGallery record - also target for permanent links
                return $this->createIFrame('/form/view');
            case 'none':
                // missing the action parameter, show the welcome page!
                return $this->createIFrame('/basic/help/form/welcome');
            default:
                // unknown action parameter!
                $this->setAlert('The action <b>%action%</b> is unknown, please check the parameters for the kitCommand!',
                array('%action%' => self::$parameter['action']), self::ALERT_TYPE_WARNING);
                return $this->createIFrame('/basic/alert/'.base64_encode($this->getAlert()));
        }
    }
}
