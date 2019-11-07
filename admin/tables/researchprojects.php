<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * ResearchProjects Table class
 */
class ResearchProjectsTableResearchProjects extends JTable
{
    /**
     * Ensure the relevant fields are json encoded in the bind method
     *
     * @var    array
     * @since  3.4
     */
    protected $_jsonEncode = array('params', 'collaborators', 'funders');

    /**
     * Constructor
     *
     * @param   JDatabaseDriver  &$db  A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__researchprojects', 'id', $db);

        // Set the alias since the column is called state
        $this->setColumnAlias('published', 'state');
    }

    /**
     * Overloaded check method to ensure data integrity.
     *
     * @return  boolean  True on success.
     */
    public function check()
    {
        // Check for valid name
        if (trim($this->title) == '')
        {
            $this->setError(JText::_('COM_RESEARCHPROJECTS_ERR_TABLES_TITLE'));
            return false;
        }

        // Check for existing name
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__researchprojects'))
            ->where($db->quoteName('title') . ' = ' . $db->quote($this->title));
            #->where($db->quoteName('catid') . ' = ' . (int) $this->catid);
        $db->setQuery($query);

        $xid = (int) $db->loadResult();

        if ($xid && $xid != (int) $this->id)
        {
            $this->setError(JText::_('COM_RESEARCHPROJECTS_ERR_TABLES_NAME'));

            return false;
        }

        if (empty($this->alias))
        {
            $this->alias = $this->title;
        }

        $this->alias = JApplicationHelper::stringURLSafe($this->alias);

        if (trim(str_replace('-', '', $this->alias)) == '')
        {
            $this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
        }

        // Check the publish down date is not earlier than publish up.
        if ($this->publish_down > $db->getNullDate() && $this->publish_down < $this->publish_up)
        {
            $this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));

            return false;
        }

        return true;
    }

    /**
     * Overloaded bind function
     *
     * @param       array           named array
     * @return      null|string     null is operation was satisfactory, otherwise returns an error
     * @see JTable:bind
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['topics']) && is_array($array['topics']))
        {
            // Convert the topics field to a string.
            $registry = new Registry;
            $registry->loadArray($array['topics']);
            $array['topics'] = (string) $registry;
        }

        if (isset($array['collaborators']) && is_array($array['collaborators']))
        {
            // Convert the collaborators field to a string.
            $registry = new Registry;
            $registry->loadArray($array['collaborators']);
            $array['collaborators'] = (string) $registry;
        }
        
        if (isset($array['funders']) && is_array($array['funders']))
        {
            // Convert the funders field to a string.
            $registry = new Registry;
            $registry->loadArray($array['funders']);
            $array['funders'] = (string) $registry;
        }
        
        if (isset($array['params']) && is_array($array['params']))
        {
            // Convert the params field to a string.
            $registry = new Registry;
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }
        
        return parent::bind($array, $ignore);
    }

    /**
     * Overloaded load function
     *
     * @param       int $pk primary key
     * @param       boolean $reset reset data
     * @return      boolean
     * @see JTable:load
     */
    public function load($pk = null, $reset = true)
    {
        if (parent::load($pk, $reset))
        {
            // Convert the topics field to a registry.
            $registry = new Registry;
            $registry->loadString($this->topics, 'JSON');

            $this->topics = $registry;
            
            // Convert the collaborators field to a registry.
            $registry = new JRegistry;
            $registry->loadString($this->collaborators, 'JSON');

            $this->collaborators = $registry;
            
            // Convert the funders field to a registry.
            $registry = new JRegistry;
            $registry->loadString($this->funders, 'JSON');

            $this->funders = $registry;
            
            // Convert the params field to a registry.
            $registry = new Registry;
            $registry->loadString($this->params, 'JSON');

            $this->params = $registry;
            
            return true;
        }
        else
        {
            return false;
        }
    }
}
