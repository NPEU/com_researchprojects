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
 * ResearchProjects Controller
 */
class ResearchProjectsControllerResearchProjects extends JControllerAdmin
{
    /**
     * Proxy for getModel.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  object  The model.
     */
    public function getModel($name = 'ResearchProject', $prefix = 'ResearchProjectsModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
