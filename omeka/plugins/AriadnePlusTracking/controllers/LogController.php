<?php
/**
 * The ARIADNEplusTracking log controller class.
 *
 * @package AriadnePlusTracking
 */
class AriadnePlusTracking_LogController extends Omeka_Controller_AbstractActionController
{
    /**
     * Set up the view for full record reports.
     *
     * @return void
     */
    public function logAction()
    {
        $flashMessenger = $this->_helper->FlashMessenger;
        $recordType = $this->_getParam('type');
        $recordId = $this->_getParam('id');
        $mode = $this->_getParam('mode');
        if (empty($recordType) || empty($recordId)) {
            $flashMessenger->addMessage(__('Record not selected.'), 'error');
        }

        $recordType = Inflector::classify($recordType);
        $recordId = (integer) $recordId;

        $record = get_record_by_id($recordType, $recordId);

        $this->view->record = $record ?: array(
            'record_type' => get_class($recordType),
            'record_id' => (integer) $recordId,
        );
        $this->view->mode = $mode;
    }
}
