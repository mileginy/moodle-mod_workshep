<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Preview the assessment form.
 *
 * @package    mod_workshep
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$cmid     = required_param('cmid', PARAM_INT);
$cm       = get_coursemodule_from_id('workshep', $cmid, 0, false, MUST_EXIST);
$course   = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$workshep = $DB->get_record('workshep', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}
$workshep = new workshep($workshep, $cm, $course);

require_capability('mod/workshep:editdimensions', $workshep->context);
$PAGE->set_url($workshep->previewform_url());
$PAGE->set_title($workshep->name);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('editingassessmentform', 'workshep'), $workshep->editform_url(), navigation_node::TYPE_CUSTOM);
$PAGE->navbar->add(get_string('previewassessmentform', 'workshep'));
$currenttab = 'editform';

// load the grading strategy logic
$strategy = $workshep->grading_strategy_instance();

// load the assessment form
$mform = $strategy->get_assessment_form($workshep->editform_url(), 'preview');

// output starts here
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($workshep->name));
echo $OUTPUT->heading(get_string('assessmentform', 'workshep'), 3);
$mform->display();
echo $OUTPUT->footer();
