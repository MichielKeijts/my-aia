<?php
/**
 * (c) 2016
 * Menu Tabs MY-AIA plugin 
 */
?>
<h2 class="nav-tab-wrapper">
	<?php 
		echo $this->Html->link(__('Algemeen','my-aia'), array('controller'=>'page'), array('class'=>'nav-tab'.($this->controller->get_controller_name()=='page'?' nav-tab-active':''))); 
		//echo $this->Html->link(__('Procesflow','my-aia'), array('controller'=>'processflow'), array('class'=>'nav-tab'.($this->controller->get_controller_name()=='processflow'?' nav-tab-active':'')));
		echo $this->Html->link(__('Profiel & Registratie','my-aia'), array('controller'=>'profile'), array('class'=>'nav-tab'.($this->controller->get_controller_name()=='profile'?' nav-tab-active':'')));
		echo $this->Html->link(__('Email','my-aia'), array('controller'=>'email'), array('class'=>'nav-tab'.($this->controller->get_controller_name()=='email'?' nav-tab-active':'')));
		echo $this->Html->link(__('Sync','my-aia'), array('controller'=>'sync'), array('class'=>'nav-tab'.($this->controller->get_controller_name()=='sync'?' nav-tab-active':'')));
	?>
	<span class="alignright by">
		<a class="my-aia-link" href="http://normit.nl" target="_blank" title="Normit : <?php _e( 'Custom Webapplications with a heart', 'my-aia' ); ?>">
			<img src="<?php echo MY_AIA_PLUGIN_URL; ?>assets/img/my-aia-logo.png" alt="My AIA" />
		</a>
	</span>
</h2>

<div id="my-aia-menu-bar">
	<ul>
	<?php foreach ((isset($menu_bar)?$menu_bar:array()) as $key=>$menu_item): ?>
		<li><?= $this->Html->link($menu_item, array('action'=>$key), array('class'=>'button')); ?></li>
	<?php endforeach; // menu_items?>
	</ul>
</div>
				
