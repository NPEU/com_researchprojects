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
use Joomla\String\StringHelper;

/**
 * ResearchProjects ResearchProject Model
 */
class ResearchProjectsModelResearchProject extends JModelAdmin
{
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type    The table name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable  A JTable object
     */
    public function getTable($type = 'ResearchProjects', $prefix = 'ResearchProjectsTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed    A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_researchprojects.researchproject',
            'researchproject',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form))
        {
            return false;
        }

        // Modify the form based on access controls.
        if (!$this->canEditState((object) $data))
        {
            // Disable fields for display.
            $form->setFieldAttribute('state', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('state', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState(
            'com_researchprojects.edit.researchproject.data',
            array()
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed  Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if (empty($item) || is_null($item->id)) {
            return $item;
        }

        $registry = new Registry;
        $registry->loadArray($item->topics);
        $item->topics = $registry->toArray();

        // we need to convert the topics field to an array so the form select displays the
        // correct values, but we don't want to lose the titles, so copying it over:
        $item->topic_details = $item->topics;
        $item->topics = array_keys($item->topics);

        // Convert the collaborators field to an array.
        $registry = new Registry;
        $registry->loadString($item->collaborators);
        $item->collaborators = $registry->toArray();

        // Convert the funders field to an array.
        $registry = new Registry;
        $registry->loadString($item->funders);
        $item->funders = $registry->toArray();

        return $item;
    }

    /**
     * Prepare and sanitise the table data prior to saving.
     *
     * @param   JTable  $table  A reference to a JTable object.
     *
     * @return  void
     */
    protected function prepareTable($table)
    {
        $date = JFactory::getDate();
        $user = JFactory::getUser();

        $table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
        $table->alias = JApplicationHelper::stringURLSafe($table->alias);

        if (empty($table->alias))
        {
            $table->alias = JApplicationHelper::stringURLSafe($table->title);
        }

        $table->modified    = $date->toSql();
        $table->modified_by = $user->id;

        if (empty($table->id))
        {
            $table->created    = $date->toSql();
            $table->created_by = $user->id;
        }
    }

    /**
     * Method to prepare the saved data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     */
    public function save($data)
    {
        $is_new = empty($data['id']);
        $app    = JFactory::getApplication();
        $input  = JFactory::getApplication()->input;
        $db     = JFactory::getDBO();

        // Get parameters:
        $params = JComponentHelper::getParams(JRequest::getVar('option'));

        // Special handling for pis and collaborators. We need to add to the collaborators table all new
        // values.
        // Pillay, Thillagavathie (Royal Wolverhampton NHS Trust)
        // Building something like this query:
        // REPLACE INTO `#__researchprojects_collaborators` VALUES ('Chris Gale (Imperial)'), ('Alex Heazell (Manchester)'), ('Tim Smith');
        $new_investigators = array();

        if (!empty($data['pi_1'])) {
            // If not NPEU: e.g. Fiona Alderdice (NPEU)
            if (!preg_match('/\(NPEU\)$/', $data['pi_1'])) {
                $new_investigators[] = '("' . md5($data['pi_1']) . '", "' . $data['pi_1'] . '")';
            }
        }

        if (!empty($data['pi_2'])) {
            // If not NPEU: e.g. Fiona Alderdice (NPEU)
            if (!preg_match('/\(NPEU\)$/', $data['pi_2'])) {
                $new_investigators[] = '("' . md5($data['pi_2']) . '", "' . $data['pi_2'] . '")';
            }
        }

        if (!empty($data['collaborators'])) {
            foreach ($data['collaborators'] as $key => $value) {
                if (empty($value['collaborator'])) {
                    unset($data['collaborators'][$key]);
                    continue;
                }
                // If not NPEU: e.g. Fiona Alderdice (NPEU)
                if (preg_match('/\(NPEU\)$/', $value['collaborator'])) {
                    continue;
                }
                $new_investigators[] = '("' . md5($value['collaborator']) . '", "' . $value['collaborator'] . '")';
            }
        }

        if (!empty($new_investigators)) {
            $q = 'REPLACE INTO `#__researchprojects_collaborators` VALUES ' . implode(", ", $new_investigators) . ';';
            $db->setQuery($q);
            if (!$db->execute($q)) {
                JError::raiseError( 500, $db->stderr() );
                return false;
            }
        }

        // Special handling for funders. We need to add to the funders table all new values.
        $new_funders = array();
        if (!empty($data['funders']['funders0']['funder'])) {
            foreach ($data['funders'] as $key => $value) {
                // If not NPEU: e.g. Fiona Alderdice (NPEU)
                if (preg_match('/\(NPEU\)$/', $value['funder'])) {
                    continue;
                }
                $new_funders[] = '("' . md5($value['funder']) . '", "' . $value['funder'] . '")';
            }
        }
        if (!empty($new_funders)) {
            $q = 'REPLACE INTO `#__researchprojects_funders` VALUES ' . implode(", ", $new_funders) . ';';
            $db->setQuery($q);
            if (!$db->execute($q)) {
                JError::raiseError( 500, $db->stderr() );
                return false;
            }
        }

        // Alter the title for save as copy
        if ($input->get('task') == 'save2copy')
        {
            list($title, $alias) = $this->generateNewTitle(0, $data['alias'], $data['title']);
            $data['title']    = $title;
            $data['alias']    = $alias;
            $data['state']    = 0;
        }

        // Automatic handling of alias for empty fields
        // Taken from com_content/models/article.php
        if (in_array($input->get('task'), array('apply', 'save', 'save2new'))) {
            if (empty($data['alias'])) {
                if (JFactory::getConfig()->get('unicodeslugs') == 1) {
                    $data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['title']);
                } else {
                    $data['alias'] = JFilterOutput::stringURLSafe($data['title']);
                }

                $table = JTable::getInstance('ResearchProjects', 'ResearchProjectsTable');

                if ($table->load(array('alias' => $data['alias']))) {
                    $msg = JText::_('COM_CONTENT_SAVE_WARNING');
                }

                #list($title, $alias) = $this->generateNewResearchProjectsTitle($data['alias'], $data['title']);
                list($title, $alias) = $this->generateNewTitle(0, $data['alias'], $data['title']);
                $data['alias'] = $alias;

                if (isset($msg)) {
                    JFactory::getApplication()->enqueueMessage($msg, 'warning');
                }
            }
        }

        return parent::save($data);
    }

    /**
     * Method to change the title & alias.
     *
     * @param   integer  $category_id  The id of the parent.
     * @param   string   $alias        The alias.
     * @param   string   $name         The title.
     *
     * @return  array  Contains the modified title and alias.
     */
    protected function generateNewTitle($category_id, $alias, $name)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias)))
        {
            if ($name == $table->title)
            {
                $name = JString::increment($name);
            }

            $alias = JString::increment($alias, 'dash');
        }

        return array($name, $alias);
    }

    /**
     * Copied from libraries/src/MVC/Model/AdminModel.php because it uses a hard-coded field name:
     * catid.
     *
     * Method to change the title & alias.
     *
     * @param   string   $alias        The alias.
     * @param   string   $title        The title.
     *
     * @return  array  Contains the modified title and alias.
     */
    /*protected function generateNewResearchProjectsTitle($alias, $title)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias)))
        {
            $title = StringHelper::increment($title);
            $alias = StringHelper::increment($alias, 'dash');
        }

        return array($title, $alias);
    }*/


    /**
     * Method to get the script that have to be included on the form
     *
     * @return string   Script files
     */
    /*public function getScript()
    {
        #return 'administrator/components/com_helloworld/models/forms/helloworld.js';
        return '';
    }*/

    /**
     * Delete this if not needed. Here for reference.
     * Method to get the data that should be injected in the form.
     *
     * @return  bool  Email success/failed to send.
     */
    /*private function _sendEmail($email_data)
    {
            $app        = JFactory::getApplication();
            $mailfrom   = $app->getCfg('mailfrom');
            $fromname   = $app->getCfg('fromname');
            $sitename   = $app->getCfg('sitename');
            $email      = JStringPunycode::emailToPunycode($email_data['email']);

            // Ref: JText::sprintf('LANG_STR', $var, ...);

            $mail = JFactory::getMailer();
            $mail->addRecipient($email);
            $mail->addReplyTo($mailfrom);
            $mail->setSender(array($mailfrom, $fromname));
            $mail->setSubject(JText::_('COM_ALERTS_EMAIL_ADMINS_SUBJECT'));
            $mail->setBody(JText::_('COM_ALERTS_EMAIL_ADMINS_BODY'));
            $sent = $mail->Send();

            return $sent;
    }*/
}
