<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2014 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NPEU\Component\Researchprojects\Site\Service;

// phpcs:disable PSR1.Files.SideEffects
\defined('JPATH_PLATFORM') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;

/**
 * RouterRules interface for Joomla
 *
 * @since  3.4
 */
class CustomRouterRules implements \Joomla\CMS\Component\Router\Rules\RulesInterface
{
    /**
     * Prepares a query set to be handed over to the build() method.
     * This should complete a partial query set to work as a complete non-SEFed
     * URL and in general make sure that all information is present and properly
     * formatted. For example, the Itemid should be retrieved and set here.
     *
     * @param   array  &$query  The query array to process
     *
     * @return  void
     *
     * @since   3.4
     */
    public function preprocess(&$query) {
    }

    /**
     * Parses a URI to retrieve information for the right route through the component.
     * This method should retrieve all its input from its method arguments.
     *
     * @param   array  &$segments  The URL segments to parse
     * @param   array  &$vars      The vars that result from the segments
     *
     * @return  void
     *
     * @since   3.4
     */
    public function parse(&$segments, &$vars) {
        $n_segments = count($segments);
        if (isset($vars['view']) && $vars['view'] == 'researchproject' && isset($vars['id']) && $n_segments == 0) {
            if ($topic_id = $this->topic_exists($vars['id'])) {
                $vars['view'] = 'researchprojects';
                $vars['topic_id'] = $topic_id;
            }

            if (isset($segments[0]) && $this->project_exists((int) $segments[0])) {
                $vars['view'] = 'researchproject';
                $vars['id'] = (int) $segments[0];
            }

        }

        return $vars;
    }

    /**
     * Builds URI segments from a query to encode the necessary information for a route in a human-readable URL.
     * This method should retrieve all its input from its method arguments.
     *
     * @param   array  &$query     The vars that should be converted
     * @param   array  &$segments  The URL segments to create
     *
     * @return  void
     *
     * @since   3.4
     */
    public function build(&$query, &$segments) {
    }

    /**
     * Method to check a record exists.
     *
     * @param   int     $id   The record ID
     *
     * @return  bool    Project does/does not exist.
     */
    protected function project_exists($id)
    {
        $db = Factory::getDbo();
        #echo 'project_exists<pre>'; var_dump($id); echo '</pre>'; exit;
        $dbQuery = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__researchprojects'))
            ->where($db->quoteName('id') . ' = ' . $id);
        $db->setQuery($dbQuery);

        $project_id = $db->loadResult();
        return (bool) $project_id;
    }

    /**
     * Method to check a topic exists.
     *
     * @param   int     $id   The record ID
     *
     * @return  bool    Record does/does not exist.
     */
    protected function topic_exists($alias)
    {
        $db = Factory::getDbo();
        $dbQuery = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__researchprojects_topics'))
            ->where($db->quoteName('alias') . ' = ' . $db->quote($alias));
        $db->setQuery($dbQuery);

        $topic_id = $db->loadResult();

        return $topic_id;
    }
}
