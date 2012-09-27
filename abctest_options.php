<?php

class abcTestOptions {

	private static $message;
	private static $msgstatus;


	public static function adminarea()
	{	

							
		?>		
		<div class="wrap">

		<div id="icon-options-general" class="icon32"><br /></div>		
			
		<h2>ABC Test: <?php _e('options','abctest_lbl') ?></h2>
							
						
			<?php 

			// Azioni principali
			
			switch($_GET['do'])
			{

				case 'addnew':
					self::mainform(NULL, 'insert');	
				break;
				
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

				case 'reorder':
					self::reorder();
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


	public static function listrecords()
	{
		
		global $wpdb;
								
		require("lib/resultlist.class.php");
		
		// Riepilogo test
		
		$rslt = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "abctest_main WHERE id_test = " . $_GET['parentid']);
		
		?>
		
		<div id="poststuff" class="metabox-holder has-right-sidebar">
		
		<div id="linkadvanceddiv" class="postbox" >
			<div class="handlediv" title="Fare clic per cambiare."><br /></div>
			<h3 class='hndle'><span><?php echo $rslt[0]->test_title ?></span></h3>
			<div class="inside">
				<?php echo strip_tags($rslt[0]->test_text) ?>
			</div>
		</div>	
		
		</div>	
		
		<form name="form_reorder" id="form_reorder" method="post" action="?page=options&parentid=<?php echo $_GET['parentid']  ?>&do=reorder">
		
		<?php
		// fine riepilogo test
		
		$rslt = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "abctest_options WHERE id_test = " . $_GET['parentid'] . " ORDER BY options_order ASC");
		
		if ( count($rslt) > 0 ) {
		
			$objRS = new Resultlist();
			
			$objRS->ReorderMode = TRUE;
		
			$objRS->addFields('options_introduction', __('Introduction','abctest_lbl'), '');
			$objRS->addFields('option_a', __('Answer A','abctest_lbl'), '');
			$objRS->addFields('option_b', __('Answer B','abctest_lbl'), '');
			$objRS->addFields('option_c', __('Answer C','abctest_lbl'), '');

			// $arrHandlers = array('gestione opzioni' => 'admin.php?page=options&parentid');
			
			$objRS->showResults($rslt, 'id_option', 'admin.php?page=options&parentid=' . $_GET['parentid'] . '&do=edit&id=', 
									   'admin.php?page=options&parentid=' . $_GET['parentid'] . '&do=delete&id=',
									    $arrHandlers);
						
		} else {
			
			printf('<p>%s</p>', __('No options available for selecting the quiz!','abctest_lbl'));
		
		}
			
			?>
			
			</form>
			
			<?php
			
			echo '<p>';
			
			echo '<input type="submit" name="addnewoption" class="button" value="' . __('Add a new option','abctest_lbl') . '" onclick="location.href=\'?page=options&parentid=' . $_GET['parentid'] . '&do=addnew\'" />';

			echo '<input type="submit" name="reorderlist" class="button" value="' . __('Save order of the list','abctest_lbl') . '" onclick="sendReorderForm();" />';
			
			echo '</p>';
		
					
	}
	
	
	public static function mainform($rows, $action)
	{
	
			$actions = array('' => '', 
							 'insert' => '?page=options&parentid=' . $_GET['parentid'] . '&do=insert', 
							 'edit' => '?page=options&parentid=' . $_GET['parentid'] . '&do=update');	
		
	?>
		<p>
			&laquo; <a href="?page=options&parentid=<?php echo $_GET['parentid'] ?>"><?php _e("back to options list",'abctest_lbl') ?></a>
		</p>
		
		<form method="post" action="<?php echo $actions[$action]; ?>">
	
			<div id="poststuff" class="metabox-holder has-right-sidebar">
				
				<?php the_editor(stripslashes( $rows[0]->options_introduction ),'options_introduction','options_introduction', true); ?>
				
				<br /><br />
				
				<div class="stuffbox">
				<h3><label for="option_a">risposta A</label></h3>
				<div class="inside">						
					<textarea name="option_a" style="width: 100%" rows="2"><?php echo $rows[0]->option_a ?></textarea>					
				</div>
				</div>

				<div class="stuffbox">
				<h3><label for="option_b">risposta B</label></h3>
				<div class="inside">						
					<textarea name="option_b" style="width: 100%" rows="2"><?php echo $rows[0]->option_b ?></textarea>					
				</div>
				</div>

				<div class="stuffbox">
				<h3><label for="option_c">risposta C</label></h3>
				<div class="inside">						
					<textarea name="option_c" style="width: 100%" rows="2"><?php echo $rows[0]->option_c ?></textarea>					
				</div>
				</div>

						
				<p>
					<input type="hidden" name="irecord" value="<?php echo self::sanitizeid(); ?>" />
					<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save changes','abctest_lbl') ?>"  />
				</p>
	
		
			</div><!-- #poststuff -->
				
		</form>	
	
	<?php			
	}


	public static function insert()
	{
		global $wpdb;
	
		$table_name = $wpdb->prefix . "abctest_options";
		
		$fields = self::dbfields();
		
	 	$rows_affected = $wpdb->insert( $table_name, $fields );		
		

			if ($rows_affected > 0) {
												
				self::$message = __('Data were entered.','abctest_lbl');
				self::$msgstatus = 'updated';
																			
			} else {
				
				self::$message = __('There was an error! Can not insert data.');
				self::$msgstatus = 'error';
			
			}



		return $rows_affected;
		
	}

	public static function edit()
	{
		
		global $wpdb;
		
		$sql = sprintf("SELECT * FROM %sabctest_options WHERE id_option = %d", $wpdb->prefix, self::sanitizeid());
		$rslt = $wpdb->get_results($sql);
		self::mainform($rslt, $_GET['do']);		
	
	}


	public static function update()
	{
		global $wpdb;
	
		$table_name = $wpdb->prefix . "abctest_options";
		
		$fields = self::dbfields();
				
	 	$rows_affected = $wpdb->update( $table_name, $fields, array('id_option' => $_POST['irecord']), NULL, array('%d') );		
			

			if ($rows_affected > 0) {
				
				self::$message = __('The data have been updated.','abctest_lbl');
				self::$msgstatus = 'updated';
										
			} else {
				
				self::$message = __('There was an error! Can not insert data.','abctest_lbl');
				self::$msgstatus = 'error';
			
			}



		return $rows_affected;
		
	}


	public static function delete()
	{
		global $wpdb;
			
		$sql = sprintf("DELETE FROM %sabctest_options WHERE id_option = %d", $wpdb->prefix, self::sanitizeid());
		
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

		$arrFields = array('id_test' => $_GET['parentid'],
						   'options_introduction' => $_POST['options_introduction'],
						   'option_a' => $_POST['option_a'],
						   'option_b' => $_POST['option_b'],
				  		   'option_c' => $_POST['option_c']); 
		
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


	public static function reorder()
	{	

		global $wpdb;
		
		$table_name = $wpdb->prefix . "abctest_options";
		
		$arrItems = $_POST['recordorder'];	
					
		for ($j = 0; $j < count($arrItems); $j++ ) {
		
			$SQLupdate = " UPDATE $table_name SET options_order = " . ($j + 1) . " WHERE id_option = " . $arrItems[$j];
			
			$wpdb->query($SQLupdate);
			
		}

												
		self::$message = __("The order of the data was stored.",'abctest_lbl');
		self::$msgstatus = 'updated';

			
	}
	

	private static function sanitizeid()
	{
						
		$idi = mysql_real_escape_string( trim($_GET['id']) );
		return (int) $idi;
	
	}	

} // end abcTestOptions

?>