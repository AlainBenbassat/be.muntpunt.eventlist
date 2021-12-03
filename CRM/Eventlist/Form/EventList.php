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

  public function postProcess() {
    parent::postProcess();
  }

  public function pager($filters) {
    $params['status'] = ts('Contribution %%StatusMessage%%');
    $params['csvString'] = NULL;
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
    $this->add('text', 'test_field', 'Test field');
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
    if (empty($values['test_field'])) {
      return 1;
    }
    else {
      return $values['test_field'];
    }
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
