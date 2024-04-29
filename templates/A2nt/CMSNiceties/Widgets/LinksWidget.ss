<% with $Widget %>
    <% if $Links %>
        <% loop $Links %>
            <a href="$LinkURL"<% if $OpenInNewWindow %> target="_blank"<% end_if %> class="btn btn-default">
                $Title
            </a>
        <% end_loop %>
    <% end_if %>
<% end_with %>
