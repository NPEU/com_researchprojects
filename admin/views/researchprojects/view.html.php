<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

/**
 * ResearchProjects ResearchProjects View
 */
class ResearchProjectsViewResearchProjects extends JViewLegacy
{
    protected $items;

    protected $pagination;

    protected $state;

    /**
     * Display the ResearchProjects view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    function display($tpl = null)
    {
        $this->state         = $this->get('State');
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        $doc = JFactory::getDocument();
        $component_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(dirname(__DIR__)));
        #$doc->addScript($component_path . '/assets/inputmask.js');
        #$doc->addScript($component_path . '/assets/inputmask.date.extensions.js');
        
        $style = <<<CSS
        
//https://css-tricks.com/snippets/css/prevent-long-urls-from-breaking-out-of-container/

.word-wrap {
    /* These are technically the same, but use both */
    overflow-wrap: break-word;
    word-wrap: break-word;

    -ms-word-break: break-all;
    /* This is the dangerous one in WebKit, as it breaks things wherever */
    //word-break: break-all;
    /* Instead use this non-standard one: */
    word-break: break-word;

    /* Adds a hyphen where the word breaks, if supported (No Blink) */
    -ms-hyphens: auto;
    -moz-hyphens: auto;
    -webkit-hyphens: auto;
    hyphens: auto;
}
CSS;
        #$doc->addStyleDeclaration($style);


        ResearchProjectsHelper::addSubmenu('researchprojects');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     */
    protected function addToolBar()
    {
        //$canDo = ResearchProjectsHelper::getActions();
        $canDo = JHelperContent::getActions('com_researchprojects');
        $user  = JFactory::getUser();

        $title = JText::_('COM_RESEARCHPROJECTS_MANAGER_RECORDS');

        if ($this->pagination->total) {
            $title .= "<span style='font-size: 0.5em; vertical-align: middle;'> (" . $this->pagination->total . ")</span>";
        }

        // Note 'question-circle' is an icon/classname. Change to suit in all views.
        JToolBarHelper::title($title, 'lamp');
        /*
        JToolBarHelper::addNew('researchproject.add');
        if (!empty($this->items)) {
            JToolBarHelper::editList('researchproject.edit');
            JToolBarHelper::deleteList('', 'researchprojects.delete');
        }
        */
        if ($canDo->get('core.create') || count($user->getAuthorisedCategories('com_researchprojects', 'core.create')) > 0) {
            JToolbarHelper::addNew('researchproject.add');
        }

        if ($canDo->get('core.edit') || $canDo->get('core.edit.own'))
        {
            JToolbarHelper::editList('researchproject.edit');
        }

        if ($canDo->get('core.edit.state'))
        {
            JToolbarHelper::publish('researchprojects.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('researchprojects.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            //JToolbarHelper::custom('researchproject.featured', 'featured.png', 'featured_f2.png', 'JFEATURE', true);
            //JToolbarHelper::custom('researchproject.unfeatured', 'unfeatured.png', 'featured_f2.png', 'JUNFEATURE', true);
            //JToolbarHelper::archiveList('researchproject.archive');
            //JToolbarHelper::checkin('researchproject.checkin');
        }


        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
        {
            JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'researchprojects.delete', 'JTOOLBAR_EMPTY_TRASH');
        }
        elseif ($canDo->get('core.edit.state'))
        {
            JToolbarHelper::trash('researchprojects.trash');
        }

        if ($user->authorise('core.admin', 'com_researchprojects') || $user->authorise('core.options', 'com_researchprojects'))
        {
            JToolbarHelper::preferences('com_researchprojects');
        }
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_RESEARCHPROJECTS_ADMINISTRATION'));
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array(
            'a.title' => JText::_('COM_RESEARCHPROJECTS_RECORDS_NAME'),
            'a.owner_user_id' => JText::_('COM_RESEARCHPROJECTS_RECORDS_OWNER'),
            'a.state' => JText::_('COM_RESEARCHPROJECTS_PUBLISHED'),
            'a.id'    => JText::_('COM_RESEARCHPROJECTS_ID')
        );
    }
}
