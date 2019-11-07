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
 * ResearchProjects ResearchProject Controller
 */
class ResearchProjectsControllerResearchProject extends JControllerForm
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     \JControllerLegacy
     * @throws  \Exception
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->view_list = 'researchprojects';
    }
}
