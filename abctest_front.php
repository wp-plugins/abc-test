<?php 
	
	class abcTestFrontEnd {
		
		
		public static function shortcode_handler( $atts )
		{
			
			
			
			extract( shortcode_atts( array('id' => '0'), $atts ) );		
				
						
			return self::rendertest($atts['id']);			
					
		}
		
		
		public static function rendertest($itest)
		{
		
			global $wpdb;
					
			$template = get_option('selectedTemplate', 'default');
			
			$iCurrentOption = empty($_POST['lastselectedoption']) ? 1 : ( (int) $_POST['lastselectedoption'] + 1 );


			if ( $_SERVER['REQUEST_METHOD'] == 'POST') {
					
					$responses =  $_POST['optresponses'] . $_POST['voteoption'];
							
						
				if ( strlen($responses) <  $iCurrentOption) {
					
					/*
						L'utente ha inviato senza selezionare una risposta. 
						Ritorna allo step relativo al numero di risposte fornite.					
					*/
					
					$iCurrentOption = strlen($responses) +1;
				}
			
			
			}
												
			$SQL = "SELECT * FROM " . $wpdb->prefix . "abctest_main";
			$SQL .= sprintf(' WHERE id_test = %d', $itest);
			
			$rslt = $wpdb->get_results($SQL);
			
					
			// titolo e descrizione test
			
			ob_start();
			
			// Verifica se esiste un template diverso da quello di default

				$tmplHTML = file_get_contents('wp-content/plugins/abctest/templates/' . $template . '/main.html');
			
					
				$tmplHTML = preg_replace("/%QUIZTITLE%/", $rslt[0]->test_title, $tmplHTML);
				$tmplHTML = preg_replace("/%QUIZDESCRIPTION%/", stripslashes($rslt[0]->test_text), $tmplHTML);

						
				// opzioni
				$SQL = "SELECT * FROM " . $wpdb->prefix . "abctest_options";
				$SQL .= sprintf(' WHERE id_test = %d', $itest);
				$SQL .= " ORDER BY options_order ASC";
				
				$options = $wpdb->get_results($SQL);
				$optindex = $iCurrentOption -1;
				
				$totaloptions = count($options);
				
				if ($iCurrentOption  <= $totaloptions ) { // mostra domanda successiva
					
								
					$tmplHTML = preg_replace("/%QUIZSTEPS%/", "domanda " . $iCurrentOption . " di " . $totaloptions, $tmplHTML);
					$tmplHTML = preg_replace("/%QUESTIONNUMBER%/", $iCurrentOption, $tmplHTML);
					$tmplHTML = preg_replace("/%QUESTIONTEXT%/", stripslashes($options[$optindex]->options_introduction), $tmplHTML);
					
			
					$tmplHTML = preg_replace("/%A_RESPONSE%/", stripslashes($options[$iCurrentOption -1]->option_a), $tmplHTML);
					$tmplHTML = preg_replace("/%B_RESPONSE%/", stripslashes($options[$iCurrentOption -1]->option_b), $tmplHTML);
					$tmplHTML = preg_replace("/%C_RESPONSE%/", stripslashes($options[$iCurrentOption -1]->option_c), $tmplHTML);
					
															
						$controls = sprintf('<input type="hidden" name="lastselectedoption" value="%s" />', $iCurrentOption);
						$controls .= "\n";						
						$controls .= sprintf('<input type="hidden" name="optresponses" value="%s" />', $responses);
						$controls .= "\n";							
						$controls .= sprintf('<input type="submit" name="SubmitOptions" value="%s" />', 'Rispondi');
						$controls .= "\n";
						
						
						$tmplHTML = preg_replace("/\[CONTROLS\]/", $controls, $tmplHTML);
											
						
						echo $tmplHTML;
						
						
						
					} else { // esito test
						
						$count_resp[0] = substr_count($responses, 'a');
						$count_resp[1] = substr_count($responses, 'b');
						$count_resp[2] = substr_count($responses, 'c');
												
						$arrLett = array('A', 'B', 'C');
						
						for($i =0; $i <= 3; $i++) {
							
							if ( $count_resp[$i] ==  max($count_resp) ) {
								
								$exitus = $arrLett[$i];
								
							}	
												
						}
							
							
							$tmpResltlHTML = file_get_contents('wp-content/plugins/abctest/templates/' . $template . '/results.html');
							
							
							$tmpResltlHTML = preg_replace("/%QUIZTITLE%/", $rslt[0]->test_title, $tmpResltlHTML);
							$tmpResltlHTML = preg_replace("/%QUIZDESCRIPTION%/", stripslashes($rslt[0]->test_text), $tmpResltlHTML);
							
							$tmpResltlHTML = preg_replace("/%RESULT_TITLE_0%/", "Hai risposto prevalentemente $exitus", $tmpResltlHTML);
							$tmpResltlHTML = preg_replace("/%RESULT_DESCRIPTION_0%/",  stripslashes($rslt[0]->{'test_result_' . strtolower($exitus) }), $tmpResltlHTML);
													
							for($i =0; $i < 3; $i++) {
							
							if ( $arrLett[$i] != $exitus) {
								
								$arrTitles[] = " prevalenza " . $arrLett[$i];
								$arrDescript[] = stripslashes($rslt[0]->{'test_result_' . strtolower($arrLett[$i]) });
						
							}	
												
						}	
							
											
					}

						$tmpResltlHTML = preg_replace("/%RESULT_TITLE_1%/", $arrTitles[0], $tmpResltlHTML);
						$tmpResltlHTML = preg_replace("/%RESULT_DESCRIPTION_1%/", $arrDescript[0], $tmpResltlHTML);
						$tmpResltlHTML = preg_replace("/%RESULT_TITLE_2%/", $arrTitles[1], $tmpResltlHTML);
						$tmpResltlHTML = preg_replace("/%RESULT_DESCRIPTION_2%/", $arrDescript[1], $tmpResltlHTML);
				
			
					echo $tmpResltlHTML;
			
			return ob_get_clean();
					
		}


		public static function frontscripts()
		{

			$template = get_option('selectedTemplate', 'default');

       	 	wp_register_style('abctest_css', plugins_url('templates/' . $template . '/style.css', __FILE__));
        	wp_enqueue_style( 'abctest_css' );
				
		}
		
	}
	

?>