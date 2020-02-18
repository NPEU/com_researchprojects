<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/researchproject.php';

/**
 * ResearchProjects ResearchProject Form Model
 */
class ResearchProjectsModelResearchProjectform extends ResearchProjectsModelResearchProject
{
    /**
     * Model typeAlias string. Used for version history.
     *
     * @var    string
     * @since  3.2
     */
    public $typeAlias = 'com_researchprojects.researchproject';

    /**
     * Get the return URL.
     *
     * @return  string  The return URL.
     */
    public function getReturnPage()
    {
        return base64_encode($this->getState('return_page'));
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState()
    {
        $app = JFactory::getApplication();

        // Load state from the request.
        $pk = $app->input->getInt('id');
        $this->setState('researchproject.id', $pk);

        // Add compatibility variable for default naming conventions.
        $this->setState('form.id', $pk);

        $return = $app->input->get('return', null, 'base64');

        if (!JUri::isInternal(base64_decode($return)))
        {
            $return = null;
        }

        $this->setState('return_page', base64_decode($return));

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        $this->setState('layout', $app->input->getString('layout'));
    }
}
