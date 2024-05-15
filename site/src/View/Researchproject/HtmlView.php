<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Researchprojects\Site\View\Researchproject;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
#use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Router\Route;
#use Joomla\CMS\Plugin\PluginHelper;
#use Joomla\Event\Event;

/**
 * Researchproject Component HTML View
 */
class HtmlView extends BaseHtmlView {

    /**
     * The researchproject object
     *
     * @var    \JObject
     */
    protected $item;

    /**
     * The page parameters
     *
     * @var    \Joomla\Registry\Registry|null
     */
    protected $params;

    /**
     * The item model state
     *
     * @var    \Joomla\Registry\Registry
     */
    protected $state;


    /*protected function getTitle() {
        return  $this->title = $menu->title;
    }*/


    public function display($template = null)
    {
        $app          = Factory::getApplication();
        $input        = $app->input;

        $this->state  = $this->get('State');
        $this->params = $this->state->get('params');
        $this->items  = $this->get('Items');
        $this->item   = $this->get('Item');


        #echo '<pre>'; var_dump($this->state); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($this->item); echo '</pre>'; exit;

        $user = $app->getIdentity();
        $user_is_root = $user->authorise('core.admin');
        $this->user   = $user;


        $this->state  = $this->get('State');
        $this->params = $this->state->get('params');

        $document     = Factory::getDocument();

        // We may not actually want to show the form at this point (though we could if we wanted to
        // include the form AND the record on the same page - especially if it's displayed via a
        // modal), but it's useful to have the form so we can retrieve language strings without
        // having to manually reclare them, along with any other properties of the form that may be
        // useful:
        $this->form = $this->get('Form');

        // Load admin lang file for use in the form:
        $app->getLanguage()->load('com_researchprojects', JPATH_COMPONENT_ADMINISTRATOR);


        $uri    = Uri::getInstance();
        $menus  = $app->getMenu();
        $menu   = $menus->getActive();
        #echo '<pre>'; var_dump($menu); echo '</pre>'; exit;
        $this->menu_params = $menu->getParams();

        $pathway = $app->getPathway();

        // Fix the pathway link:
        // I don't think this should be necessary - I thought the Router should handle this???

        $pathway = $app->getPathway();

        $pathway_items = $pathway->getPathway();
        $last_item =  array_pop($pathway_items);
        $last_item->name =  $this->item->title;
        $pathway_items[] = (object) ['name' => $menu->title, 'link' => $menu->link];
        $pathway_items[] = $last_item;

        $pathway->setPathway($pathway_items);

        // Set the menu (page) title to be this item:
        $menu->title = $this->item->title;


        $this->return_page = base64_encode($uri::base() . $menu->route);



        // Check for errors.
        $errors = $this->get('Errors', false);

        if (!empty($errors)) {
            Log::add(implode('<br />', $errors), Log::WARNING, 'jerror');

            return false;
        }

        $this->return_page = base64_encode($uri);

        // Call the parent display to display the layout file
        parent::display($template);
    }
}