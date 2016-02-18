<?php
/**
 * List options for static condition checking for hook
 */

	foreach (get_class_methods("MY_AIA_PROCESSFLOW_STATIC_CONDITION") as $func) {
		if ($func=='const') 
			echo "<option value='{$func}' selected>".__($func,'my-aia')."</option>";
		else
			echo "<option value='{$func}'>".__($func,'my-aia')."</option>";
	}
?>