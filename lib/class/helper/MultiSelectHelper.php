<?php
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/CssResources.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/JsResources.php');
class MultiSelectHelper{
	private $elements = array();
	private $name;
	private $id;

	public function __construct($name, $id){
		$this->name = $name;
		$this->id = $id;
	}

	function addElement($name,  $value , $selected = false){
		$selec = '';
		if($selected){
			$selec = 'selected="selected"';
		}
		$this->elements[$value] = "<option value='".$value."' $selec >$name</option>";
	}

	function printMultiSelect($print = true){
		global $CFG;
		echo '<link type="text/css" href="'.$CFG->wwwroot.CssResources::UI_JQUERY.'" rel="stylesheet" />';
		echo '<link type="text/css" href="'.$CFG->wwwroot.CssResources::UI_THEME.'" rel="stylesheet" />';
		echo '<link type="text/css" href="'.$CFG->wwwroot.CssResources::UI_MULTISELECT.'" rel="stylesheet" />';
		echo '<script type="text/javascript" src="'.$CFG->wwwroot.JsResources::JQUERY.'"></script>';
		echo '<script type="text/javascript" src="'.$CFG->wwwroot.JsResources::JQUERY_UI.'""></script>';
		echo '<script type="text/javascript" src="'.$CFG->wwwroot.JsResources::JQUERY_LOCALISATION.'""></script>';
		echo '<script type="text/javascript" src="'.$CFG->wwwroot.JsResources::JQUERY_SCROLLTO.'""></script>';
		echo '<script type="text/javascript" src="'.$CFG->wwwroot.JsResources::JQUERY_MULTISELECT.'"></script>';
		?>
		<script type="text/javascript">
		$(function(){
			$.localise('ui-multiselect', {/*language: 'en',*/ path: 'js/locale/'});
			$(".multiselect").multiselect();
		});
		</script>
		<?php
		$output = "<div id=\"fitem_id_multiselect\" class =\"fitem fitem_ffilepicker\" ><select id='".$this->id."' class='felement ffilepicker' multiple='multiple' name='".$this->name."' >";

		foreach ($this->elements as $element){
			$output.= $element;
		}

		if($print){
			return $output."</select> </div>";
		}else{
			echo $output."</select> </div>";
		}
	}
}
