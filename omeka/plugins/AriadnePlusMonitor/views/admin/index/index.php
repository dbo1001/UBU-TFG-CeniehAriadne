<?php
$pageTitle = __('AriadnePlus Monitor');
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'ariadne-plus-monitor index',
));
?>
<div id="primary">
	<?php if (!empty($results)): ?>
       	<form method="POST">
       	<?php echo $this->formSelect('element_id', null, array('id' => 'element-id'), $options_for_select); ?>
       	<input type="submit" name="submit" value="Seleccionar" />
       	</form> 
   	<?php endif; ?>
<?php echo flash(); ?>
    <h2><?php
        echo  __('Total published items: %d / %d', get_db()->getTable('Item')->count(array('public' => 1)), total_records('Item'));
    ?></h2>
<?php

if (!empty($results)):
$statusElements = $this->monitor()->getStatusElements();
foreach ($results as $elementId => $result):
    if (!isset($statusElements[$elementId]['element'])):
        continue;
    endif;
    $element = $statusElements[$elementId]['element'];
?>
<section class="ten columns alpha omega">
    <div class="panel">
        <h2><?php echo $element->name; ?></h2>
        <?php $published = 0; ?>
        <table id="ariadne-plus-monitor-stats-<?php echo $element->id; ?>">
            <thead>
                <tr>
                    <?php
                    $browseHeadings = array();
                    $headers = array_keys(reset($result));
                    foreach ($headers as $header):
                        $browseHeadings[strlen($header) > 0 ? $header : __('Not Set')] = null;
                    endforeach;
                    echo browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => ''));
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $key = 0;
                foreach ($result as $period => $row): ?>
                <tr class="ariadne-plus-monitor-stat <?php echo ++$key%2 == 1 ? 'odd' : 'even'; ?>">
                    <?php
                        // Replace values "0" by empty string if wanted (default
                        // for "by" queries).
                        // $row = array_map(function($value) { return $value ?: '';}, $row);
                        echo '<td>' . implode('</td><td>', $row) . '</td>';
                    ?>
                </tr>
                <?php endforeach; ?>
                <tr>
                <?php foreach ($headers as $key => $header): ?>
                    <td>
                    <?php 
                        echo link_to_items_browse(__('Browse'),
                            array('collection' => $collectionId, 'advanced' => array(array('element_id' => $element->id, 'type' => 'is exactly', 'terms' => $header))),
                            array('class' => 'button small blue'));
                        if ($statusElements[$elementId]['steppable'] && $key < count($headers) - 1):
                            printf('<a href="%s" class="button small red">%s</a>',
                                html_escape(url('ariadne-plus-monitor/index/stage', array('element' => $element->id, 'collection' => $collectionId, 'term' => $header))),
                                __('Stage'));
                        endif;
                    ?>
                   </td>
                <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
</section>
<?php endforeach; ?>
<?php fire_plugin_hook('ariadne-plus_monitor_stat_element', array('view' => $this)); ?>
<?php else: ?>
    <br class="clear" />
    <p><?php
        echo __('Your query returned no result.');
        $statusElements = $this->monitor()->getStatusElements(true, null, true);
        if (empty($statusElements)):
            echo ' ' . __('There is no element that can be used as a status (a Monitor element with terms and unrepeatable).');
            echo ' ' . __('Check the options of the elements in Settings > Elements sets > "Monitor".');
        endif;
    ?></p>
<?php endif; ?>
</div>
<?php echo foot();