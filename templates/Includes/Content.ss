<% if not $ExcludeHeader %>
    <% include PageHeader %>
<% end_if %>

<% if $CurrentElement %>
    <%-- div class="element page-content-element">
      <div class="element_container">
        <div class="$DefaultContainer">
            <div class="typography">$Content</div>
        </div>
      </div>
    </div --%>

    <div class="current-element">
        $CurrentElement
    </div>
<% else %>
    <% if $ElementalArea.Elements.Count %>
        $ElementalArea
    <% else_if $Content %>
        <div class="element element_content_field dnadesign__elemental__models__elementcontent">
            <div class="element-container {$DefaultContainer}">
                <% include DNADesign\Elemental\Models\ElementContent HTML=$Content %>
            </div>
        </div>
    <% end_if %>
<% end_if %>

<% if $Form %>
<div class="page-form-element element">
    <div class="element_container">
        <div class="$DefaultContainer">
            $Form
        </div>
    </div>
</div>
<% end_if %>

<% if $ExtraCode %>
<div class="page-extra-code">
    <div class="element">
        <div class="element_container">
            <div class="$DefaultContainer">
                $ExtraCode
            </div>
        </div>
    </div>
</div>
<% end_if %>