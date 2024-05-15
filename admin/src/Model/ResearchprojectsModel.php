<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Researchprojects\Administrator\Model;

defined('_JEXEC') or die;


use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

/**
 * Researchprojects List Model
 */
class ResearchprojectsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JController
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'alias', 'a.alias',
                'params', 'a.params',
                'state', 'a.state',
                'owner_user_id', 'a.owner_user_id',
                'o.name', 'owner_name',
                'o.username', 'owner_username',
                'o.email', 'owner_email',
                'created', 'a.created',
                'created_by', 'a.created_by',
                'modified', 'a.modified',
                'modified_by', 'a.modified_by',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'access', 'a.access'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @note    Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = 'a.title', $direction = 'ASC')
    {
        // Load the filter state.
        $this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
        $this->setState('filter.published', $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string'));

        // Load the parameters.
        $params = ComponentHelper::getParams('com_researchprojects');
        $this->setState('params', $params);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string  $id  A prefix for the store id.
     *
     * @return  string  A store id.
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');

        return parent::getStoreId($id);
    }

    /**
     * Gets the list of projects and adds expensive joins to the result set.
     *
     * @return      mixed  An array of data items on success, false on failure.
     */
    public function getItems()
    {
        // Get a storage key.
        $store = $this->getStoreId();

        // Try to load the data from internal storage.
        if (empty($this->cache[$store])) {
            $topics  = $this->getState('filter.topics');
            $topicId = $this->getState('filter.topic_id');

            if (isset($topics) && (empty($topics) || $topicId && !in_array($topicId, $topics))) {
                $items = array();
            } else {
                $items = parent::getItems();
            }

            // Bail out on an error or empty list.
            if (empty($items)) {
                $this->cache[$store] = $items;

                return $items;
            }

            // Joining the topics with the main query is a performance hog.
            // Find the information only on the result set.

            // First pass: get list of the project id's and reset the counts.
            $projectIds = array();

            foreach ($items as $item) {
                $projectIds[] = (int) $item->id;
                $item->topic_count = 0;
                $item->topic_names = '';
                $item->note_count = 0;
            }

            // Get the counts from the database only for the projects in the list.
            $db    = $this->getDbo();
            $query = $db->getQuery(true);

            // Join over the topic mapping table.
            $query->select('map.project_id, COUNT(map.topic_id) AS topic_count')
                ->from('#__researchprojects_topics_map AS map')
                ->where('map.project_id IN (' . implode(',', $projectIds) . ')')
                ->group('map.project_id')
                // Join over the project topics table.
                ->join('LEFT', '#__researchprojects_topics AS g2 ON g2.id = map.topic_id');

            $db->setQuery($query);

            // Load the counts into an array indexed on the project id field.
            try {
                $projectTopics = $db->loadObjectList('project_id');
            } catch (RuntimeException $e) {
                $this->setError($e->getMessage());

                return false;
            }

            // Second pass: collect the topic counts into the master items array.
            foreach ($items as &$item) {
                if (isset($projectTopics[$item->id])) {
                    $item->topic_count = $projectTopics[$item->id]->topic_count;

                    // Topic_concat in other databases is not supported
                    $item->topic_names = $this->_getProjectDisplayedTopics($item->id);
                }
            }

            // Add the items to the internal cache.
            $this->cache[$store] = $items;
        }

        return $this->cache[$store];
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        // Initialize variables.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.alias, a.catid, a.owner_user_id, a.checked_out, a.checked_out_time, a.created, a.created_by, a.state'
            )
        );
        $query->from($db->quoteName('#__researchprojects', 'a'));

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor')
            ->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');

        // Join over the users for the owner user.
        $query->select($db->quoteName('o.name', 'owner_name'))
            ->select($db->quoteName('o.username', 'owner_username'))
            ->select($db->quoteName('o.email', 'owner_email'))
            ->join('LEFT', $db->quoteName('#__users', 'o') . ' ON ' . $db->qn('o.id') . ' = ' . $db->qn('a.owner_user_id'));


        // Filter the items over the topic id if set.
        $topicId = $this->getState('filter.topic_id');

        if ($topicId) {
            $query->join('LEFT', '#__researchprojects_topics_map AS map2 ON map2.project_id = a.id')
                ->group(
                    $db->quoteName(
                        array(
                            'a.id',
                            'a.title'
                        )
                    )
                );
            $query->where('map2.topic_id = ' . (int) $topicId);
        }

        // Filter: like / search
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            $like = $db->quote('%' . $search . '%');
            $query->where('a.title LIKE ' . $like);
            $query->where('a.alias LIKE ' . $like);
        }

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            $query->where($db->quoteName('a.state') . ' = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(' . $db->quoteName('a.state') . ' IN (0, 1))');
        }

        // Filter: owner
        $owner = $this->getState('filter.owner_user_id');

        if (!empty($owner)) {
            $query->where('a.owner_user_id = ' . $owner);
        }

        // Add the list ordering clause.
        $orderCol   = $this->state->get('list.ordering', 'a.title');
        $orderDirn  = $this->state->get('list.direction', 'ASC');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    /**
     *
     * @param   integer  $project_id  User identifier
     *
     * @return  string   Groups titles imploded :$
     */
    protected function _getProjectDisplayedTopics($project_id)
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true)
            ->select($db->qn('title'))
            ->from($db->qn('#__researchprojects_topics', 'pt'))
            ->join('LEFT', $db->qn('#__researchprojects_topics_map', 'map') . ' ON (pt.id = map.topic_id)')
            ->where($db->qn('map.project_id') . ' = ' . (int) $project_id);

        try {
            $result = $db->setQuery($query)->loadColumn();
        } catch (RunTimeException $e) {
            $result = array();
        }

        return implode("\n", $result);
    }
}
