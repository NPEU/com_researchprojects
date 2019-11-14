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
use Joomla\Utilities\ArrayHelper;

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
            /*
            // Convert the topics field to a registry.
            $registry = new Registry;
            $registry->loadString($this->topics, 'JSON');

            $this->topics = $registry;
            */

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

            // Load the owner:
            $query = $this->_db->getQuery(true);
            $query->select('*')
                  ->from($this->_db->quoteName('#__users'))
                  ->where($this->_db->quoteName('id') . ' = ' . (int) $this->owner_user_id);
            $this->_db->setQuery($query);
            
            $this->owner_details = $this->_db->loadObject();

            // Load the topics.
            $query = $this->_db->getQuery(true);
            $query->select($this->_db->quoteName('t.id'))
                  ->select($this->_db->quoteName('t.title'))
                  ->from($this->_db->quoteName('#__researchprojects_topics') . ' AS t')
                  ->join('INNER', $this->_db->quoteName('#__researchprojects_topics_map') . ' AS m ON m.topic_id = t.id')
                  ->where($this->_db->quoteName('m.project_id') . ' = ' . (int) $this->id);
            $this->_db->setQuery($query);

            // Add the topics to the project data.
            $this->topics = $this->_db->loadAssocList('id', 'title');
            
            // Load the brand.
            $query = $this->_db->getQuery(true);
            $query->select('*')
                  ->from($this->_db->quoteName('#__brands'))
                  ->where($this->_db->quoteName('id') . ' = ' . (int) $this->brand_id);
            $this->_db->setQuery($query);
            
            $this->brand_details = $this->_db->loadObject();
            


            return true;
        }
        else
        {
            return false;
        }
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
        /*if (isset($array['topics']) && is_array($array['topics']))
        {
            // Convert the topics field to a string.
            $registry = new Registry;
            $registry->loadArray($array['topics']);
            $array['topics'] = (string) $registry;
        }*/

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

        // Attempt to bind the data.
        $return = parent::bind($array, $ignore);

        // Load the real topic data based on the bound ids.
        if ($return && !empty($this->topics))
        {
            // Set the topic ids.
            $this->topics = ArrayHelper::toInteger($this->topics);

            // Get the titles for the topics.
            $query = $this->_db->getQuery(true)
                ->select($this->_db->quoteName('id'))
                ->select($this->_db->quoteName('title'))
                ->from($this->_db->quoteName('#__researchprojects_topics'))
                ->where($this->_db->quoteName('id') . ' = ' . implode(' OR ' . $this->_db->quoteName('id') . ' = ', $this->topics));
            $this->_db->setQuery($query);

            #// Set the titles for the topics.
            #$this->topic = $this->_db->loadAssocList('id', 'title');
        }

        return $return;
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

        return true;
    }


    /**
     * Method to store a row in the database from the Table instance properties.
     *
     * If a primary key value is set the row with that primary key value will be updated with the instance property values.
     * If no primary key value is set a new row will be inserted into the database with the properties from the Table instance.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     */
    public function store($updateNulls = false)
    {
        $date = \JFactory::getDate();
		$user = \JFactory::getUser();


		if (!$this->id)
		{
			// New item
			$this->created    = $date->toSql();
            $this->created_by = $user->get('id');
		}

        // Attempt to store the data.
        $return = parent::store($updateNulls);

        // Store the topics if the project data was saved.
        if (is_array($this->topics) && count($this->topics))
        {
            $query = $this->_db->getQuery(true);

            // Remove existing associations, since we're adding them all again anyway:
            $query -> delete($this->_db->quoteName('#__researchprojects_topics_map'))
                   -> where($this->_db->quoteName('project_id') . ' = ' . (int) $this->id);
            $this->_db->setQuery($query);
            $this->_db->execute();

            // Added topic associations:
            $query->clear()
                  ->insert($this->_db->quoteName('#__researchprojects_topics_map'))
                  ->columns(array($this->_db->quoteName('project_id'), $this->_db->quoteName('topic_id')));

            // Have to break this up into individual queries for cross-database support.
            foreach ($this->topics as $topic)
            {
                $query->clear('values')
                       ->values($this->id . ', ' . $topic);
                $this->_db->setQuery($query);
                $this->_db->execute();
            }
        }


        #echo 'store<pre>'; var_dump($return); echo '</pre>'; exit;
        return $return;
    }

    /**
     * Method to delete a project, project topics, and any other necessary data from the database.
     *
     * @param   integer  $project_id  An optional projectTopics id.
     *
     * @return  boolean  True on success, false on failure.
     */
    public function delete($project_id = null)
    {
        // Attempt to delete the data.
        $return = parent::delete($project_id);

        // Store the topics.
        if ($return && !empty($this->topics))
        {
            $query = $this->_db->getQuery(true);

            $query -> delete($this->_db->quoteName('#__researchprojects_topics_map'))
                   -> where($this->_db->quoteName('project_id') . ' = ' . (int) $this->id);
            $this->_db->setQuery($query);
            $this->_db->execute();
        }

        return $return;
    }
}
