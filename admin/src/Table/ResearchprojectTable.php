<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Researchprojects\Administrator\Table;

defined('_JEXEC') or die;

#use Joomla\CMS\Tag\TaggableTableInterface;
#use Joomla\CMS\Tag\TaggableTableTrait;
#use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Nested;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;


/**
 * Researchproject Table class.
 *
 * @since  1.0
 */
#class ResearchprojectTable extends Nested implements VersionableTableInterface, TaggableTableInterface
class ResearchprojectTable extends Table
{
    #use TaggableTableTrait;

    /**
     * Ensure the relevant fields are json encoded in the bind method
     *
     * @var    array
     * @since  3.4
     */
    #protected $_jsonEncode = array('params', 'collaborators', 'funders');

    /**
     * Array with alias for "special" columns such as ordering, hits etc etc
     * Note the admin listing uses 'published' in the special un/publish switcher, so the
     * published/state alias is neeed for that at least.
     *
     * @var    array
     * @since  3.4.0
     */
    protected $_columnAlias = [
        'published' => 'state'
    ];

    public function __construct(DatabaseDriver $db) {
        $this->typeAlias = 'com_researchprojects.researchproject';

        parent::__construct('#__researchprojects', 'id', $db);

        // In functions such as generateTitle() Joomla looks for the 'title' field ...
        #$this->setColumnAlias('title', 'greeting');
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
        if (parent::load($pk, $reset)) {
            // Convert the collaborators field to a registry.
            $registry = new Registry;
            $registry->loadString($this->collaborators, 'JSON');

            $this->collaborators = $registry->toObject();

            // Convert the funders field to a registry.
            $registry = new Registry;
            $registry->loadString($this->funders, 'JSON');

            $this->funders = $registry->toObject();

            // Convert the params field to a registry.
            $registry = new Registry;
            $registry->loadString($this->params, 'JSON');

            $this->params = $registry->toObject();

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
            #$this->topics = $this->_db->loadObjectList();
            #echo '<pre>'; var_dump($this->topics); echo '</pre>'; exit;

            // Load the brand.
            $query = $this->_db->getQuery(true);
            $query->select('*')
                  ->from($this->_db->quoteName('#__brands'))
                  ->where($this->_db->quoteName('id') . ' = ' . (int) $this->brand_id);
            $this->_db->setQuery($query);

            $this->brand_details = $this->_db->loadObject();

            #echo '<pre>'; var_dump($this->brand_details); echo '</pre>'; exit;

            return true;
        } else {
            return false;
        }
    }

    public function bind($array, $ignore = '') {

        if (isset($array['pi_1'])) {
            // Convert the "fake" comma back to real one
            $array['pi_1'] = str_replace('﹐', ',', $array['pi_1']);
        }

        if (isset($array['pi_2'])) {
            // Convert the "fake" comma back to real one

            $array['pi_2'] = str_replace('﹐', ',', $array['pi_2']);
        }

        if (isset($array['collaborators']) && is_array($array['collaborators'])) {
            // Convert the collaborators field to a string.
            $registry = new Registry;
            $registry->loadArray($array['collaborators']);

            // Convert the "fake" comma back to real one
            $array['collaborators'] = str_replace('\ufe50', ',', (string) $registry);
        }

        if (isset($array['funders']) && is_array($array['funders'])) {
            // Convert the funders field to a string.
            $registry = new Registry;
            $registry->loadArray($array['funders']);

            // Convert the "fake" comma back to real one
            $array['funders'] = str_replace('\ufe50', ',', (string) $registry);
        }

        if (isset($array['params']) && is_array($array['params'])) {
            // Convert the params field to a string.
            $registry = new Registry;
            $registry->loadArray($array['params']);

            // Convert the "fake" comma back to real one
            $array['params'] = str_replace('\ufe50', ',', (string) $registry);
        }

        // Bind the rules.
        if (isset($array['rules']) && \is_array($array['rules'])) {
            $rules = new Rules($array['rules']);
            $this->setRules($rules);
        }

        return parent::bind($array, $ignore);
    }

    public function store($updateNulls = true) {
        // add the 'created by' and 'created' date fields if it's a new record
        // and these fields aren't already set
        $date = date('Y-m-d h:i:s');
        $user_id = Factory::getApplication()->getIdentity()->get('id');

        if (!$this->id) {
            // new record
            if (empty($this->created_by)) {
                $this->created_by = $user_id;
                $this->created    = $date;
            }
        }

        $input  = Factory::getApplication()->input;
        $form_data = new Registry($input->get('jform', '', 'array'));

        $return = parent::store();

        // Store the topics if the project data was saved.
        if ($return) {

            $topics = $form_data->get('topics', false);

            if (is_array($topics) && count($topics)) {
                $query = $this->_db->getQuery(true);

                // Remove existing associations, since we're adding them all again anyway:
                $query->delete($this->_db->quoteName('#__researchprojects_topics_map'))
                      ->where($this->_db->quoteName('project_id') . ' = ' . (int) $this->id);
                $this->_db->setQuery($query);
                $this->_db->execute();

                // Have to break this up into individual queries for cross-database support.
                foreach ($topics as $topic_id) {
                    $query->clear();
                    $query->insert($this->_db->quoteName('#__researchprojects_topics_map'))
                          ->columns(array($this->_db->quoteName('project_id'), $this->_db->quoteName('topic_id')))
                          ->values($this->id . ', ' . $topic_id);
                    $this->_db->setQuery($query);
                    $this->_db->execute();
                }
            }
        }

        return $return;
    }

    /**
     * Method to compute the default name of the asset.
     * The default name is in the form `table_name.id`
     * where id is the value of the primary key of the table.
     *
     * @return    string
     * @since    2.5
     */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_researchprojects.researchproject.'.(int) $this->$k;
    }
    /**
     * Method to return the title to use for the asset table.
     *
     * @return    string
     * @since    2.5
     */
    protected function _getAssetTitle() {
        return $this->title;
    }

    public function check() {
        $this->alias = trim($this->alias);
        if (empty($this->alias)) {
            $this->alias = $this->greeting;
        }
        $this->alias = OutputFilter::stringURLSafe($this->alias);

        // Check for valid name
        if (trim($this->title) == '') {
            $this->setError(Text::_('COM_RESEARCHPROJECTS_ERR_TABLES_TITLE'));
            return false;
        }

        // Check for existing name
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__researchprojects'))
            ->where($db->quoteName('title') . ' = ' . $db->quote($this->title));
        $db->setQuery($query);

        $xid = (int) $db->loadResult();

        if ($xid && $xid != (int) $this->id) {
            $this->setError(Text::_('COM_RESEARCHPROJECTS_ERR_TABLES_NAME'));

            return false;
        }

        if (empty($this->alias)) {
            $this->alias = $this->title;
        }

        $this->alias = ApplicationHelper::stringURLSafe($this->alias);

        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = Factory::getDate()->format("Y-m-d-H-i-s");
        }

        return true;
    }

    public function delete($pk = null, $children = false) {
        return parent::delete($pk, $children);

        // Delete the topics.
        if ($return && !empty($this->topics)) {
            $query = $this->_db->getQuery(true);

            $query -> delete($this->_db->quoteName('#__researchprojects_topics_map'))
                    -> where($this->_db->quoteName('project_id') . ' = ' . (int) $this->id);
            $this->_db->setQuery($query);
            $this->_db->execute();
        }
    }

    /**
     * typeAlias is the key used to find the content_types record
     * needed for creating the history record
     */
    public function getTypeAlias() {
        return $this->typeAlias;
    }
}
