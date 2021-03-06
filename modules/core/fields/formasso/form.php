<?php 
/**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@parsimony-cms.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 * @authors Julien Gras et BenoÃ®t Lorillot
 * @copyright  Julien Gras et BenoÃ®t Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package core/fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
\app::$request->page->addJSFile('lib/jquery-ui/jquery-ui-1.10.3.min.js');
\app::$request->page->addCSSFile('core/fields/formasso/css.css');
echo $this->displayLabel($fieldName);
?>

<?php
$mode = '';
$mode = \app::getModule($this->entity->getModule())->getEntity($this->entity_foreign);
$words = array();
$tit = $mode->getBehaviorTitle();
foreach ($mode->select() as $row) {
	$obj = new stdClass;
	$obj->label = $row->$tit;
	$obj->value = $row->getId()->value;
	$words[] = $obj; 
}

?>
<script>
	function addATag<?php echo $fieldName; ?>(id,label){
		if(label.length > 0)$('<div style="float:left;padding:3px"><span class="ui-icon ui-icon-circle-close" style="float:left;cursor:pointer"></span>' + label + '<input type="hidden" name="<?php echo $this->name; ?>[' + id + ']" value="' + label + '" /></div>').appendTo("#log<?php echo $fieldName; ?>");
	}
	$(function() {
		var availableTags<?php echo $fieldName; ?> = <?php echo json_encode($words); ?>;
		$( "#<?php echo $fieldName; ?>").autocomplete({
			source: availableTags<?php echo $fieldName; ?>,
			select: function( event, ui ) {
			addATag<?php echo $fieldName; ?>(ui.item.value, ui.item.label);
			ui.item.value = '';
			}
		});
		$(document).on('click', ".ui-icon-circle-close", function(){
			$(this).parent().remove();
		})
		.on("keydown","#<?php echo $fieldName; ?>",function(event){
			if(event.keyCode == 13){
			event.preventDefault();
			event.stopPropagation();
			$("#<?php echo $fieldName; ?>_ok").trigger("click");
			}
		})
		.on("click","#<?php echo $fieldName; ?>_ok",function(){
			var val = $("#<?php echo $fieldName; ?>").val();
			if(val.indexOf(",")){
			var cut = val.split(",")
			for(i=0;i < cut.length;i++){
				addATag<?php echo $fieldName; ?>("new" + (new Date()).getTime(),cut[i]);
			}
			}else{
			addATag<?php echo $fieldName; ?>("new" + (new Date()).getTime(),val);
			}
			$("#<?php echo $fieldName; ?>").val("");
		});
	});
</script>

<div class="fixedzindex<?php echo $fieldName; ?>"><input type="text" id="<?php echo $fieldName; ?>" /><input type="button" id="<?php echo $fieldName; ?>_ok"  value="<?php echo t('Add'); ?>" /></div>
<div id="log<?php echo $fieldName; ?>" style="border-color: #ddd;border-radius:4px;padding-top:7px;width: 100%; overflow: auto;" class="ui-widget-content">
<?php
if($row){
	$idNameEntity = $row->getId()->name;
	$foreignEntity = \app::getModule($this->entity->getModule())->getEntity($this->entity_foreign);
	$idNameForeignEntity = $foreignEntity->getId()->name;
	$titleForeignEntity = $foreignEntity->getBehaviorTitle();
	$assoEntity = \app::getModule($this->entity->getModule())->getEntity($this->entity_asso);
	\app::$request->setParam('idformasso' , $row->getId()->value); // set value to be used in prepared query
	foreach ($assoEntity->select()->join($this->entity->getModule().'_'.$this->entity_asso.'.'.$idNameForeignEntity, $this->entity->getModule().'_'.$this->entity_foreign.'.'.$idNameForeignEntity)->where($idNameEntity.' = :idformasso') as $row) {
		echo '<div style="float:left;padding:3px"><span class="ui-icon ui-icon-circle-close" style="float:left;cursor:pointer"></span>' . $row->$titleForeignEntity . '<input type="hidden" name="'.$this->name.'[' . $row->$idNameForeignEntity . ']" value="' . $row->$titleForeignEntity . '" /></div>';
	}
}
?>
</div>

