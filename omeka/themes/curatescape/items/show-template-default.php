<?php 
$maptype='story';
$hasimg = metadata($item, 'has thumbnail');
if ($hasimg === 1) {
	$img_markup = item_image('fullsize',array(),0, $item);
	preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $img_markup, $result);
	$hero_img = array_pop($result);
	$noimg= false;
} else {
	$noimg= true;
	$hero_img = img('thumbnailDefault.jpg');
}
	
echo head(array(
	'item'=>$item, 
	'maptype'=>$maptype, 
	'bodyid'=>'items', 
	'bodyclass'=>'show item-story',
	'title' => metadata($item,array('Dublin Core', 'Title')),
	)); ?>

<article class="story item show" role="main" id="content">
			
	<header id="story-header">
		<?php
			echo '<div class="item-hero hero" style="background-image: url('.$hero_img.')">';
			echo '<div class="item-hero-text">'.mh_the_title().mh_the_subtitle().mh_the_byline($item,true).'</div>';
			echo '</div>';	
			if(function_exists('tour_nav')){
				$tournavhtml=tour_nav(null,mh_tour_label('singular'),get_theme_option('tour_nav_always'),$item->id);
				echo $tournavhtml ? '<nav aria-label="'.__('Tour Navigation - Top').'" class="tour-nav-container top">'.$tournavhtml.'</nav>' : null;
			}
			echo mh_the_lede();
		?>
		<?php //TODO: echo item_is_private($item);?>
	</header>
	<section class="text">
		<h2 hidden class="hidden"><?php echo __('Text');?></h2>
		<?php echo mh_the_text(); ?>
	</section>
	
	<section class="media">
		<h2 hidden class="hidden"><?php echo __('Media');?></h2>
		<?php mh_video_files($item);?>
		<?php if ($hasimg) mh_item_images($item);?>	
		<?php mh_audio_files($item);?>	
		<?php mh_document_files($item);?>		
	</section>
	<?php if(mh_get_item_json($item)): ?>
	<section class="map">
		<h2>Map</h2>
		<nav aria-label="<?php echo __('Skip Interactive Map');?>"><a id="skip-map" href="#map-actions-anchor"><?php echo __('Skip Interactive Map');?></a></nav>
		<figure>
			<?php echo mh_map_type($maptype,$item); ?>
			<?php echo mh_map_actions($item);?>
			<figcaption><?php echo mh_map_caption();?></figcaption>
		</figure>
	</section>
	<?php endif;?>
	
	<?php echo mh_factoid(); ?>
	
	<section id="simplemeta" class="metadata">
		<h2><?php echo __('Item metadata');?></h2>
		<?php echo mh_official_website();?>	
		
		<?php echo function_exists('tours_for_item') ? tours_for_item($item->id, __('Related %s', mh_tour_label('plural'))) : null?>
		<?php echo mh_subjects(); ?>
		<?php echo mh_related_links();?>
                <?php echo mh_item_citation(); ?>
                <button id="showfull">Show all metadata</button>
	</section>	
  
        <section id="fullmeta" class="metadata" hidden="">
          <h2><?php echo __('Item metadata');?></h2>
          <?php echo all_element_texts($item, array('show_element_sets' => 'Dublin Core', 'partial' => 'common/record-metadata-theme.php'));?>
          <?php echo mh_item_citation(); ?>
          <button id="showsimple">Show simple metadata</button>
        </section>
        
	<section>
		<?php echo mh_post_date(); ?>		
		<?php echo mh_display_comments();?>
	</section>
  
        

	<?php echo mh_share_this(mh_item_label());?>
	
	<?php echo function_exists('tour_nav') ? '<nav aria-label="'.__('Tour Navigation - Bottom').'" class="tour-nav-container bottom">'.tour_nav(null,mh_tour_label()).'</nav>' : null; ?>
</article>
<?php echo foot(); ?>
<script>
    jQuery(document).ready(function() {
        jQuery('#showfull').click( function(){
            jQuery('#simplemeta').hide();
            jQuery('#fullmeta').show();
        });
        
        jQuery('#showsimple').click( function(){
            jQuery('#fullmeta').hide();
            jQuery('#simplemeta').show();
        });
    });
</script>