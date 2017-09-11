<?php
/**
 * Plugin Name: Download Quiz LifterLMS
 * Plugin URI: https://larasoftbd.com/
 * Description: Download Question / Quiz from LifterLMS. It's a supporting plugin for LifterLMS
 * Version: 1.0.1
 * Author: ronymaha
 * Author URI: https://larasoftbd.com/
 * Text Domain: larasoft
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 4.8
 *
 * @package     Download-Quiz-LifterLMS
 * @category 	Core
 * @author 		LaraSoft
 */

/**
 * Restrict direct access
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define('EXPORT', plugin_dir_path( __FILE__ ));


if(!function_exists('function_name')){
	add_action( 'admin_menu', 'quiz_download_menu' );
	function quiz_download_menu() {
		add_submenu_page( 'edit.php?post_type=course', 'Download Quiz', 'Download Quiz Questions', 'manage_options', 'download_quiz_lifterlms', 'downloadLifterlmsQuizCallback');
	}	
}

if(!function_exists('downloadLifterlmsQuizCallback')){
	function downloadLifterlmsQuizCallback(){ ?>
		<div style="max-width: 1140px; margin:0 auto;">
		<h2>Click below buton for process to download</h2>
		<hr>
			<br>
			<ul class="inline-list">
				
			
			<li><a href="<?= admin_url() . 'edit.php?post_type=course&page=download_quiz_lifterlms&process=1';  ?>" class="button button-primary" title="Download">All</a></li>
			<?php 
					/**
					 * The WordPress Query class.
					 * @link http://codex.wordpress.org/Function_Reference/WP_Query
					 *
					 */
					$args = array(
						//Type & Status Parameters
						'post_type'   => 'llms_quiz',
						'post_status' => 'publish',
						//Pagination Parameters
						'posts_per_page' => -1,
					);
				$query = new WP_Query( $args );
				if($query->have_posts()):
					while($query->have_posts()): $query->the_post(); global $post;
				
			?>
			<li><a href="<?= admin_url() . 'edit.php?post_type=course&page=download_quiz_lifterlms&process=1&quizid='.$post->ID.'';  ?>" class="button button-primary" title="Download"><?php the_title(); ?></a></li>
			<?php endwhile; /*End Loop*/ endif; wp_reset_query(); //End chec if post have or not  ?>
		</ul>
		
	<?php
	if(isset($_GET['process'])):
		$fileHead = array(
	    	'Question Title', 
	    	'Question', 
	    	'Option1', 
	    	'Option2', 
	    	'Option3', 
	    	'Option4',
	    	'Option5',
	    	'Correct Answer',
	    	'details for option1',
	    	'details for option2',
	    	'details for option3',
	    	'details for option4',
	    	'details for option5'
	    );
			/**
			 * The WordPress Query class.
			 * @link http://codex.wordpress.org/Function_Reference/WP_Query
			 *
			 */
			$args = array(
				//Type & Status Parameters
				'post_type'   => 'llms_question',
				'post_status' => 'publish',
				//Pagination Parameters
				'posts_per_page' => -1
			);
			$fname = '';
			if(isset($_GET['quizid'])){
				$qpost = get_post($_GET['quizid']); 
				$fname .= $qpost->post_name;
				$getQuzID = get_post_meta( $_GET['quizid'], '_llms_questions', true );
				$qids = array();
				foreach($getQuzID as $sQ) array_push($qids, $sQ['id']);
				if(count($qids) > 0){
					$args['post__in'] = $qids;
				}
			}else{
				$fname .= 'all_questions';
			}
			$query = new WP_Query( $args );
			$valuArray = array();
			if($query->have_posts()):
				while($query->have_posts()): global $post;
					$query->the_post();
					$sarray = array();
					array_push($sarray, get_the_title());
					array_push($sarray, get_the_excerpt());
					
					$metas = get_post_meta( $post->ID, '_llms_question_options', true );
					$correct = '';
					for($i=0; 4 >= $i; $i++):
						array_push($sarray, $metas[$i]['option_text']);
						if($metas[$i]['correct_option']) $correct .= $metas[$i]['option_text'];
					endfor;
					array_push($sarray, $correct);
					for($s=0; 4 >= $s; $s++):
						array_push($sarray, $metas[$s]['option_description']);
					endfor;
					array_push($valuArray, $sarray);
				endwhile; //end query while
			endif; wp_reset_query(); //end query if
		
	    $fp = fopen(EXPORT.'csv/'.$fname.'.csv', 'w');

		fputcsv($fp, $fileHead);	    
	    foreach($valuArray as $v){
	    	 fputcsv($fp, $v);
	    }
	    fclose($fp);
	    $url = plugin_dir_url( __FILE__ ) . 'csv/'.$fname.'.csv';

	    if(count($valuArray) > 0){
	    	echo '<hr/><h3>Complete Process, Please click below button for download</h3>';
	    	echo '<div class="restndownload"><a download style="text-align: center;" href="'.$url.'" class="button button-primary" title="Download">Click for Download</a>';
	    	echo '<a style="text-align: center;" href="'.admin_url() . 'edit.php?post_type=course&page=download_quiz_lifterlms" class="button button-primary" title="Download">Reset</a></div>';
	    }
	    echo '</div>';
	    endif; // Check is process set at 1

	} // End downloadLifterlmsQuizCallback function 
}

add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
	if(isset($_GET['page']) && $_GET['page'] == 'download_quiz_lifterlms'){
	echo '<style>
    	#wpbody ul.inline-list li {
		    display: inline-block;
		    margin-bottom: 10px;
		    margin-right: 5px;
		}
		#wpbody ul.inline-list li a {
		    font-size: 15px;
		    padding: 16px;
		    line-height: 0;
		}
		#wpbody .restndownload a {
		    margin-right: 10px;
		    display: inline-block;
		    overflow: hidden;
		    font-size: 23px;
		    padding: 20px;
		    line-height: 0;
		    background: #151516;
		    border-color: #32373c;
		    text-shadow: none;
		}
		#wpbody .restndownload a:hover {
		    background: #565657;
		    border-color: #575757;
		}
  	</style>';

	}
}



