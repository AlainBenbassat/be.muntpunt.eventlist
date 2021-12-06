<?php

use CRM_Eventlist_ExtensionUtil as E;

class CRM_Eventlist_Form_EventList extends CRM_Core_Form {
  protected $_pager = NULL;

  public function buildQuickForm() {
    $this->addFormFields();
    $this->addFormButtons();

    $filters = $this->getFilters();
    $this->pager($filters);
    [$offset, $rowCount] = $this->_pager->getOffsetAndRowCount();
    $rows = CRM_Eventlist_Helper::getEvents($filters, $offset, $rowCount);

    $this->assign('rows', $rows);
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function getDefaultEntity() {
    return 'Event';
  }

  public function postProcess() {
    parent::postProcess();
  }

  public function pager($filters) {
    $params['status'] = ts('Events %%StatusMessage%%');
    $params['csvString'] = '';
    $params['buttonTop'] = 'PagerTopButton';
    $params['buttonBottom'] = 'PagerBottomButton';
    $params['rowCount'] = $this->get(CRM_Utils_Pager::PAGE_ROWCOUNT);
    if (!$params['rowCount']) {
      $params['rowCount'] = 10;
    }

    $params['total'] = CRM_Eventlist_Helper::getNumberOfEvents($filters);

    $this->_pager = new CRM_Utils_Pager($params);
    $this->assign_by_ref('pager', $this->_pager);
  }

  private function addFormFields() {
    $this->addSelect('event_type_id', ['multiple' => TRUE, 'context' => 'search']);

    $locationEvents = CRM_Eventlist_Helper::getLocBlocList();
    $this->add('select', 'loc_block_id', 'Locatie', $locationEvents, FALSE, ['class' => 'crm-select2']);

    $mpRooms = [1 => 'Ketje', 2 => 'Ketje2'];
    $this->add('select', 'event_mp_rooms', 'Muntpunt zalen', $mpRooms, FALSE, ['multiple' => TRUE, 'class' => 'crm-select2']);

    $this->add('select', 'event_status', 'Status', [], FALSE, ['class' => 'crm-select2']);

    $this->add('text', 'event_title_contains', 'Titel bevat');

    $this->add('datepicker', 'event_start_date_from', 'Periode', [],FALSE, ['time' => FALSE, 'date' => 'yy-mm-dd', 'minDate' => '2000-01-01']);
    $this->add('datepicker', 'event_start_date_to', 'Periode tot', [],FALSE, ['time' => FALSE, 'date' => 'yy-mm-dd', 'minDate' => '2000-01-01']);
  }

  private function addFormButtons() {
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => 'Filter',
        'isDefault' => TRUE,
      ],
    ]);
  }

  private function getFilters() {
    $values = $this->exportValues();
    return $values;
  }

  private function getRenderableElementNames() {
    $elementNames = [];
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
