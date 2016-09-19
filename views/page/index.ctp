
			<div class="clearfix rtm-vertical-tabs rtm-admin-tab-container rtm-settings-tab-container">
				<ul class="rtm-tabs">
					<li class="active">
						<a id="tab-rtmedia-display" title="Display" href="#my-aia-display" class="rtmedia-tab-title display">
							<i class="dashicons-desktop dashicons rtmicon"></i><span>Display</span>
						</a>
					</li>
					<li class="">
						<a id="tab-email" title="Email instellingen" href="#my-aia-email" class="rtmedia-tab-title privacy">
							<i class="dashicons-lock dashicons rtmicon"></i><span>Email</span>
						</a>
					</li>
					<li class="">
						<a id="tab-webshop" title="webshop instellingen" href="#my-aia-webshop" class="rtmedia-tab-title types">
							<i class="dashicons-editor-video dashicons rtmicon"></i><span>Webshop</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-bp" title="rtMedia BuddyPress" href="#rtmedia-bp" class="rtmedia-tab-title buddypress">
							<i class="dashicons-groups dashicons rtmicon"></i><span>BuddyPress</span>
						</a>
					</li>
					
					<li class="">
						<a id="tab-rtmedia-sizes" title="rtMedia Formaten" href="#rtmedia-sizes" class="rtmedia-tab-title media-sizes">
							<i class="dashicons-editor-expand dashicons rtmicon"></i><span>Media Sizes</span>
						</a>
					</li>
					<li class="">
						<a id="tab-email" title="Email instellingen" href="#my_aia_email" class="rtmedia-tab-title privacy">
							<i class="dashicons-lock dashicons rtmicon"></i><span>Email</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-custom-css-settings" title="rtMedia aangepaste CSS" href="#rtmedia-custom-css-settings" class="rtmedia-tab-title aangepaste-css">
							<i class="dashicons-clipboard dashicons rtmicon"></i><span>Aangepaste CSS</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-general" title="Andere instellingen" href="#rtmedia-general" class="rtmedia-tab-title andere-instellingen">
							<i class="dashicons-admin-tools dashicons rtmicon"></i><span>Andere instellingen</span>
						</a>
					</li>
				</ul>

				<div class="tabs-content rtm-tabs-content">
				<?php include "tab-display.ctp"; ?>
				<?php include "tab-email.ctp"; ?>
				<?php include "tab-webshop.ctp"; ?>
				</div>
			</div>
