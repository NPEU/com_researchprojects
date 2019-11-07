<?php
defined('_JEXEC') or die;

/**
 * ResearchProjects Component Category Tree
 */
class ResearchProjectsCategories extends JCategories
{
    /**
     * Constructor
     *
     * @param   array  $options  Array of options
     */
    public function __construct($options = array())
    {
        $options['table']      = '#__researchprojects';
        $options['extension']  = 'com_researchprojects';
        $options['statefield'] = 'state';
        parent::__construct($options);
    }
}