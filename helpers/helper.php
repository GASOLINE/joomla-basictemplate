<?php
/**
 * @package     Basic template
 * @copyright   Copyright (c) Joomla Community Netherlands
 * @license     GNU General Public License version 3 or later
 * @version     1.0
 */

// No direct access.
defined('_JEXEC') or die;

// Get Parameters configuration from templateDetails
require_once 'params.php';

class BasicTemplateHelper
{
    static public function template()
    {
        return JFactory::getApplication()->getTemplate();
    }

    /**
    * Method to manually override the META-generator
    * @since BasicTemplate 1.0
    */
    static public function setGenerator($generator)
    {
        JFactory::getDocument()->setGenerator($generator);
    }

    /**
     * Method to get the current sitename
     * @since BasicTemplate 1.0
     */
    static public function getSitename()
    {
        return JFactory::getConfig()->get('config.sitename');
    }

    /**
     * Method to set some Meta data
     * @since BasicTemplate 1.0
     */
    static public function setMetadata()
    {
        $doc    = JFactory::getDocument();
        $config = JFactory::getConfig();

        $doc->setCharset('utf-8');
        $doc->setMetaData('X-UA-Compatible', 'IE=edge', true);
        $doc->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
        $doc->setMetaData('content-type', 'text/html', true );
        $doc->setMetaData('mobile-web-app-capable', 'yes');
        $doc->setMetaData('apple-mobile-web-app-capable', 'yes');
        $doc->setMetaData('apple-mobile-web-app-status-bar-style', 'black');
        $doc->setMetaData('apple-mobile-web-app-title', $config->get('sitename'));
        self::setGenerator(self::getSitename());
    }

    /**
     * Method to set Favicon
     * @since BasicTemplate 1.0
     */
    static public function setFavicon()
    {
        $doc = JFactory::getDocument();

        $doc->addHeadLink('templates/' . self::template() . '/images/favicon.ico', 'shortcut icon', 'rel', array('type' => 'image/ico'));
        $doc->addHeadLink('templates/' . self::template() . '/images/favicon.png', 'shortcut icon', 'rel', array('type' => 'image/png'));
    }

    /**
     * Method to return the current Menu Item ID
     * @since BasicTemplate 1.0
     */
    static public function getItemId()
    {
        return JFactory::getApplication()->input->getInt('Itemid');
    }

    /** Generate the needed information for the GetBodySuffix */
    /**
     * Method to fetch the current path
     * @since BasicTemplate 1.0
     */
    static public function getPath($output = 'array')
    {
        $uri  = JURI::getInstance();
        $path = $uri->getPath();
        $path = preg_replace('/^\//', '', $path);
        if ($output == 'array')
        {
            $path = explode('/', $path);

            return $path;
        }

        return $path;
    }

    /**
     * get PageClass set with Menu Item
     * @since BasicTemplate 1.0
     */
    static public function getPageClass()
    {
        $activeMenu = JFactory::getApplication()->getMenu()->getActive();
        $pageclass  = ($activeMenu) ? $activeMenu->params->get('pageclass_sfx', '') : '';

        return $pageclass;
    }

    /**
     * @since BasicTemplate 1.0
     */
    static public function getPageOption()
    {
        $input = JFactory::getApplication()->input;

        return str_replace('_', '-', $input->getCmd('option', ''));
    }

    /**
     * @since BasicTemplate 1.0
     */
    static public function getPageView()
    {
        $input = JFactory::getApplication()->input;

        return str_replace('_', '-', $input->getCmd('view', ''));
    }

    /**
     * @since BasicTemplate 1.0
     */
    static public function getPageLayout()
    {
        $input = JFactory::getApplication()->input;

        return str_replace(self::template(), '', $input->getCmd('layout', ''));
    }

    /**
     * @since BasicTemplate 1.0
     */
    static public function getPageTask()
    {
        $input = JFactory::getApplication()->input;

        return str_replace('_', '', $input->getCmd('task', ''));
    }
    /**
     * Method to determine whether the current page is the Joomla! homepage
     * @since BasicTemplate 1.0
     */
    static public function isHome()
    {
        // Fetch the active menu-item
        $activeMenu = JFactory::getApplication()->getMenu()->getActive();

        // Return whether this active menu-item is home or not
        return (boolean) ($activeMenu) ? $activeMenu->home : false;
    }

    /**
     * Generate a list of useful CSS classes for the body
     * @since  BasicTemplate 1.0
     */
    static public function getBodySuffix()
    {
        $classes   = array();
        $classes[] = 'option-' . self::getPageOption();
        $classes[] = 'view-' . self::getPageView();
        $classes[] = self::getPageLayout() ? 'layout-' . self::getPageLayout() : 'no-layout';
        $classes[] = self::getPageTask() ? 'task-' . self::getPageTask() : 'no-task';
        $classes[] = 'itemid-' . self::getItemId();
        $classes[] = self::getPageClass();
        $classes[] = self::isHome() ? 'path-home' : 'path-' . implode('-', self::getPath('array'));
        $classes[] = 'home-' . (int) self::isHome();

        return implode(' ', $classes);
    }

    /**
     * Remove unwanted CSS
     * @since  BasicTemplate 1.0
     */
    static public function unloadCss()
    {
        $doc = JFactory::getDocument();

        $unset_css = array('com_finder');
        foreach ($doc->_styleSheets as $name => $style)
        {
            foreach ($unset_css as $css)
            {
                if (strpos($name, $css) !== false)
                {
                    unset($doc->_styleSheets[$name]);
                }
            }
        }
    }

    /**
     * Load CSS
     * @since  BasicTemplate 1.0
     */
    static public function loadCss()
    {
        JHtml::_('stylesheet', 'templates/' . self::template() . '/css/template.css', array('version' => 'auto'));
    }

    /**
     * Remove unwanted JS
     * @since  BasicTemplate 1.0
     */
    static public function unloadJs()
    {
        $doc = JFactory::getDocument();

        // Call JavaScript to be able to unset it correctly
        JHtml::_('behavior.framework');
        JHtml::_('bootstrap.framework');
        JHtml::_('jquery.framework');
        JHtml::_('bootstrap.tooltip');

        // Unset unwanted JavaScript
        unset($doc->_scripts[$doc->baseurl . '/media/system/js/mootools-core.js']);
        unset($doc->_scripts[$doc->baseurl . '/media/system/js/mootools-more.js']);
        unset($doc->_scripts[$doc->baseurl . '/media/system/js/caption.js']);
        unset($doc->_scripts[$doc->baseurl . '/media/system/js/core.js']);
        unset($doc->_scripts[$doc->baseurl . '/media/system/js/validate.js']);
        unset($doc->_scripts[$doc->baseurl . '/media/system/js/modal.js']);
        unset($doc->_scripts[$doc->baseurl . '/media/jui/js/jquery.min.js']);
        unset($doc->_scripts[$doc->baseurl . '/media/jui/js/jquery-noconflict.js']);
        unset($doc->_scripts[$doc->baseurl . '/media/jui/js/jquery-migrate.min.js']);
        unset($doc->_scripts[$doc->baseurl . '/media/jui/js/bootstrap.min.js']);
        unset($doc->_scripts[$doc->baseurl . '/media/system/js/tabs-state.js']);


        if (isset($doc->_script['text/javascript']))
        {
            $doc->_script['text/javascript'] = preg_replace('%jQuery\(window\)\.on\(\'load\'\,\s*function\(\)\s*\{\s*new\s*JCaption\(\'img.caption\'\);\s*}\s*\);\s*%', '', $doc->_script['text/javascript']);
            $doc->_script['text/javascript'] = preg_replace("%\s*jQuery\(document\)\.ready\(function\(\)\{\s*jQuery\('\.hasTooltip'\)\.tooltip\(\{\"html\":\s*true,\"container\":\s*\"body\"\}\);\s*\}\);\s*%", '', $doc->_script['text/javascript']);
            $doc->_script['text/javascript'] = preg_replace('%\s*jQuery\(function\(\$\)\{\s*\$\(\"\.hasTooltip\"\)\.tooltip\(\{\"html\":\s*true,\"container\":\s*\"body\"\}\);\s*\}\);\s*%', '', $doc->_script['text/javascript']);

            // Unset completely if empty
            if (empty($doc->_script['text/javascript']))
            {
                unset($doc->_script['text/javascript']);
            }
        }
    }

    /**
     * Load JS
     *
     * @since  BasicTemplate 1.0
     */
    static public function loadJs()
    {
        JHtml::_('script', 'media/jui/js/jquery.min.js', array('version' => 'auto'));
        JHtml::_('script', 'templates/' . self::template() . '/js/bootstrap.min.js', array('version' => 'auto'));
        JHtml::_('script', 'templates/' . self::template() . '/js/logic.js', array('version' => 'auto'));
    }

    /**
     * Insert the Google Analytics Tracking code
     * @since  BasicTemplate 1.0
     */
    static public function putAnalyticsTrackingCode()
    {
        $analytics = JFactory::getApplication()->getTemplate(true)->params->get('analytics');
        if ($analytics)
        {
            echo "<script>\n";
            echo "  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){\n";
            echo "  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),\n";
            echo "  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)\n";
            echo "  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');\n";
            echo "  ga('create', '$analytics', 'auto');\n";
            echo "  ga('send', 'pageview');\n";
            echo "</script>\n";
        }
    }

    static  public function loadApplIcon()
    {
        //Detect special conditions devices
        $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
        $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
        $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
        $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
        $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
        $macOS   = stripos($_SERVER['HTTP_USER_AGENT'],"Macintosh");

        //do something with this information
        if( $iPod || $iPhone || $iPad ){
            $doc = JFactory::getDocument();
            $doc->addHeadLink('templates/' . self::template() . '/images/apple-touch-icon-57x57-precomposed.png', 'apple-touch-icon-precomposed', 'rel', array('type' => 'image/png'));
            $doc->addHeadLink('templates/' . self::template() . '/images/apple-touch-icon-72x72-precomposed.png', 'apple-touch-icon-precomposed', 'rel', array('type' => 'image/png','sizes' => '72x72'));
            $doc->addHeadLink('templates/' . self::template() . '/images/apple-touch-icon-114x114-precomposed.png', 'apple-touch-icon-precomposed', 'rel', array('type' => 'image/png','sizes' => '114x114'));
            $doc->addHeadLink('templates/' . self::template() . '/images/apple-touch-icon-144x144-precomposed.png', 'apple-touch-icon-precomposed', 'rel', array('type' => 'image/png','sizes' => '144x144'));
        }else if($iPad){
            //browser reported as an iPad -- do something here
        }else if($Android){
            //browser reported as an Android device -- do something here
        }else if($webOS){
            //browser reported as a webOS device -- do something here
        }else if($macOS){
            //browser reported as a macOS device -- do something here
        }
    }
}
