<% if $RecentPages %>
<div class="dashboard__block dashboard__block--pages">
    <h2 class="dashboard__block__title">
        <%t A2nt\CMSNiceties\Dashboard\Dashboard.RecentPages 'Recent Pages' %>
    </h2>

    <% loop $RecentPages %>
        <div class="recent recent--page">
            <a href="{$CMSEditLink}">
                $MenuTitle
                ($LastEdited.Ago)
            </a>
        </div>
    <% end_loop %>
</div>
<% end_if %>