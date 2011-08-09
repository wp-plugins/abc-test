<?php 
	
	class abcTestConfig {
		
	private static $message;
	private static $msgstatus;
	
		public static function adminarea()
		{				
			?>		
			<div class="wrap">
	
			<div id="icon-options-general" class="icon32"><br /></div>		
				
			<h2>ABC Test: <?php _e('settings','abctest_lbl'); ?></h2>
								
							
				<?php 
	
				// Azioni principali
				
				switch($_GET['do'])
				{
					
		
					case 'update':
						
						if ($_SERVER['REQUEST_METHOD'] == 'POST') {					
							self::update();
						}
										
					break;
					
					
					default:
						self::mainform('update');
					break;
					
				}
		
			
				?>			
	
					    <div id="message" class="<?php echo self::$msgstatus ?>">
					    <p>
					    	<?php echo self::$message ?>
					    </p>
					    </div>
			
			
			</div>		
			<?php
			
		}

		
		public static function update()
		{
			
			$selTmpl = $_POST['template'];
			update_option('selectedTemplate', $selTmpl);
	
			self::$message = __('The data have been updated.','abctest_lbl');
			self::$msgstatus = 'updated';
			
			//self::mainform('');
						
		}
		
		
		

		public static function mainform($action)
		{
	
			$actions = array('' => '', 
							 'update' => '?page=config&do=update');	
		
		?>
		
		<form method="post" action="<?php echo $actions[$action]; ?>">
	
			<div id="poststuff" class="metabox-holder has-right-sidebar">
	
				<div class="stuffbox">
				<h3><label for="test_title"><?php _e('Select look and feel','abctest_lbl') ?></label></h3>
				<div class="inside">
											
					<select name="template" id="template" style="width: 300px;">
			
						<?php 
						
						
						$selectedTemplate = get_option('selectedTemplate', 'default');
							
						$abspath = dirname( __FILE__ );
							
						$path = $abspath . '/templates';
						$results = scandir($path);
						
						foreach ($results as $result) {
						    if ($result === '.' or $result === '..') continue;
						
						    if (is_dir($path . '/' . $result)) {
						      	
						      		$SELECTED = ($selectedTemplate == $result) ? ' selected="selected"' : NULL;
						      	
						      	printf('<option%s>%s</option>', $SELECTED, $result);
						    	echo "\n";
						    	
						    }
						
						}
							
						
						?>						
					</select> 				    
				</div>
				</div>

							
				<p>
					<input type="hidden" name="irecord" value="<?php echo $_GET['id'] ?>" />
					<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save changes','abctest_lbl')  ?>"  />
				</p>
	
		
			</div><!-- #poststuff -->
				
		</form>	
	
		<?php			
		}

			

	}

?>