<% if $RecentFiles %>
<div class="dashboard__block dashboard__block--files">
    <h2 class="dashboard__block__title">
        <%t A2nt\CMSNiceties\Dashboard\Dashboard.RecentFiles 'Recent Files' %>
    </h2>

    <% loop $RecentFiles %>
        <div class="recent recent--file">
            <a href="{$CMSEditLink}">
                $Name
                ($LastEdited.Ago)
            </a>
        </div>
    <% end_loop %>
</div>
<% end_if %>