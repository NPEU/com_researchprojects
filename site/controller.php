<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

/**
 * ResearchProjects Component Controller
 */
class ResearchProjectsController extends JControllerLegacy
{
    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe url parameters and their variable types,
     *                               for valid values see {@link JFilterInput::clean()}.
     *
     * @return  ResearchProjectsController   This object to support chaining.
     *
     */
    public function display($cachable = false, $urlparams = false)
    {
        #$cachable  = true; // Huh? Why not just put that in the constructor?
        #$user      = JFactory::getUser();

        // Set the default view name and format from the Request.
        // Note we are using r_id to avoid collisions with the router and the return page.
        // Frontend is a bit messier than the backend.
        $id    = $this->input->getInt('r_id');
        $vName = $this->input->get('view', 'researchprojects');
        $safeurlparams = array(
            'id'                => 'INT',
            'limit'             => 'UINT',
            'limitstart'        => 'UINT',
            'filter_order'      => 'CMD',
            'filter_order_Dir'  => 'CMD',
            'lang'              => 'CMD'
        );

        // Check for edit form.
        if ($vName == 'form' && !$this->checkEditId('com_researchprojects.edit.researchproject', $id))
        {
            // Somehow the person just went to the form - we don't allow that.
            return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
        }

        return parent::display($cachable, $safeurlparams);
    }
}
