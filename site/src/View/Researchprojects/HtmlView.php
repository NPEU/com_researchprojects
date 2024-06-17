<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Researchprojects\Site\View\Researchprojects;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
#use Joomla\CMS\Helper\TagsHelper;
#use Joomla\CMS\Router\Route;
#use Joomla\CMS\Plugin\PluginHelper;
#use Joomla\Event\Event;

/**
 * Researchprojects Component HTML View
 */
class HtmlView extends BaseHtmlView {


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

    // This allows alternate views to overide this and supply a different title:
    protected function getTitle($title = '') {
        return $title;
    }

    public function display($template = null)
    {
        $app = Factory::getApplication();

        $this->state  = $this->get('State');
        $this->params = $this->state->get('params');
        $this->items  = $this->get('Items');


        $user = $app->getIdentity();
        $user_is_root = $user->authorise('core.admin');
        $this->user  = $user;

        $document = Factory::getDocument();


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

        $this->title = $this->getTitle($menu->title);
        $this->menu_params = $menu->getParams();

        $db    = Factory::getDbo();
        // Get the list of topics:
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__researchprojects_topics'));
        $db->setQuery($query);

        $topics = $db->loadObjectList('id');

        $topic_title = '';
        $doc_title = '';
        $topic_id = $app->input->getInt('topic_id');
        if ($topic_id) {

            $query = $db->getQuery(true)
                ->select($db->quoteName('title'))
                ->from($db->quoteName('#__researchprojects_topics'))
                ->where($db->quoteName('id') . ' = ' . $db->quote($topic_id));
            $db->setQuery($query);

            $topic_title = $db->loadResult();
            $document->page_heading_additional = ' Topic: ' . $topic_title;

            $pathway = $app->getPathway();
            $pathway->addItem('Topic: ' . $topic_title);
        }

        $this->topics = $topics;

        // Add to breadcrumbs:
        $pathway = $app->getPathway();

        $layout = $this->getLayout();
        if ($layout != 'default') {

            $page_title = Text::_('COM_RESEARCHPROJECTS_PAGE_TITLE_' . strtoupper($layout));
            $pathway->addItem($page_title);
            $menu->title = $page_title;
        }

        // Check for errors.
        $errors = $this->get('Errors', false);

        if (!empty($errors)) {
            Log::add(implode('<br />', $errors), Log::WARNING, 'jerror');

            return false;
        }


        // Call the parent display to display the layout file
        parent::display($template);

    }

}