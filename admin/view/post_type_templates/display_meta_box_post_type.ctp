<p>Selecteer post type waar template bij hoort:</p>

<select name="parent_type">
	<?php
		foreach (MY_AIA::$CUSTOM_POST_TYPES as $post_type) {
			echo sprintf('<option value="%s" %s>%s</option>', $post_type, ($post_type == $this->parent_type)?'selected':'', $post_type);
		}
	?>
</select>

<p>Mogelijke velden van dit post type:</p>
<ul id="template_field_list"></ul>

