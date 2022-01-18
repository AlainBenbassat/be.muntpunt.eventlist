<?php

use CRM_Eventlist_ExtensionUtil as E;

class CRM_Eventlist_Form_EventList extends CRM_Core_Form {
  protected $_pager = NULL;
  public $formFilterNames = [];
  private $eventListHelper;

  public function __construct($state = NULL, $action = CRM_Core_Action::NONE, $method = 'post', $name = NULL) {
    $this->eventListHelper = new CRM_Eventlist_Helper();

    Civi::resources()->addStyleFile('be.muntpunt.eventlist', 'css/eventlist.css');

    parent::__construct($state, $action, $method, $name);
  }

  public function buildQuickForm() {
    $this->setTitle('Lijstweergave evenementen');
    $this->clearStoredFiltersIfNeeded();

    $this->addFormFields();
    $this->addFormButtons();

    $filters = $this->getFilters();
    $this->pager($filters);
    [$offset, $rowCount] = $this->_pager->getOffsetAndRowCount();
    $rows = $this->eventListHelper->getEvents($filters, $offset, $rowCount);

    $this->assign('rows', $rows);
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function getDefaultEntity() {
    return 'Event';
  }

  public function postProcess() {
    // the filters are not remembered when we use the pager, so we store them in the session
    $values = $this->getFilters();
    $this->storeFiltersInSession($values);

    parent::postProcess();
  }

  public function setDefaultValues() {
    return $this->getFilters();
  }

  public function pager($filters) {
    $params['status'] = ts('Events %%StatusMessage%%');
    $params['csvString'] = NULL;
    $params['buttonTop'] = 'PagerTopButton';
    $params['buttonBottom'] = 'PagerBottomButton';
    $params['rowCount'] = $this->get(CRM_Utils_Pager::PAGE_ROWCOUNT);
    if (!$params['rowCount']) {
      $params['rowCount'] = 10;
    }

    $params['total'] = $this->eventListHelper->getNumberOfEvents($filters);

    $this->_pager = new CRM_Utils_Pager($params);
    $this->assign_by_ref('pager', $this->_pager);
  }

  private function addFormFields() {
    $muntpuntConfig = CRM_Muntpuntconfig_Config::getInstance();

    $this->addSelect('event_type_id', ['multiple' => TRUE, 'context' => 'search']);
    $this->formFilterNames[] = 'event_type_id';

    $locationEvents = $this->eventListHelper->getLocBlocList();
    $this->add('select', 'loc_block_id', 'Locatie', $locationEvents, FALSE, ['class' => 'crm-select2']);
    $this->formFilterNames[] = 'loc_block_id';

    $list = $muntpuntConfig->getOptionValues_MuntpuntZalen(FALSE);
    $this->add('select', 'event_mp_rooms', 'Muntpunt zalen', $list, FALSE, ['multiple' => TRUE, 'class' => 'crm-select2']);
    $this->formFilterNames[] = 'event_mp_rooms';

    $list = $muntpuntConfig->getOptionValues_EvenementStatus(TRUE);
    $this->add('select', 'event_status', 'Status', $list, FALSE, ['class' => 'crm-select2']);
    $this->formFilterNames[] = 'event_status';

    $this->add('text', 'event_title_contains', 'Titel bevat');
    $this->formFilterNames[] = 'event_title_contains';

    $this->add('datepicker', 'event_start_date_from', 'Periode', [],FALSE, ['time' => FALSE, 'date' => 'yy-mm-dd', 'minDate' => '2000-01-01']);
    $this->formFilterNames[] = 'event_start_date_from';
    $this->add('datepicker', 'event_start_date_to', 'Periode tot', [],FALSE, ['time' => FALSE, 'date' => 'yy-mm-dd', 'minDate' => '2000-01-01']);
    $this->formFilterNames[] = 'event_start_date_to';
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
    $filters = [];

    $postedFilters = $this->exportValues();
    $storedFiltersSerialized = CRM_Core_Session::singleton()->get('event_list_filters');

    if ($storedFiltersSerialized) {
      $storedFilters = unserialize($storedFiltersSerialized);
    }

    foreach ($this->formFilterNames as $formFilterName) {
      // see if a filter was posted, if not see if we have it in the session
      if (!empty($postedFilters[$formFilterName])) {
        $filters[$formFilterName] = $postedFilters[$formFilterName];
      }
      elseif (!empty($storedFilters[$formFilterName])) {
        $filters[$formFilterName] = $storedFilters[$formFilterName];
      }
    }

    return $filters;
  }

  private function clearStoredFiltersIfNeeded() {
    if (CRM_Utils_Request::retrieve('clearfilters', 'Positive') == 1) {
      CRM_Core_Session::singleton()->set('event_list_filters', '');
    }
  }

  private function storeFiltersInSession($values) {
    $filtersToStore = [];

    foreach ($this->formFilterNames as $formFilterName) {
      if (!empty($values[$formFilterName])) {
        $filtersToStore[$formFilterName] = $values[$formFilterName];
      }
    }

    if (count($filtersToStore)) {
      CRM_Core_Session::singleton()->set('event_list_filters', serialize($filtersToStore));
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
