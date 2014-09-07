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
use phpManufaktur\ContactForm\Control\Configuration;
use phpManufaktur\Basic\Control\Pattern\Alert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Admin extends Alert
{

    protected static $usage = null;
    protected static $config = null;

    /**
     * Initialize the class with the needed parameters
     *
     * @param Application $app
     */
    protected function initialize(Application $app)
    {
        parent::initialize($app);

        $cms = $this->app['request']->get('usage');
        self::$usage = is_null($cms) ? 'framework' : $cms;
        // set the locale from the CMS locale
        if (self::$usage != 'framework') {
            $app['translator']->setLocale($this->app['session']->get('CMS_LOCALE', 'en'));
        }
        $Configuration = new Configuration($app);
        self::$config = $Configuration->getConfiguration();
    }

    /**
     * Get the toolbar for all backend dialogs
     *
     * @param string $active dialog
     * @return array
     */
    public function getToolbar($active) {
        $toolbar = array();
        foreach (self::$config['nav_tabs']['order'] as $tab) {
            switch ($tab) {
                case 'about':
                    $toolbar[$tab] = array(
                        'name' => 'about',
                        'text' => $this->app['translator']->trans('About'),
                        'hint' => $this->app['translator']->trans('Information about the ContactForm extension'),
                        'link' => FRAMEWORK_URL.'/admin/form/about',
                        'active' => ($active === 'about')
                    );
                    break;
                case 'edit':
                    $toolbar[$tab] = array(
                        'name' => 'edit',
                        'text' => ($active === 'edit') ? $this->app['translator']->trans('Edit form') : $this->app['translator']->trans('Create form'),
                        'hint' => $this->app['translator']->trans('Create or edit contact forms'),
                        'link' => FRAMEWORK_URL.'/admin/form/edit',
                        'active' => ($active === 'edit')
                    );
                    break;
            }
        }
        return $toolbar;
    }

    /**
     * Controller to select the default navigation tab.
     *
     * @param Application $app
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ControllerSelectDefaultTab(Application $app)
    {
        $this->initialize($app);

        switch (self::$config['nav_tabs']['default']) {
            case 'about':
                $route = '/admin/form/about';
                break;
            case 'edit':
                $route = '/admin/form/edit';
            default:
                throw new \Exception('Invalid default nav_tab in configuration: '.self::$config['nav_tabs']['default']);
        }

        $subRequest = Request::create($route, 'GET', array('usage' => self::$usage));
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
 }
