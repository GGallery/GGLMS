<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 25/11/2020
 * Time: 12:15
 */

// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');

class JFormFieldlistacolssummaryreport extends JFormFieldList {

    /**
     * A flexible category list that respects access controls
     *
     * @var		string
     * @since	1.6
     */
    public $type = 'listacolssummaryreport';

    /**
     * Method to get a list of categories that respects access controls and can be used for
     * either category assignment or parent category assignment in edit screens.
     * Use the parent element to indicate that the field will be used for assigning parent categories.
     *
     * @return	array	The field option objects.
     * @since	1.6
     */

    protected function getOptions() {
        // Initialise variables.
        $options = gglmsHelper::GetSummaryReportColumns();

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        // pre-select values
        //$this->value = array();

        return $options;
    }
}
