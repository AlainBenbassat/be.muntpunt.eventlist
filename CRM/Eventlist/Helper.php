<?php

class CRM_Eventlist_Helper {

  public static function getEvents($filters, $offset, $rowCount) {
    $eventTypeOptionGroupId = 15;
    [$whereClause, $sqlParams] = self::convertToWhereClause($filters);

    if ($whereClause) {
      $where = " where $whereClause ";
    }

    $sql = "
      select
        e.id,
       'XXX' status,
        e.title titel,
        ov.label type,
        a.name locatie,
        'XXX' muntpunt_zalen,
        date_format(start_date, '%d %b, %Y %h:%i') begindatum,
        date_format(start_date, '%d %b, %Y %h:%i') eind,
        'XXX' aanspreekpersoon,
        'XXX' organisator,
        'XXX' verwacht,
        (select count(*) from civicrm_participant p1 inner join civicrm_contact c1 on p1.contact_id = c1.id where c1.is_deleted = 0 and p1.event_id = e.id and p1.status_id in (1,2)) geregistreerd,
        (select count(*) from civicrm_participant p2 inner join civicrm_contact c2 on p2.contact_id = c2.id where c2.is_deleted = 0 and p2.event_id = e.id and p2.status_id not in (1,2)) geannuleerd,
        'XXX' effectief,
        'XXX' maxnum,
        'XXX' beschikbaar,
        'XXX' beheer
      from
        civicrm_event e
      inner join
        civicrm_option_value ov on e.event_type_id = ov.value and ov.option_group_id = $eventTypeOptionGroupId
      left outer join
        civicrm_loc_block lb on e.loc_block_id = lb.id
      left outer join
        civicrm_address a on a.id = lb.address_id
      $where
      order by
        start_date desc
      limit
        $offset, $rowCount
    ";
    $dao = CRM_Core_DAO::executeQuery($sql, $sqlParams);
    $rows = $dao->fetchAll();
    return $rows;
  }

  public static function getNumberOfEvents($filters) {
    [$whereClause, $sqlParams] = self::convertToWhereClause($filters);

    $sql = "
      select
        count(*)
      from
        civicrm_event e
    ";

    if ($whereClause) {
      $sql .= " where $whereClause ";
    }

    return CRM_Core_DAO::singleValueQuery($sql, $sqlParams);
  }

  public static function convertToWhereClause($values) {
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
      $filters['loc_block_id'] = ['e.loc_block_id', '=', $values['loc_block_id'], 'Integer'];
    }

    if (!empty($values['event_start_date_from'])) {
      $filters['event_start_date_from'] = ['e.start_date', '>=', $values['event_start_date_from'] . ' 00:00', 'String'];
    }

    if (!empty($values['event_start_date_to'])) {
      $filters['event_start_date_to'] = ['e.start_date', '<=', $values['event_start_date_to'] . ' 23:59', 'String'];
    }

    $i = 1;
    foreach ($filters as $filter) {
      if (strlen($sqlWhere) > 0) {
        $sqlWhere .= ' and ';
      }

      if ($filter[3] == 'CommaSeparatedIntegers') {
        $sqlWhere .= $filter[0] . ' ' . $filter[1] . "(%$i)";
      }
      else {
        $sqlWhere .= $filter[0] . ' ' . $filter[1] . ' %' . $i;
      }

      $sqlParams[$i] = [$filter[2], $filter[3]];

      $i++;
    }

    return [$sqlWhere, $sqlParams];
  }

  public static function getLocBlocList() {
    $locBlocks = [
      '' => ' - Elke -'
    ];

    $sql = "
      select
        lb.id,
        a.name,
        a.street_address
      from
        civicrm_loc_block lb
      inner join
        civicrm_address a on lb.address_id = a.id
      order by
        a.name
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);

    while ($dao->fetch()) {
      $locBlocks[$dao->id] = $dao->street_address ? $dao->name . ' (' . $dao->street_address . ')' : $dao->name;
    }

    return $locBlocks;
  }
}
