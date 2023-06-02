<% with $SiteConfig %>
    <% if $HolidayToday %>
        <div class="hours hours--closed">
            CLOSED
        </div>
    <% else %>
        <% if $OpeningHoursToday %>
            <div class="hours hours--open">
                <% loop $OpeningHoursToday %>
                    Open: $From.Format("h a") | Close: $Till.Format("h a")
                <% end_loop %>
            </div>
        <% end_if %>
    <% end_if %>
<% end_with %>
