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
 * ResearchProjectsHelper component helper.
 */
class ResearchProjectsHelper extends JHelperContent
{   
    /**
	 * Method parse a collaborator string into an array
	 *
     * @param string    The collaborator string.
	 * @return  array   The field input markup.
	 */
    public static function parseCollaborator($str)
    {
        // E.g. Abalos, Edgardo (Queensland Institute of Medical Research, Brisbane, Australia)
        // Also has the option to have URL in [], so lets extract that first:
        $url1 = strpos($str, '[');
        $url2 = strrpos($str, ']');
        
        $url = '';
        if ($url1 && $url2) {
            $url = substr($str, $url1 + 1, ($url2 - $url1 - 1));
            $str = trim(str_replace('[' . $url . ']', '', $str));
        }
        
        // Look for the first open bracket after the name, as determined by the first space after
        // the first comma.
        $institution1 = strpos($str, '(', strpos($str, ' ', strpos($str, ',')));
        $institution2 = strrpos($str, ')');
        
        // Extract the institution:
        $institution = '';
        if ($institution1 && $institution2) {
            
            $institution = substr($str, $institution1 + 1, ($institution2 - $institution1 - 1));
            $str = trim(str_replace('(' . $institution . ')', '', $str));
        }
        
        // Really, there should be a comma in the remaining text, separating last name, first name,
        // but this isn't (and shouldn't be) enforced, so check for presence of comma first:
        if (strpos($str, ',')) {
            $name = explode(',', $str);
        } else {
            $name = array('', $str);
        }
        return array(
            'first_name'  => trim($name[1]),
            'last_name'   => trim($name[0]),
            'institution' => $institution,
            'url'         => $url
        );
        
    }
}
