<a
    href="{$Link}"
    class="
        nav-link
        graphql-page
        $ExtraClass
        {$LinkClass}
        <% if $RedirectionType = 'External' || $ExternalURL || $OpenInNewWindow %>
            legacy
        <% end_if %>
        <% if $isCurrent || $isSection %>active<% end_if %>
        <% if $isSection %>section<% end_if %>
    "
    <% if $OpenInNewWindow %>
        rel="noreferrer"
        target="_blank"
    <% end_if %>

    data-text="{$MenuTitle.XML}"
>
    <% if $BlockIcon %>
        <i class="fa-icon $BlockIcon"></i>
    <% end_if %>
    $MenuTitle.XML
    <% if $isCurrent || $isSection %><i class="visually-hidden">(current)</i><% end_if %>
</a>
