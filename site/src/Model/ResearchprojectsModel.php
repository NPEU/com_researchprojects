<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Researchprojects\Site\Model;

use Joomla\CMS\Factory;

use NPEU\Component\Researchprojects\Administrator\Helper\ResearchprojectsHelper;

defined('_JEXEC') or die;

/**
 * Researchproject Component Model
 */
class ResearchprojectsModel extends \NPEU\Component\Researchprojects\Administrator\Model\ResearchprojectsModel {
    protected $published = 1;

    /**
     * Gets the list of projects and adds expensive joins to the result set.
     *
     * @return  mixed  An array of data items on success, false on failure.
     */
    public function getItems() {

        $items = parent::getItems();
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        $t = [];
        foreach ($items as &$item) {
            // Add topics:
            $query->clear()
                  ->select('*')
                  ->from($db->quoteName('#__researchprojects_topics', 'pt'))
                  ->join('LEFT', $db->quoteName('#__researchprojects_topics_map', 'map') . ' ON (pt.id = map.topic_id)')
                  ->where($db->quoteName('map.project_id') . ' = ' . (int) $item->id);

                  $topics = $db->setQuery($query)->loadObjectList('id');
            $item->topics = $topics;

            // Add parsed pi/collaborator format:
            $item->pi_1_parsed = ResearchProjectsHelper::parseCollaborator($item->pi_1);

            if (!empty($item->pi_2)) {
                $item->pi_2_parsed = ResearchProjectsHelper::parseCollaborator($item->pi_2);
            }
        }
        return $items;
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery() {
        $app = Factory::getApplication();
        $topic = $app->input->getInt('topic');

        $this->setState('list.limit', 0);
        // Initialize variables.
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);

        // Create the select statement.
        $query->select('*')
              ->from($db->quoteName('#__researchprojects', 'a'));

        // Filter the items over the topic id if set.
        $topicId = $app->input->getInt('topic_id');

        if ($topicId) {
            $query->join('LEFT', '#__researchprojects_topics_map AS map2 ON map2.project_id = a.id')
                ->group(
                    $db->quoteName(
                        [
                            'a.id',
                            'a.title'
                        ]
                    )
                );
            $query->where('map2.topic_id = ' . (int) $topicId);
        }

        $query->order($db->escape('a.title'));

        return $query;
    }

}