<?= head(array(
    'title' => __('ARIADNEplus Tracking'),
    ));
?>
<div id="primary">
    <?= flash(); ?>
    <h2><?= __('Step 1: Create a new ticket'); ?></h2>
<div id="selectoptions">
    <div class="center-div-step">
        <div id="questionsdiv">
            <div class="container-step initial-active-area">
                <div class="line">
                  <div class="step first"><img alt="Step 1" id="step-1" src="<?= htmlspecialchars(img('step-1.png')); ?>" /></i></div>
                  <div class="step second"> <img alt="Step 2" id="step-2" src="<?= htmlspecialchars(img('step-2.png')); ?>" /></div>
                  <div class="step third"> <img alt="Step 3" id="step-3" src="<?= htmlspecialchars(img('step-3.png')); ?>" /></div>
                </div>
                <div class="steps">
                    <form id="new-form" class="option-submission" method="post">
                        <div class="submission first-step">
                            <p><?= htmlspecialchars(__('Select the type of record you would like manage.')); ?></p>
                            <label class="input">
                                <?= $this->formSelect('record_type', null, array('id' => 'record-type'), $options_for_select_type); ?> <br/><br/>
                            </label>
                            <button class="first next"><?= htmlspecialchars(__('Continue')); ?></button>
                        </div>
                        <div class="submission second-step">
                            <p><?= htmlspecialchars(__('Choose the specific record.')); ?> </p>
                            <label class="input">              
                                <?= $this->formSelect('collection', null, array('id' => 'record-id-col'), $options_for_select_collection); ?> 
                                <?= $this->formSelect('item', null, array('id' => 'record-id-item'), $options_for_select_item); ?> <br/><br/>
                            </label>
                            <button class="second next"><?= htmlspecialchars(__('Continue')); ?></button>
                        </div>
                        <div id="last-form" class="submission third-step" method="post">
                            <h5><?= __('Selected record : '); ?><span class="selected-record"></span></h5>
                            <p> <?= __('Now select one of the fundamental ARIADNE categories which belongs to. '); ?></p>
                            <label class="input"> 
                                <?= $this->formSelect('ariadne_category', null, array('id' => 'ariadne-category'), $options_for_select_category); ?> <br/><br/>
                            </label>
                            <button type="submit" class="third next"><?= htmlspecialchars(__('Continue')); ?></button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
        jQuery(document).ready(function(){
        var type = jQuery("#record-type");
        var typeval;
        var record;
        var recordval;
        var selectedrecord = jQuery(".selected-record");

        jQuery(".first").click(function (event) {
            typeval = type.val();
            if (typeval == "") {
                Swal.fire({
                    icon: 'error',
                    title: 'No type!',
                    showConfirmButton:false,
                });
                return false;
            }   
            if(typeval == "Collection"){
                jQuery("#record-id-item").remove();
                record = jQuery("#record-id-col");
            } else {
                jQuery("#record-id-col").remove();
                record = jQuery("#record-id-item");
            }
            jQuery(".container-step").removeClass("third initial-active-area");
            jQuery(".container-step").addClass("second second-active-area");
            event.preventDefault();
        });

        jQuery(".second").click(function (event) {
            recordval = record.val();
            if (recordval == "") {
                Swal.fire({
                    icon: 'error',
                    title: 'No record!',
                    showConfirmButton:false,
                });
                return false;
            } else {
                selectedrecord.html(typeval + " " + recordval);
                if(typeval == 'Item'){
                    jQuery(".switcher").hide();
                }
            }
            jQuery(".container-step").removeClass("second second-active-area initial-active-area");
            jQuery(".container-step").addClass("third third-active-area");
            event.preventDefault();
        });
        
        jQuery("#new-form").submit(function () {
            Swal.fire({
                icon: 'success',
                title: 'The ticket has been created successfully!',
                text: 'Wait a moment, please...',
                showConfirmButton:false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
            });
            setTimeout(function(){
                return true;
            }, 2000);
        });
    });   
</script>