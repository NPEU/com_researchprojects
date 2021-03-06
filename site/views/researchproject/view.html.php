<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

// Load the Admin language file to avoid repoeating form language strings:
$lang = JFactory::getLanguage();
$extension = 'com_researchprojects';
$base_dir = JPATH_COMPONENT_ADMINISTRATOR;
$language_tag = 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

/**
 * HTML View class for the ResearchProjects Component
 */
class ResearchProjectsViewResearchProject extends JViewLegacy
{
    // Overwriting JView display method
    function display($tpl = null)
    {
        #echo '<pre>'; var_dump(JInput::get('layout')); echo '</pre>'; exit;

        $user  = JFactory::getUser();
        $app   = JFactory::getApplication();
        $menus = $app->getMenu();
        $menu  = $menus->getActive();
        $item  = $this->get('Item');
        
        // We may not actually want to show the form at this point (though we could if we wanted to
        // include the form AND the record on the same page - especially if it's displayed via a
        // modal), but it's useful to have the form so we can retrieve language strings without
        // having to manually reclare them, along with any other properties of the form that may be
        // useful:
        $form = $this->get('Form');
        #echo '<pre>'; var_dump($item); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($form); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($this->getLayout()); echo '</pre>'; exit;

       
        #echo '<pre>'; var_dump($menu); echo '</pre>'; exit;
        #echo '<pre>'; var_dump(JRoute::_($menu->link)); echo '</pre>'; exit;
        #echo '<pre>'; var_dump(JURI::base()); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($item->id); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($user, $item); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($user->id, $item->created_by); echo '</pre>'; exit;

        $this->return_page = base64_encode(JURI::base() . $menu->route);


        /*$is_new = empty($item->id);
        $is_own = false;
        if (!$is_new && ($user->id == $item->created_by)) {
            $is_own = true;
        }


        if ($is_new) {
            $authorised = $user->authorise('core.create', 'com_researchprojects');
        } elseif ($is_own) {
            $authorised = $user->authorise('core.edit.own', 'com_researchprojects');
        }
        else {
            $authorised = $user->authorise('core.edit', 'com_researchprojects');
        }

        if ($authorised !== true && $this->getLayout() == 'form') {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));

            return false;
        }*/

        /*if (!empty($this->item))
        {
            $this->form->bind($this->item);
        }*/
      

        // Add to breadcrumbs:
        /*if ((!$breadcrumb_title = $item->title)) {
            $breadcrumb_title  = JText::_('COM_RESEARCHPROJECTS_PAGE_TITLE_ADD_NEW');
        }*/
  
        $pathway = $app->getPathway();
        #echo '<pre>'; var_dump($pathway); echo '</pre>'; exit;
        $pathway->addItem($item->title);
        
        // Page title
        $menu->title = $item->title;
        $doc = JFactory::getDocument();
		$doc->title = $item->title . ' | ' . $doc->title;


        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

            return false;
        }


        // Assign data to the view
        $this->item = $item;
        // Although we're not actually showing the form, it's useful to use it to be able to show
        // the field names without having to explicitly state them (more DRY):
        $this->form = $form;


        // Display the view
        parent::display($tpl);

        // Assign data to the view
        #$this->msg = 'Get from API';

        /*$form = $this->get('Form');
        $item   = $this->get('Item');

        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $menu   = $menus->getActive();

        // Get the parameters
        $this->com_params  = JComponentHelper::getParams('com_researchprojects');
        $this->menu_params = $menu->params;

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

            return false;
        }

        // Assign data to the view
        $this->form   = $form;
        $this->item   = $item;
        $this->title  = $menu->title;
        // Display the view
        parent::display($tpl);*/
    }
}
