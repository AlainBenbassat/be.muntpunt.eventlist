

{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

<hr>

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
        <tr id="event_list-{$row.id}" class="crm-entity">
          <td>{$row.status}</td>
          <td><a href="event/manage/settings?reset=1&action=update&id={$row.id}">{$row.titel}</a></td>
          <td>{$row.type}</td>
          <td>{$row.locatie}</td>
          <td>{$row.muntpunt_zalen}</td>
          <td>{$row.begindatum}</td>
          <td>{$row.eind}</td>
          <td>{$row.aanspreekpersoon}</td>
          <td>{$row.organisator}</td>
          <td>{$row.verwacht}</td>
          <td><a href="event/search?reset=1&force=1&status=true&event={$row.id}">{$row.geregistreerd}</a></td>
          <td><a href="event/search?reset=1&force=1&status=false&event={$row.id}">{$row.geannuleerd}</a></td>
          <td>{$row.effectief}</td>
          <td>{$row.maxnum}</td>
          <td>{$row.beschikbaar}</td>
          <td>{$row.beheer}</td>
        </tr>
      {/foreach}
      </tbody>
    </table>

    {include file="CRM/common/pager.tpl" location="bottom"}
  </div>
{/if}

