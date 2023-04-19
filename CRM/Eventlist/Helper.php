<?php

class CRM_Eventlist_Helper {
  private $muntpuntConfig;

  public function __construct() {
    $this->muntpuntConfig = CRM_Muntpuntconfig_Config::getInstance();
  }

  public function getEvents($filters, $offset, $rowCount) {
    $from = $this->getFrom();
    [$whereClause, $sqlParams] = $this->convertFiltersToWhereClause($filters);

    if ($whereClause) {
      $where = " where $whereClause ";
    }

    $countParticipantPositive = $this->getcountParticipantQuery(1);
    $countParticipantNegative = $this->getcountParticipantQuery(0);

    $sql = "
      select
        e.id,
        ep.id ep_id,
        eei_event_status.label status,
        e.title titel,
        ov.label type,
        ifnull(a.name, concat(a.street_address, ' :: ', a.city)) locatie,
        eei.muntpunt_zalen muntpunt_zalen,
        date_format(e.start_date, '%d %b %Y %H:%i') begindatum,
        date_format(e.end_date, '%d %b %Y %H:%i') eind,
        c_aanspr.display_name aanspreekpersoon,
        c_org.display_name organisator,
        ep.verwachte_deelnemers  verwacht,
        ($countParticipantPositive) geregistreerd,
        ($countParticipantNegative) geannuleerd,
        ep.geschatte_deelnemers  effectief,
        case when ifnull(e.max_participants, '') = '' then 'Onbeperkt' else e.max_participants end maximum,
        'XXX' beschikbaar,
        'XXX' beheer
      $from
      $where
      order by
        start_date
      limit
        $offset, $rowCount
    ";
    $dao = CRM_Core_DAO::executeQuery($sql, $sqlParams);
    $rows = $dao->fetchAll();

    $this->processSpecialFields($rows);

    return $rows;
  }

  private function getFrom() {
    $eventTypeOptionGroupId = 15;
    $eventStatusOptionGroupId = $this->muntpuntConfig->getOptionGroupId_EvenementStatus();

    $from = "
      from
        civicrm_event e
      inner join
        civicrm_option_value ov on e.event_type_id = ov.value and ov.option_group_id = $eventTypeOptionGroupId
      left outer join
        civicrm_loc_block lb on e.loc_block_id = lb.id
      left outer join
        civicrm_address a on a.id = lb.address_id
      left outer join
        civicrm_value_extra_evenement_info eei on eei.entity_id = e.id
      left outer join
        civicrm_option_value eei_event_status on eei_event_status.value = eei.activiteit_status and eei_event_status.option_group_id = $eventStatusOptionGroupId
      left outer join
        civicrm_value_evenement_planning_memo_overleg_en_statistiek ep on ep.entity_id = e.id
      left outer join
        civicrm_contact c_org on c_org.id = eei.organisator
      left outer join
        civicrm_contact c_aanspr on c_aanspr.id = ep.aanpreekpersoon
    ";

    return $from;
  }

  private function getcountParticipantQuery($isCounted) {
    $pAlias = "p$isCounted";
    $cAlias = "c$isCounted";
    $sAlias = "s$isCounted";

    $sql = "
      select
        count(*)
      from
        civicrm_participant $pAlias
      inner join
        civicrm_contact $cAlias on $pAlias.contact_id = $cAlias.id
      inner join
        civicrm_participant_status_type $sAlias on $pAlias.status_id = $sAlias.id
      where
        $cAlias.is_deleted = 0
      and
        $cAlias.contact_type = 'Individual'
      and
        $pAlias.event_id = e.id
      and
        $sAlias.is_counted = $isCounted
      and
        $sAlias.is_active = 1
    ";

    return $sql;
  }

  public function getNumberOfEvents($filters) {
    $from = $this->getFrom();
    [$whereClause, $sqlParams] = $this->convertFiltersToWhereClause($filters);

    $sql = "
      select
        count(*)
      $from
    ";

    if ($whereClause) {
      $sql .= " where $whereClause ";
    }

    return CRM_Core_DAO::singleValueQuery($sql, $sqlParams);
  }

  public function convertFiltersToWhereClause($values) {
    $sqlWhere = '';
    $sqlParams = [];
    $filters = [];

    if (!empty($values['event_title_contains'])) {
      $filters['event_title_contains'] = ['e.title', 'like', '%' . $values['event_title_contains'] . '%', 'String'];
    }

    if (!empty($values['event_type_id'])) {
      $filters['event_type_id'] = ['e.event_type_id', 'in', implode(',', $values['event_type_id']), 'CommaSeparatedIntegers'];
    }

    if (!empty($values['loc_block_id'])) {
      $filters['loc_block_id'] = ['lb.address_id', '=', $values['loc_block_id'], 'Integer'];
    }

    if (!empty($values['event_start_date_from'])) {
      $filters['event_start_date_from'] = ['e.start_date', '>=', $values['event_start_date_from'] . ' 00:00', 'String'];
    }

    if (!empty($values['event_start_date_to'])) {
      $filters['event_start_date_to'] = ['e.start_date', '<=', $values['event_start_date_to'] . ' 23:59', 'String'];
    }

    if (!empty($values['event_mp_rooms'])) {
      $f = [];
      foreach ($values['event_mp_rooms'] as $room) {
        $f[] = "eei.muntpunt_zalen like '%" . CRM_Core_DAO::VALUE_SEPARATOR . $room . CRM_Core_DAO::VALUE_SEPARATOR . "%'";
      }
      $filters['event_mp_rooms'] = '(' . implode(' or ', $f) . ')';
    }

    if (!empty($values['event_status'])) {
      $filters['event_status'] = ['eei.activiteit_status', '=', $values['event_status'], 'Integer'];
    }

    if (array_key_exists('event_online_registration', $values)) {
      if ($values['event_online_registration'] === '0' ||  $values['event_online_registration'] === '1') {
        $filters['event_online_registration'] = ['e.is_online_registration', '=', (int)$values['event_online_registration'], 'Integer'];
      }
    }

    $i = 1;
    foreach ($filters as $filter) {
      if (strlen($sqlWhere) > 0) {
        $sqlWhere .= ' and ';
      }

      if (is_array($filter)) {
        if ($filter[3] == 'CommaSeparatedIntegers') {
          $sqlWhere .= $filter[0] . ' ' . $filter[1] . "(%$i)";
        }
        else {
          $sqlWhere .= $filter[0] . ' ' . $filter[1] . ' %' . $i;
        }

        $sqlParams[$i] = [$filter[2], $filter[3]];
      }
      else {
        $sqlWhere .= " $filter ";
      }

      $i++;
    }

    return [$sqlWhere, $sqlParams];
  }

  public function getLocBlocList() {
    $locBlocks = [
      '' => ' - Elke - '
    ];

    $sql = "
      select
        max(a.id) id,
        a.name,
        a.street_address
      from
        civicrm_loc_block lb
      inner join
        civicrm_address a on lb.address_id = a.id
      group by
        a.name, a.street_address
      order by
        a.name
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);

    while ($dao->fetch()) {
      $locBlocks[$dao->id] = $dao->street_address ? $dao->name . ' (' . $dao->street_address . ')' : $dao->name;
    }

    return $locBlocks;
  }

  private function processSpecialFields(&$rows) {
    for ($i = 0; $i < count($rows); $i++) {
      $rows[$i]['muntpunt_zalen'] = $this->replaceValueSeparator($rows[$i]['muntpunt_zalen']);
      $rows[$i]['status'] = $this->setStatusColor($rows[$i]['status']);
      $rows[$i]['beheer'] = $this->getAdminLinks($rows[$i]['id']);
      $rows[$i]['beschikbaar'] = $this->calculatePlacesLeft($rows[$i]['geregistreerd'], $rows[$i]['maximum']);
    }
  }

  private function setStatusColor($status) {
    if ($status == 'Goedgekeurd') {
      $status = '<span style="color: #17c700">' . $status . '</span>';
    }

    return $status;
  }

  private function replaceValueSeparator($field) {
    if ($field) {
      $values = explode(CRM_Core_DAO::VALUE_SEPARATOR, $field);
      $valueString = '';

      foreach ($values as $value) {
        if ($value) {
          if ($valueString) {
            $valueString .= ', ';
          }

          $valueString .= $value;
        }
      }
    }
    else {
      $valueString = $field;
    }

    return $valueString;
  }

  private function getAdminLinks($eventId) {
    $actionLinks = '<div class="muntpunt-events-actions">';
    $actionLinks .= '<span class="muntpunt-events-list-edit"><a href="event/manage/settings?reset=1&amp;action=update&amp;id=' . $eventId . '" target="_blank" title="Bewerken"><i class="fa fa-edit"></i></a></span>';
    $actionLinks .= '<span class="muntpunt-events-list-add-participant"><a href="participant/add?reset=1&amp;action=add&amp;context=standalone&amp;eid=' . $eventId . '" target="_blank" title="Deelnemer inschrijven"><i class="fa fa-plus-circle"></i></a></span>';
    $actionLinks .= '<span class="muntpunt-events-list-info"><a href="event/info?reset=1&amp;id=' . $eventId . '" target="_blank" title="Informatiepagina"><i class="fa fa-search"></i></a></span>';
    $actionLinks .= '<span class="muntpunt-events-list-entry"><a href="event/register?reset=1&amp;id=' . $eventId . '" target="_blank" title="Inschrijvingspagina"><i class="fa fa-file"></i></a></span>';
    $actionLinks .= '<span class="muntpunt-events-list-delete"><a href="event/manage?reset=1&amp;action=delete&amp;id=' . $eventId . '" target="_blank" title="Verwijderen"><i class="fa fa-trash"></i></a></span>';
    $actionLinks .= '<span class="muntpunt-events-list-copy"><a href="event/manage?reset=1&amp;action=copy&amp;id=' . $eventId . '" target="_blank" title="Kopiëren"><i class="fa fa-copy"></i></a></span>';
    $actionLinks .= '</div>';

    return $actionLinks;
  }

  private function calculatePlacesLeft($numRegistered, $maxSeats) {
    if ($maxSeats == 'Onbeperkt') {
      $placesLeft = 'Onbeperkt';
    }
    else {
      $placesLeft = $maxSeats - $numRegistered;
    }

    return $placesLeft;
  }
}
