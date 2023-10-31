<img src="$Image.FocusFill(432,315).URL" alt="$Title" />
<% if $Linked %>
    <% with $Linked %>
    <a href="$LinkURL"<% if $OpenInNewWindow %> target="_blank"<% end_if %> class="stretched-link">
        <span class="visually-hidden">$Title</span>
    </a>
    <% end_with %>
<% end_if %>
