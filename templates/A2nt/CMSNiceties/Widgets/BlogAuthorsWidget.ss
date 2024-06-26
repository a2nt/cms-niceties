<% cached 'blogauthorswidget', $List('SilverStripe\Blog\Model\BlogPost').max('LastEdited'), $List('SilverStripe\Blog\Model\BlogPost').count() %>
    <% if $Authors %>
        <div class="blog-post__credits">
        <% loop $Authors %>
            <% if $URLSegment && not $Up.ProfilesDisabled %>
                <a href="$URL" class="blog-post__credit">$Name.XML</a>
            <% else %>
                <span class="blog-post__credit">$Name.XML</span>
            <% end_if %>
        <% end_loop %>
        </div>
    <% end_if %>
<% end_cached %>
