<label class="ti-left-label"><span><?php echo TrustindexPlugin::___("Your business name or URL on %s", [ "Tripadvisor" ]); ?>:</span></label>
<div class="input">
<input class="form-control name"
placeholder="<?php echo TrustindexPlugin::___("e.g.:") . ' ' . esc_attr($example .' / '. $example_url); ?>"
type="text" required="required"
data-url=true
/>
<span class="info-text"><?php echo TrustindexPlugin::___("Type your business/company's name or URL and select from the list"); ?></span>
<img class="loading" src="<?php echo admin_url('images/loading.gif'); ?>" />
<div class="results"
data-errortext="<?php echo TrustindexPlugin::___("Something went wrong."); ?>"
data-noresultstext="<?php echo TrustindexPlugin::___("No results. %s cannot find your business by the terms you gave. Do not panic! There is an unique business search function in Trustindex, you only need to register for free and it will help you to find your business/store. Check out the next tab, called 'More Features'!", [" Tripadvisor"]); ?>"
data-tooshorttext="<?php echo TrustindexPlugin::___("Too short! Please enter your business' name and city, if applicable"); ?>"
></div>
</div>
<button class="btn btn-text btn-search"><?php echo TrustindexPlugin::___("Search") ;?></button>