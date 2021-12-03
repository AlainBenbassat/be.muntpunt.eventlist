<?php

class CRM_Eventlist_Helper {

  public static function getEvents($filters, $offset, $rowCount) {
    $eventTypeOptionGroupId = 15;

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
      order by
        start_date desc
      limit
        $offset, $rowCount
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $rows = $dao->fetchAll();
    return $rows;
  }

  public function getNumberOfEvents($filters) {
    $sql = "
      select
        count(*)
      from
        civicrm_event
    ";
    return CRM_Core_DAO::singleValueQuery($sql);
  }
}
