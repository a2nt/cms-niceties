<% if $Submenu %>
    <nav>
        <ul class="nav flex-column">
        	<% if $TopLevelSubmenu %>
                <% with $Page.Level(1) %>
                    <li class="nav-item-level1 nav-item {$CSSClass} $ExtraClass">
                        <b class="nav-link">
                            $MenuTitle.XML
                            <% if $isCurrent || $isSection %><i class="visually-hidden">(current)</i><% end_if %>
                        </b>
                    </li>
                <% end_with %>
            <% else %>
                <% with $Page %>
                    <li class="nav-item-level1 nav-item {$CSSClass} $ExtraClass">
                        <b class="nav-link">
                            $MenuTitle.XML
                            <% if $isCurrent || $isSection %><i class="visually-hidden">(current)</i><% end_if %>
                        </b>
                    </li>
                <% end_with %>
            <% end_if %>

            <% loop $Submenu %>
                <% include NavItem %>
            <% end_loop %>
        </ul>
    </nav>
<% end_if %>
