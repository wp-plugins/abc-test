jQuery(document).ready(function() {

	jQuery(".sortable thead tr").prepend('<th></th>');
	jQuery(".sortable tfoot tr").prepend('<th></th>');

	jQuery(".sortable tbody tr").prepend('<td class="reordercell"></td>');

    jQuery(".sortable tbody.content").sortable();
    jQuery(".sortable tbody.content").disableSelection();
    
});


function sendReorderForm() {

	document.getElementById('form_reorder').submit();

}