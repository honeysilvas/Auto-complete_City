<style>
.ui-autocomplete-loading {
	background: white url( <?php echo plugins_url( "/hs_city_autocomplete/source/asset/image/ui-anim_basic_16x16.gif" ); ?> ) right center no-repeat;
}
</style>

<script>
	jQuery( function() {
		jQuery( "#city" ).autocomplete({
			source: "<?php echo site_url(); ?>/cities-json",
			minLength: 3,		
			select: function( event, ui ) {		
				window.location = "<?php echo site_url(); ?>/" + ui.item.slug;
				return false;
			}
		})
	});	

</script>

<div class="ui-widget">
	<label for="city">City: </label>
	<input id="city" type="text" />
</div>
	
