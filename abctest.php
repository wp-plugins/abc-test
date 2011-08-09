<?php
/*
Plugin Name: ABC Test
Plugin URI: http://www.webfaster.it/cms/wordpress/plugins/abctest
Description: Test a risposta multipla
Author: Antonio Agrestini
Version: 0.1
Author URI: http://www.webfaster.it
*/

require('abctest_options.php');
require('abctest_config.php');
require('abctest_front.php');

class abcTest {

	private static $message;
	private static $msgstatus;
	public static $CurrentPage;

	public static function init()
	{
		
		load_plugin_textdomain('abctest_lbl', false, basename( dirname( __FILE__ ) ) . '/languages' );
				
	}

	public static function admin_abc_test()
	{
		add_menu_page( 'ABC Test', 'ABC Test', 1, 'abctest', array('abcTest','adminarea'), $icon_url, $position );
		add_submenu_page('abctest', __('New Quiz','abctest_lbl'), __('New Quiz','abctest_lbl'), 1, 'addnew', array('abcTest','addnew'));		
		add_submenu_page('abctest', __('Settings','abctest_lbl'), __('Settings','abctest_lbl'), 1, 'config', array('abcTestConfig','adminarea'));				
	
    	self::$CurrentPage = add_management_page( 'abctest', 'abctest', 1, 'options', array('abcTestOptions','adminarea') );
		
		// echo self::$CurrentPage;		
	}	
	
	public static function adminarea()
	{				
		?>		
		<div class="wrap">

		<div id="icon-options-general" class="icon32"><br /></div>		
			
		<h2>ABC Test</h2>
							
						
			<?php 

			// Azioni principali
			
			switch($_GET['do'])
			{
				
				case 'edit':
					self::edit();
				break;
				
				case 'insert':
					
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {					
						self::insert();
					}
									
				break;

				case 'update':
					
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {					
						self::update();
					}
									
				break;
				
				case 'delete':
					self::delete();
				break;
				
			}
	
		
			?>			

				    <div id="message" class="<?php echo self::$msgstatus ?>">
				    <p>
				    	<?php echo self::$message ?>
				    </p>
				    </div>
		
					<?php 
					
					if ($_GET['do'] != 'edit') {
						self::listrecords();
					}			
					?>
		
		</div>		
		<?php
		
	}

	public static function addnew()
	{
		?>	

			<div class="wrap">
	
			<div id="icon-options-general" class="icon32"><br /></div>		
				
			<h2>ABC Test</h2>
			
			<?php
		
				self::mainform(NULL, 'insert');				
			
			?>
			
			</div>
		
		<?php
								
	}	
	

	public static function listrecords()
	{
		
		global $wpdb;
				
		require("lib/resultlist.class.php");
		
		$rslt = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "abctest_main");
		
		if ( count($rslt) > 0 ) {
		
			$objRS = new Resultlist();
			$objRS->addFields('id_test', 'ID', '');
			$objRS->addFields('test_title', __('Title','abctest_lbl'), '');

			$arrHandlers = array( __('options management','abctest_lbl') => 'admin.php?page=options&parentid');
			
			$objRS->showResults($rslt, 'id_test', 'admin.php?page=abctest&do=edit&id=', 
									   'admin.php?page=abctest&do=delete&id=',
									    $arrHandlers);
			
			

										
		} else {
			
			printf('<p>%s</p>', __('No quiz available!','abctest_lbl'));
		
		}

			echo '<p><input type="submit" name="addnewtest" class="button" value="' . __('add a new quiz','abctest_lbl') . '" onclick="location.href=\'?page=addnew\'" /></p>';
		
					
	}
	
	
	public static function mainform($rows, $action)
	{
	
			$actions = array('' => '', 
							 'insert' => 'admin.php?page=abctest&do=insert', 
							 'edit' => '?page=abctest&do=update');	
		
	?>
		
		<form method="post" action="<?php echo $actions[$action]; ?>">
	
			<div id="poststuff" class="metabox-holder has-right-sidebar">
	
				<div class="stuffbox">
				<h3><label for="test_title"><?php _e('quiz title','abctest_lbl') ?></label></h3>
				<div class="inside">
					<input type="text" name="test_title" id="test_title" value="<?php echo $rows[0]->test_title ?>" style="width: 100%;"  />
				    <p><?php _e('Example:','abctest_lbl') ?><code><?php _e('Discover your personality','abctest_lbl') ?></code></p>
				</div>
				</div>

				<?php the_editor(stripslashes( $rows[0]->test_text ),'test_text','test_text', true); ?>
												
				<br /><br />
				
				<h2 style="margin:0px; padding:0px;"><?php _e('Test results','abctest_lbl')  ?></h2>
				<?php _e('Define the outcome of the test according to the prevalence of answers A, B or C. You can add response options after saving the settings of the main tests','abctest_lbl')  ?>

				<br /><br />
				
				<div class="stuffbox">
				<h3><label for="test_result_a"><?php _e('Prevalence of the letter A','abctest_lbl')  ?></label></h3>
				<div class="inside">						
					<textarea name="test_result_a" style="width: 100%" rows="5"><?php echo $rows[0]->test_result_a ?></textarea>					
				</div>
				</div>

				<div class="stuffbox">
				<h3><label for="test_result_b"><?php _e('Prevalence of the letter B','abctest_lbl')  ?></label></h3>
				<div class="inside">						
					<textarea name="test_result_b" style="width: 100%" rows="5"><?php echo $rows[0]->test_result_b ?></textarea>					
				</div>
				</div>

				<div class="stuffbox">
				<h3><label for="test_result_c"><?php _e('Prevalence of the letter C','abctest_lbl')  ?></label></h3>
				<div class="inside">						
					<textarea name="test_result_c" style="width: 100%" rows="5"><?php echo $rows[0]->test_result_c ?></textarea>					
				</div>
				</div>

						
				<p>
					<input type="hidden" name="irecord" value="<?php echo $_GET['id'] ?>" />
					<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save the changes','abctest_lbl')  ?>"  />
				</p>
	
		
			</div><!-- #poststuff -->
				
		</form>	
	
	<?php			
	}


	public static function insert()
	{
		global $wpdb;
	
		$table_name = $wpdb->prefix . "abctest_main";
		
		$fields = self::dbfields();
		
	 	$rows_affected = $wpdb->insert( $table_name, $fields );		
		

			if ($rows_affected > 0) {
												
				self::$message = __('Data were entered.','abctest_lbl');
				self::$msgstatus = 'updated';
																			
			} else {
				
				self::$message = __('There was an error! Can not insert data.','abctest_lbl');
				self::$msgstatus = 'error';
			
			}



		return $rows_affected;
		
	}

	public static function edit()
	{
		
		global $wpdb;
			
		$sql = sprintf("SELECT * FROM %sabctest_main WHERE id_test = %d", $wpdb->prefix, $_GET['id']);
		$rslt = $wpdb->get_results($sql);
		self::mainform($rslt, $_GET['do']);		
	
	}


	public static function update()
	{
		global $wpdb;
	
		$table_name = $wpdb->prefix . "abctest_main";
		
		$fields = self::dbfields();
		
				
	 	$rows_affected = $wpdb->update( $table_name, $fields, array('id_test' => $_POST['irecord']), NULL, array('%d') );		
			
		
			if ($rows_affected > 0) {
				
				self::$message = __('The data have been updated.','abctest_lbl');
				self::$msgstatus = 'updated';
										
			} else {
				
				self::$message = __('There was an error! Can not update the data.','abctest_lbl');
				self::$msgstatus = 'error';
			
			}



		return $rows_affected;
		
	}


	public static function delete()
	{
		global $wpdb;
			
		$sql = sprintf("DELETE FROM %sabctest_main WHERE id_test = %d", $wpdb->prefix, $_GET['id']);
		
		$wpdb->query($sql);

		// cancella dati correlati
		$sql = sprintf("DELETE FROM %sabctest_options WHERE id_test = %d", $wpdb->prefix, $_GET['id']);
		
		$wpdb->query($sql);
		
		
				self::$message = __('The data have been deleted.','abctest_lbl');
				self::$msgstatus = 'updated';
		
		// Implementare cancellazione immagine
		/*
		$deletefile = self::$uploadfolder . $_GET['file'];
		
		if ( file_exists( $deletefile ) ) {
			
			unlink($deletefile);
			
		}
		*/
		
	}

	public static function dbfields()
	{

		$arrFields = array('test_title' => $_POST['test_title'],
							'test_text' => $_POST['test_text'],
				  			'test_result_a' => $_POST['test_result_a'],
				  			'test_result_b' => $_POST['test_result_b'],
				  			'test_result_c' => $_POST['test_result_c']); 
		
		//'bgimage' => $_FILES['bgimage']['name'],
		/*
		$upfile = self::upload();
		
		if ( ! empty($upfile) ) {
			
			$fup = array("bgimage" => $upfile);
			$arrFields = array_merge((array) $arrFields, (array) $fup); 		
		
		}
		*/
		
		return $arrFields;
				
	}


	public static function install()
	{
	
		global $wpdb;
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
		// Prima tabella
		
		$table_name = $wpdb->prefix . "abctest_main";
		
		$sql = "CREATE TABLE " . $table_name . " (
	  		id_test bigint(11) NOT NULL AUTO_INCREMENT,
	  		test_title varchar(255) DEFAULT NULL,
	  		test_text longtext,
	  		test_image varchar(255) DEFAULT NULL,
	  		test_result_a longtext,
	  		test_result_b longtext,
	  		test_result_c longtext,
	  		absolute_url text,
	  		PRIMARY KEY (id_test)
			);";
	
		// Seconda tabella
		
		$table_name = $wpdb->prefix . "abctest_options";
		
		$sql .= "CREATE TABLE " . $table_name . " (
  			id_option bigint(11) NOT NULL AUTO_INCREMENT,
  			id_test bigint(20) DEFAULT '0',
  			options_introduction longtext,
  			option_a text,
  			option_b text,
  			option_c text,
  			options_order int(11) DEFAULT '0',
  			PRIMARY KEY (id_option)
			);";
	
		dbDelta($sql);
				
	}


} // end abcTest

register_activation_hook(__FILE__, array('abcTest', 'install'));

add_action('admin_menu', array('abcTest', 'admin_abc_test'));


/* Tiny MCE */

add_filter('admin_head','show_tinyMCE');
 
function show_tinyMCE() {
    wp_enqueue_script( 'common' );
    wp_enqueue_script( 'jquery-color' );
    wp_print_scripts('editor');
    if (function_exists('add_thickbox')) add_thickbox();
    wp_print_scripts('media-upload');
    if (function_exists('wp_tiny_mce')) wp_tiny_mce();
    wp_admin_css();
    wp_enqueue_script('utils');
    do_action("admin_print_styles-post-php");
    do_action('admin_print_styles');
    remove_all_filters('mce_external_plugins');
}

	

if ( is_admin() ) {
	
	add_action('init', array('abcTest', 'init'));
	
	// add_action('admin_print_scripts-' . abcTest::$CurrentPage, 'add_scripts');
	add_action('admin_print_scripts-tools_page_options', 'add_scripts');
	 
 }
 

function add_scripts()
{
		wp_register_style('abctest_css', plugins_url('css/abctest_style.css', __FILE__));
        wp_enqueue_style('abctest_css');

		wp_enqueue_script( array("jquery", "jquery-ui-core", "interface", "jquery-ui-sortable", "wp-lists", "jquery-ui-sortable") );
        
        wp_register_script('abctest_script', plugins_url('js/abctest_functions.js', __FILE__));
        wp_enqueue_script( 'abctest_script' );
	
 
}

/* FRONT END */

if (! is_admin() ) {

	add_shortcode( 'abctest', array('abcTestFrontEnd', 'shortcode_handler') );	
	add_action('init', array('abcTestFrontEnd','frontscripts'));

}

?>