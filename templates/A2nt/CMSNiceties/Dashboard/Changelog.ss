<h2>Changelog</h2>
<ol>

    <% if $Versions %>
        <% loop $Versions %>
            <li>
                <% if $First %>
                    Current Version #{$Version} {$relField('LastEdited')}
                    <% if $Author %>
                        (Author $Author.Title)
                    <% end_if %>
                <% else %>
                    <a href="{$Top.CMSEditLink($Version)}">
                        Version #{$Version}
                        {$relField('LastEdited')}
                        <% if $Author %>
                            (Author $Author.Title)
                        <% end_if %>
                    </a>
                <% end_if %>
            </li>
        <% end_loop %>
    <% end_if %>

    <li>
        Created {$Created.Ago}
        <% if $Author %>
            (Author $Author.Title)
        <% end_if %>
    </li>
</ol>