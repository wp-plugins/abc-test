<?php 
/* 		
	Class Name: Resultlist
	Version: 0.2 (BETA)
  	Author: Antonio Agrestini
	E-mail: info@webfaster.it	
	Web Site: http://www.webfaster.it

*/
	
class Resultlist
{

	private $arrDbFields = array();
	private $arrColumnLabel = array();
	private $arrImagePaths = array();	
	
	public $ReorderMode;
	
	public function addFields($DbFieldName, $ColumnName, $imagepath = NULL) {
		
		$this->arrDbFields[] = $DbFieldName;
		$this->arrColumnLabel[] = $ColumnName;
		$this->arrImagePaths[] = $imagepath;
	
	}

	
	public function showResults($resultsoutput, $idfield, $edit_handler, $delete_handler, $arrcustom_handlers = array())
	{
		
		$numCols = count($this->arrColumnLabel);		
		$numcustomHandlers = count($arrcustom_handlers);
		
		for ($i=0; $i < $numCols; $i++) {

			$intestRow .= "<th>" . $this->arrColumnLabel[$i] . "</th>\n";
		
		}
		
		if ($numcustomHandlers > 0) {
			for ($i=0; $i < $numcustomHandlers; $i++) {
				$intestRow .= "<th></th>\n";
			}
		}
		
		if (count($resultsoutput) == 0)
		{
			
			echo "<div class=\"error\"><p>" . __('Still no data available.','abctest_lbl') . "</p></div>\n";
		
		} 
		
		
		echo("<table class=\"widefat sortable\">\n");
		echo("<thead>
			    <tr>\n 
					$intestRow
				</tr>\n
			</thead>");

		echo("<tfoot>
			    <tr>\n 
					$intestRow
				</tr>\n
			</tfoot>");

		

		echo("<tbody class=content>");
		
		foreach ($resultsoutput as $numpost) {
			
			$edit = get_bloginfo('wpurl') . '/wp-admin/' . $edit_handler . $numpost->{$idfield};
			$remove = get_bloginfo('wpurl') . '/wp-admin/' . $delete_handler . $numpost->{$idfield};
			
						
			echo "<tr>\n";
			
			for ($i=0; $i < count($this->arrDbFields); $i++) {		
				
				echo "<td>"; 
					
					if ($this->ReorderMode && $i == 0) {
						printf('<input type="hidden" name="recordorder[]" value="%d" />',  $numpost->{$idfield});
					}
					
					
					if ($this->arrImagePaths[$i] != NULL) { // Immagine
						
						$imgpth = $this->arrImagePaths[$i] . $numpost->{$this->arrDbFields[$i]};
						
						echo "<a href=\"$imgpth\" target=\"_blank\"><img src=\"" . $imgpth . "\" width=\"200\" border=\"0\" /></a>";						
						
						$remove .= "&amp;file=" . $numpost->{$this->arrDbFields[$i]}; 
						
					} else {
					
						echo stripslashes( strip_tags($numpost->{$this->arrDbFields[$i]}) );
				
					}
				
					if ($i == 0) { // Solo per la prima riga
						
						echo "<br />\n";
						echo "<a href=\"{$edit}\">" . __('edit','abctest_lbl') . "</a> | ";
						echo "<a href=\"{$remove}\" onclick=\"return confirm('" . __('Do you really want to remove this element?') . "');\">" . __('delete','abctest_lbl') . "</a>";
					}				
 
				echo "</td>\n"; 
			
					   			
			}

			if ($numcustomHandlers > 0) {
					
					foreach($arrcustom_handlers as $val => $handlr) {
						
						printf('<td><a href="%s=%d">%s</a></td>', $handlr, $numpost->{$idfield}, $val);
					
					}
																				
			}


			echo "</tr>\n";
		}
		
		echo("</tbody>\n");
		
		echo("</table>\n\r");

	
	}

}

?>