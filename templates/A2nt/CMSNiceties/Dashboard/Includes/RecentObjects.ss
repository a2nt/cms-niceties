<div class="dashboard__block dashboard__block--objects">
    <h2 class="dashboard__block__title">
        $Title
    </h2>

    <% loop $Objects %>
        <div class="recent recent--object">
            <% if $CMSEditLink %>
                <a href="{$CMSEditLink}">
                    $Title
                    ($LastEdited.Ago)
                </a>
            <% else %>
                $Title
                ($LastEdited.Ago)
            <% end_if %>
        </div>
    <% end_loop %>
</div>
