<fieldset id="ariadne-plus-tracking-item-metadata">
    <h2><?= __('ARIADNEplus Tracking'); ?></h2>
    <p class="explanation">
        <?= __('Set these status for the selected items.'); ?>
        <?= __('Note that some elements may be automatically set.'); ?>
    </p>
<?php foreach ($statusTermsElements as $elementId => $statusElement): ?>
    <div class="field">
        <div class="two columns alpha">
            <?= $this->formLabel('ariadne-plus-tracking-element-' . $elementId, $statusElement['name']); ?>
        </div>
        <div class='inputs five columns omega'>
            <?php
            $options = array();
            $options[''] = __('Select Below');
            $options += $statusElement['terms'];
            $options['remove'] = __('[Remove value]');?>
            <?= $this->formSelect('custom[ariadneplustracking][statusterms][element-' . $elementId. ']', null, array(), $options); ?>
        </div>
    </div>
<?php endforeach; ?>
</fieldset>
<?php if($batch_edit_disable): ?>
<script type="text/javascript">
    alert("Batch edit is disabled.");
    jQuery("input").attr('disabled',true);
    jQuery("select").attr('disabled',true);
</script>
<?php endif; 
