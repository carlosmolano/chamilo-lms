<?php
/* For licensing terms, see /license.txt */

require_once '../inc/global.inc.php';

$allow = api_get_configuration_value('extra');
if (empty($allow)) {
    exit;
}

/**
 * Implements the tracking of students in the Reporting pages
 * @package chamilo.mySpace
 */

// name of the language file that needs to be included
$language_file = array('registration', 'index', 'tracking', 'exercice', 'admin', 'gradebook');


require '../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'tracking.lib.php';
require_once api_get_path(LIBRARY_PATH).'export.lib.inc.php';
require_once api_get_path(LIBRARY_PATH).'usermanager.lib.php';
require_once api_get_path(LIBRARY_PATH).'course.lib.php';
require_once api_get_path(SYS_CODE_PATH).'lp/learnpath.class.php';
require_once api_get_path(SYS_CODE_PATH).'mySpace/myspace.lib.php';
require_once api_get_path(SYS_CODE_PATH).'gradebook/lib/be/gradebookitem.class.php';
require_once api_get_path(SYS_CODE_PATH).'gradebook/lib/be/evaluation.class.php';
require_once api_get_path(SYS_CODE_PATH).'gradebook/lib/be/result.class.php';
require_once api_get_path(SYS_CODE_PATH).'gradebook/lib/be/linkfactory.class.php';
require_once api_get_path(SYS_CODE_PATH).'gradebook/lib/be/category.class.php';
require_once api_get_path(LIBRARY_PATH).'attendance.lib.php';
require_once api_get_path(LIBRARY_PATH).'sessionmanager.lib.php';




$from_myspace = false;
if (isset ($_GET['from']) && $_GET['from'] == 'myspace') {
	$from_myspace = true;
	$this_section = SECTION_TRACKING;
} else {
	$this_section = SECTION_COURSES;
}

//$nameTools = get_lang('StudentDetails');
$cidReset = true;
$get_course_code = Security :: remove_XSS($_GET['course']);
if (isset ($_GET['details'])) {
	if (!empty ($_GET['origin']) && $_GET['origin'] == 'user_course') {
		$course_info = CourseManager :: get_course_information($get_course_code);
		if (empty ($cidReq)) {
			$interbreadcrumb[] = array (
				"url" => api_get_path(WEB_COURSE_PATH) . $course_info['directory'],
				'name' => $course_info['title']
			);
		}
		$interbreadcrumb[] = array (
			"url" => "../user/user.php?cidReq=" . $get_course_code,
			"name" => get_lang("Users")
		);
	} else
		if (!empty ($_GET['origin']) && $_GET['origin'] == 'tracking_course') {
			$course_info = CourseManager :: get_course_information($get_course_code);
			if (empty ($cidReq)) {
				//$interbreadcrumb[] = array ("url" => api_get_path(WEB_COURSE_PATH).$course_info['directory'], 'name' => $course_info['title']);
			}
			$interbreadcrumb[] = array (
				"url" => "../tracking/courseLog.php?cidReq=" . $get_course_code . '&studentlist=true&id_session=' . (empty ($_SESSION['id_session']) ? '' : $_SESSION['id_session']),
				"name" => get_lang("Tracking")
			);
		} else
			if (!empty ($_GET['origin']) && $_GET['origin'] == 'resume_session') {
				$interbreadcrumb[] = array (
					'url' => '../admin/index.php',
					"name" => get_lang('PlatformAdmin')
				);
				$interbreadcrumb[] = array (
					'url' => "../admin/session_list.php",
					"name" => get_lang('SessionList')
				);
				$interbreadcrumb[] = array (
					'url' => "../admin/resume_session.php?id_session=" . Security :: remove_XSS($_GET['id_session']),
					"name" => get_lang('SessionOverview')
				);
			} else {
				$interbreadcrumb[] = array (
					"url" => "index.php",
					"name" => get_lang('MySpace')
				);
				if (isset ($_GET['id_coach']) && intval($_GET['id_coach']) != 0) {
					$interbreadcrumb[] = array (
						"url" => "student.php?id_coach=" . Security :: remove_XSS($_GET['id_coach']),
						"name" => get_lang("CoachStudents")
					);
					$interbreadcrumb[] = array (
						"url" => "myStudents.php?student=" . Security :: remove_XSS($_GET['student']) . '&id_coach=' . Security :: remove_XSS($_GET['id_coach']),
						"name" => get_lang("StudentDetails")
					);
				} else {
					$interbreadcrumb[] = array (
						"url" => "student.php",
						"name" => get_lang("MyStudents")
					);
					$interbreadcrumb[] = array (
						"url" => "myStudents.php?student=" . Security :: remove_XSS($_GET['student']),						
						"name" => get_lang("StudentDetails")
					);
				}
			}
	$nameTools = get_lang("DetailsStudentInCourse");
} else {
		
	if (!empty ($_GET['origin']) && $_GET['origin'] == 'resume_session') {
		$interbreadcrumb[] = array (
			'url' => '../admin/index.php',
			"name" => get_lang('PlatformAdmin')
		);
		$interbreadcrumb[] = array (
			'url' => "../admin/session_list.php",
			"name" => get_lang('SessionList')
		);
		$interbreadcrumb[] = array (
			'url' => "../admin/resume_session.php?id_session=" . Security :: remove_XSS($_GET['id_session']),
			"name" => get_lang('SessionOverview')
		);
	} else {
		$interbreadcrumb[] = array (
			"url" => "index.php",
			"name" => get_lang('MySpace')
		);
		if (isset ($_GET['id_coach']) && intval($_GET['id_coach']) != 0) {
			if (isset ($_GET['id_session']) && intval($_GET['id_session']) != 0) {
				$interbreadcrumb[] = array (
					"url" => "student.php?id_coach=" . Security :: remove_XSS($_GET['id_coach']) . "&id_session=" . $_GET['id_session'],
					"name" => get_lang("CoachStudents")
				);
			} else {
				$interbreadcrumb[] = array (
					"url" => "student.php?id_coach=" . Security :: remove_XSS($_GET['id_coach']),
					"name" => get_lang("CoachStudents")
				);
			}
		} else {
			$interbreadcrumb[] = array (
				"url" => "student.php",
				"name" => get_lang("MyStudents")
			);
		}
	}
}

api_block_anonymous_users();

if (!api_is_allowed_to_edit() && !api_is_coach() && !api_is_drh() && !api_is_course_tutor() && $_user['status'] != SESSIONADMIN && !api_is_platform_admin(true)) {
	api_not_allowed(true);
}

Display :: display_header($nameTools);

/*
 *	MAIN CODE
*/
// Database Table Definitions
$tbl_user 					= Database :: get_main_table(TABLE_MAIN_USER);
$tbl_session_user 			= Database :: get_main_table(TABLE_MAIN_SESSION_USER);
$tbl_session 				= Database :: get_main_table(TABLE_MAIN_SESSION);
$tbl_session_course 		= Database :: get_main_table(TABLE_MAIN_SESSION_COURSE);
$tbl_session_course_user 	= Database :: get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
$tbl_course 				= Database :: get_main_table(TABLE_MAIN_COURSE);
$tbl_course_user 			= Database :: get_main_table(TABLE_MAIN_COURSE_USER);
$tbl_stats_access 			= Database :: get_main_table(TABLE_STATISTIC_TRACK_E_ACCESS);
$tbl_stats_exercices 		= Database :: get_main_table(TABLE_STATISTIC_TRACK_E_EXERCISES);
$tbl_stats_exercices_attempts= Database :: get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
$tbl_personal_agenda = Database :: get_main_table(TABLE_PERSONAL_AGENDA);
$tbl_course_lp_item 		= Database :: get_course_table(TABLE_LP_ITEM);

$tbl_course_lp_view = 'lp_view';
$tbl_course_lp_view_item = 'lp_item_view';
$tbl_course_lp_item = 'lp_item';
$tbl_course_lp = 'lp';
$tbl_course_quiz = 'quiz';
$course_quiz_question = 'quiz_question';
$course_quiz_rel_question = 'quiz_rel_question';
$course_quiz_answer = 'quiz_answer';
$course_student_publication = Database::get_course_table(TABLE_STUDENT_PUBLICATION);
$TABLECALHORAIRE  = Database :: get_course_table(TABLE_CAL_HORAIRE);


if (isset($_GET['user_id']) && $_GET['user_id'] != "") {
	$user_id = intval($_GET['user_id']);
} else {
	$user_id = $_user['user_id'];
}

$session_id = intval($_GET['id_session']);
$student_id = intval($_GET['student']);

// Action behaviour
$check= Security::check_token('get');

if (!empty ($_GET['student'])) 
	// infos about user
	$info_user = api_get_user_info($student_id);
	if (api_is_drh() && !UserManager::is_user_followed_by_drh($student_id, $_user['user_id'])) {
		api_not_allowed();
	}

	$info_user['name'] = api_get_person_name($info_user['firstname'], $info_user['lastname']);

?>

<center><table class='data_table'>

<tr>
					<th colspan="6">
<?php echo get_lang('result_exam_title'); echo $info_user['name']; ?>

					</th>

  <tr>
				<th><?php echo get_lang('module_no') ?>	</th>
	<th>
						<?php echo get_lang('result_exam') ?>
	</th>
					<th>
						<?php echo get_lang('result_rep_1') ?>
					</th>
					<th>
						<?php echo get_lang('result_rep_2') ?>
					</th>
					<th>
						<?php echo get_lang('comment') ?>
					</th>
					
					
  </tr>

						<?php
						

$sqlexam = "SELECT *
								 FROM $tbl_stats_exercices
								 WHERE exe_user_id = ".$_GET['student']."
								 AND c_id = '0' AND mod_no != '0'
								 ORDER BY mod_no ASC
								 ";
					$resultexam = api_sql_query($sqlexam);

					while($a_exam = Database::fetch_array($resultexam))
					{
					 $ex_id =$a_exam['ex_id'];
           $mod_no =$a_exam['mod_no'];
            $score_ex =$a_exam['score_ex'];
             $score_rep1 =$a_exam['score_rep1'];
             $score_rep2 =$a_exam['score_rep2'];
             $coment = stripslashes ($a_exam['coment']);
             echo"
				<tr><center>
					<td> ".$a_exam['mod_no']."
					</td>
				<td><center>
						".$a_exam['score_ex']."
					</td>
				<td><center>
						".$a_exam['score_rep1']."
					</td>
					<td><center>
						".$a_exam['score_rep2']."
					</td>
					<td>$coment

				";
				$exe_idd = $a_exam['exe_id'];
         $student_id= $_GET['student'] ;
         
				?>
     
				
      </tr>
<?php

}
?>


	</table>
   </form>


		<strong><?php echo get_lang('imprime_sommaire');?> </strong>
    	
		<a href="#" onclick="window.print()"><img align="absbottom" src="../img/printmgr.gif"border="0"></a>
