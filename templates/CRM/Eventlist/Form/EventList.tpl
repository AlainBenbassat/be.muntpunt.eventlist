<table class="form-layout">
  <tr>
    <td>
        {$form.event_status.label}<br>
        {$form.event_status.html}
    </td>
    <td>
        {$form.event_title_contains.label}<br>
        {$form.event_title_contains.html}
    </td>
    <td>
      {$form.event_type_id.label}<br>
      {$form.event_type_id.html}
    </td>
    <td>
        {$form.loc_block_id.label}<br>
        {$form.loc_block_id.html}
    </td>
    <td>
        {$form.event_mp_rooms.label}<br>
        {$form.event_mp_rooms.html}
    </td>
    <td>
        {$form.event_online_registration.label}<br>
        {$form.event_online_registration.html}
    </td>
  </tr>

  <tr>
    <td colspan="6">
      {$form.event_start_date_from.label}<br>
      van {$form.event_start_date_from.html} tot en met {$form.event_start_date_to.html}
    </td>
  </tr>

  <tr>
    <td colspan="5">{include file="CRM/common/formButtons.tpl"} <a href="?reset=1&clearfilters=1">filters wissen</a></td>
  </tr>
</table>

{if $rows}
  <div id="muntpunt_event_list">
    {include file="CRM/common/pager.tpl" location="top"}

    <table id="options" class="display">
      <thead>
      <tr>
        <th>Status</th>
        <th>Titel</th>
        <th>Type</th>
        <th>Locatie</th>
        <th>Muntpunt zalen</th>
        <th>Begindatum</th>
        <th>Eind</th>
        <th>Aanspreekpersoon</th>
        <th>Organisator</th>
        <th>Verwacht</th>
        <th>Geregistreerd</th>
        <th>Geannuleerd</th>
        <th>Effectief</th>
        <th>Max.</th>
        <th>Beschikbaar</th>
        <th>Beheer</th>
      </tr>
      </thead>
      <tbody>
      {foreach from=$rows item=row}
        <tr id="CustomValue-{$row.ep_id}" class="crm-entity" >
          <td>{$row.status}</td>
          <td><a href="event/manage/settings?reset=1&action=update&id={$row.id}">{$row.titel}</a></td>
          <td>{$row.type}</td>
          <td>{$row.locatie}</td>
          <td>{$row.muntpunt_zalen}</td>
          <td>{$row.begindatum}</td>
          <td>{$row.eind}</td>
          <td>{$row.aanspreekpersoon}</td>
          <td>{$row.organisator}</td>
          <td class="crm-editable crmf-{$custom_field_verwacht}" data-params='{ldelim}"entity_id":"{$row.id}"{rdelim}'>{$row.verwacht}</td>
          <td><a href="event/search?reset=1&force=1&status=true&event={$row.id}">{$row.geregistreerd}</a></td>
          <td><a href="event/search?reset=1&force=1&status=false&event={$row.id}">{$row.geannuleerd}</a></td>
          <td class="crm-editable crmf-{$custom_field_geschat}" data-params='{ldelim}"entity_id":"{$row.id}"{rdelim}'>{$row.effectief}</td>
          <td>{$row.maximum}</td>
          <td>{$row.beschikbaar}</td>
          <td>{$row.beheer}</td>
        </tr>
      {/foreach}
      </tbody>
    </table>

    {include file="CRM/common/pager.tpl" location="bottom"}
  </div>
{/if}

