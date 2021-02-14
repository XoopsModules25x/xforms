<div id="help-template" class="outer">
    <h1 class="head">Help:
        <a class="ui-corner-all tooltip" href="<{$xoops_url}>/modules/xforms/admin/index.php"
           title="Back to the administration of xForms"> xForms <img src="<{xoAdminIcons home.png}>"
                                                                     alt="Back to the Administration of xForms">
        </a></h1>
    <!-- -----Help Content ---------- -->
    <h2 class="odd">ELEMENT LIST</h2>
    <div class="even marg10 boxshadow1">
        Below is a list of the xForms elements available to be used in forms. Click on the link for each element
        to see a desription of the element.<br><br>
        <ul>
            <li><a href="#checkbox_element">Check box</a></li>
            <li><a href="#colorselect_element">Color select</a></li>
            <li><a href="#countryselect_element">Country select</a></li>
            <li><a href="#dateselect_element">Date select</a></li>
            <li><a href="#email_element">Email</a></li>
            <li><a href="#number_element">Number - Integer</a></li>
            <li><a href="#range_element">Numeric Range</a></li>
            <li><a href="#obfuscated_element">Obfuscated input</a></li>
            <li><a href="#plain_element">Plain text / HTML</a></li>
            <li><a href="#radio_element">Radio buttons</a></li>
            <li><a href="#radioyn_element">Radio buttons (Simple yes/no)</a></li>
            <li><a href="#select_element">Select box</a></li>
            <li><a href="#pattern_element">Text (Pattern)</a></li>
            <li><a href="#tarea_element">Text area</a></li>
            <li><a href="#tbox_element">Text box</a></li>
            <li><a href="#time_element">Time</a></li>
            <li><a href="#ufile_element">Upload (File)</a></li>
            <li><a href="#uimage_element">Upload (Image)</a></li>
            <li><a href="#url_element">Url</a></li>
        </ul>
    </div>

    <h3 class="odd" id="checkbox_element">Check Box</h3>
    <div class="even marg10 boxshadow1">
        <p>This form element adds a check box with options to the form. The site administrator can choose to
            pre-select (check) options if desired.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Options</td>
                <td>Checkbox indicates if item is checked by default.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="colorselect_element">Color Select</h3>
    <div class="even marg10 boxshadow1">
        <p>This form element adds a color select input element to the form. The site administrator can choose to
            set a default color if desired.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Size</td>
                <td>A number input box to set the size of the color input element on the form.</td>
            </tr>
            <tr>
                <td>Default value</td>
                <td>Color select to preselect the default value.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="countryselect_element">Country Select</h3>
    <div class="even marg10 boxshadow1">
        <p>This form element allows the user to select a country. The country list is populated using the built in XOOPS country list. The site administrator can choose to
            set a default color if desired.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Size</td>
                <td>Set the number of rows of the select box to show on the form.</td>
            </tr>
            <tr>
                <td>Allow multiple selections</td>
                <td>Select 'Yes' to allow users to select more than one (1) country in the dropdown box. Select 'No' to only allow selection a single country in the box.</td>
            </tr>
            <tr>
                <td>Default value</td>
                <td>Country to preselect as the default value. Note: Only one country can be preselected - even if 'allow multiple selections' is set to 'Yes'.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="dateselect_element">Date Select</h3>
    <div class="even marg10 boxshadow1">
        <p>This form element allows the user to enter a date. The administrator can set a starting, ending, and/or default date if desired.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Set Minimum (Start) date</td>
                <td>Selecting 'No' does not set a restraint on the oldest date that can be entered. Selecting 'Current Date' sets today's date, at the time the form was created. Select 'Other' to set a different minimum date.</td>
            </tr>
            <tr>
                <td>Set Maximum (End) date</td>
                <td>Selecting 'No' does not set a restraint on a future date that can be entered. Selecting 'Current Date' sets today's date, at the time the form was created. Select 'Other' to set a different maximum date.</td>
            </tr>
            <tr>
                <td>Default value</td>
                <td>Selecting 'Current Date' sets today's date, at the time the form was created. Select 'Other' to set a different default date.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="email_element">Email</h3>
    <div class="even marg10 boxshadow1">
        <p>The email form element allows the user to enter a valid email. This element uses the HTML5 element to validate the input value is an email address.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Size</td>
                <td>A number input box to set the size of the email input field on the form.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="number_element">Numeric Range</h3>
    <div class="even marg10 boxshadow1">
        <p>The number form element allows the user to enter an integer in the field between a minimum and maximum value. This element uses the HTML5 range element to validate the input value is an numeric integer value.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Range Minimum</td>
                <td>Set the minimum integer value the user is allowed to enter</td>
            </tr>
            <tr>
                <td>Range Maximum</td>
                <td>Set the maximum integer value the user is allowed to enter</td>
            </tr>
            <tr>
                <td>Step Size</td>
                <td>The step size is the increment allowed. For example a step size of 5 will allow increment/decrement from the default value.</td>
            </tr>
            <tr>
                <td>Default value</td>
                <td>Select 'Yes' to set a default value and then enter the starting (default) value. Selecting 'No' will automatically set zero (0) as the default.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="range_element">Range - Number</h3>
    <div class="even marg10 boxshadow1">
        <p>The number form element allows the user to enter an integer in the field. This element uses the HTML5 element to validate the input value is an numeric integer value.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Minimum value</td>
                <td>Set the minimum integer value the user is allowed to enter</td>
            </tr>
            <tr>
                <td>Maximum value</td>
                <td>Set the maximum integer value the user is allowed to enter</td>
            </tr>
            <tr>
                <td>Step size</td>
                <td>The step size is the increment allowed. For example a step size of 5 will allow increment/decrement from the default value.</td>
            </tr>
            <tr>
                <td>Default value</td>
                <td>Select 'Yes' to set a default value and then enter the starting (default) value. Selecting 'No' will automatically set zero (0) as the default.</td>
            </tr>
            <tr>
                <td>Size</td>
                <td>A number input box to set the size of the number's input field on the form.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="obfuscated_element">Obfuscated Input</h3>
    <div class="even marg10 boxshadow1">
        <p>The number form element allows the user to enter a value that is obfuscated so that it is not visible while typing. When sending the form after completion the value is not shown. The value is available in the administrator reports.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Minimum value</td>
                <td>Set the minimum integer value the user is allowed to enter</td>
            </tr>
            <tr>
                <td>Maximum value</td>
                <td>Set the maximum integer value the user is allowed to enter</td>
            </tr>
            <tr>
                <td>Step size</td>
                <td>The step size is the increment allowed. For example a step size of 5 will allow increment/decrement from the default value.</td>
            </tr>
            <tr>
                <td>Default value</td>
                <td>Select 'Yes' to set a default value and then enter the starting (default) value. Selecting 'No' will automatically set zero (0) as the default.</td>
            </tr>
            <tr>
                <td>Size</td>
                <td>A number input box to set the size of the number's input field on the form.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="plain_element">Plain Text</h3>
    <div class="even marg10 boxshadow1">
        <p>This form element allows the administrator to enter a 'label' or 'separator' field. The element does not allow for any user input. The field may be used to separate form sections, display instructions, etc.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Display Content</td>
                <td>Enter the content to be displayed on the form. The content may include HTML tags to display rich text.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="radio_element">Radio Buttons</h3>
    <div class="even marg10 boxshadow1">
        <p>The radio buttons form element will display a radio select element with options preset by the administrator. The user may only select one of the available options.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>This field is only used when displaying the field in form administration. This caption is useful to help the administrator identify the purpose of the field. The caption will not be displayed on the form shown to the users.</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Options</td>
                <td>Enter text into the text boxes to create options for the user. Additional options can be created by clicking the 'Add' button. To delete an option just clear (delete) the text for that option. The administrator can set the default value selected when the form loads by clicking
                    the radio button next to the desired option.
                </td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="radioyn_element">Radio Y/N Buttons</h3>
    <div class="even marg10 boxshadow1">
        <p>The radio Y/N buttons form element will create a simple, two (2) option radio select element.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>This field is only used when displaying the field in form administration. This caption is useful to help the administrator identify the purpose of the field. The caption will not be displayed on the form shown to the users.</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Default value</td>
                <td>Select the default value (Yes or No).</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="select_element">Select Box</h3>
    <div class="even marg10 boxshadow1">
        <p>The select box form element will display a HTML select element with options preset by the administrator. The administrator can either allow, or disallow, multiple selections by the user.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>This field is only used when displaying the field in form administration. This caption is useful to help the administrator identify the purpose of the field. The caption will not be displayed on the form shown to the users.</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Size</td>
                <td>Sets the number of option rows to be displayed on the form.</td>
            </tr>
            <tr>
                <td>Allow multiple selections</td>
                <td>Selecting Yes allows the user to select more than one option. Select No to only allow the user to select a single option.</td>
            </tr>
            <tr>
                <td>Options</td>
                <td>Enter text into the text boxes to create options for the user. Additional options can be created by clicking the 'Add' button. To delete an option just clear (delete) the text for that option. The administrator can set the default value(s) selected when the form loads by clicking
                    the select box next to the desired option. Only the first checked select box is used as the default
                    if multiple selection is not allowed. The select box allows for a user defined option. For an user defined option (Other), put {OTHER|*number*} in one of the text boxes. e.g. {OTHER|15} generates a text box 15 chars wide to allow the user to enter a value.
                </td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="pattern_element">Text (Pattern)</h3>
    <div class="even marg10 boxshadow1">
        <p>The text patterned input allows a free-form text input box where the administrator can require the input value match a specific pattern. For example, telephone number, a minimum number of characters, a date, etc. The pattern is defined using regular expression syntax. If this element is
            included on a form it is assumed user input is required as a response.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Size</td>
                <td>Set the size of the input field to be displayed on the form (in characters).</td>
            </tr>
            <tr>
                <td>Maximum length</td>
                <td>Set the maximum number of characters a user is allowed to enter.</td>
            </tr>
            <tr>
                <td>Pattern</td>
                <td>The regular expression pattern to validate the user input against. For example to require 4 numbers enter '\d{4}'</td>
            </tr>
            <tr>
                <td>Pattern Description</td>
                <td>Enter text to be shown if a user does not enter a correct value. For example: 'Please enter 4 integer digits'</td>
            </tr>
            <tr>
                <td>Placeholder</td>
                <td>Text directions to be shown in the input box as a hint. For example: 'Enter a number here...'.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="tarea_element">Text Area</h3>
    <div class="even marg10 boxshadow1">
        <p>The text area form element allows the user to enter a long text input string. The input is 'free form' and is useful for comments, detailed explanatory comments, etc.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Rows</td>
                <td>Set the input box number of rows to display.</td>
            </tr>
            <tr>
                <td>Columns</td>
                <td>Set the input box number of columns to display.</td>
            </tr>
            <tr>
                <td>Placeholder</td>
                <td>Text directions to be shown in the input box as a hint. For example: 'Enter your story here...'.</td>
            </tr>
            <tr>
                <td>Default value</td>
                <td>Enter default text to be included in the input box if desired. Note: The Placeholder text defined above will not be displayed if a default value is entered.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="tbox_element">Text Box</h3>
    <div class="even marg10 boxshadow1">
        <p>The text box form element allows the user to enter a one line 'free-form' input string. The maximum length captured is 255 characters.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. question)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to answer the question when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Size</td>
                <td>Set the input box width shown on the form (in number of characters).</td>
            </tr>
            <tr>
                <td>Maximum length</td>
                <td>Set the maximum number of characters a user is allowed to enter in the input box.</td>
            </tr>
            <tr>
                <td>Placeholder</td>
                <td>Text directions to be shown in the input box as a hint. For example: 'Enter your pet's name here...'.</td>
            </tr>
            <tr>
                <td>Default value</td>
                <td>Enter default text to be included in the input box if desired. Note: The Placeholder text defined above will not be displayed if a default value is entered.</td>
            </tr>
            <tr>
                <td>Contains email?</td>
                <td>Select Yes if this field contains an email address. The administrator may send a copy of the form to this email if selected in the configuration of the form.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="time_element">Time</h3>
    <div class="even marg10 boxshadow1">
        <p>The time form element allows the user to either type or use the HTML element select arrows to enter a time. The administrator can set the
            upper and lower limits for time input. A step size can also be set so the administrator can further limit an entry. This is useful for things like
            making appointments, or setting a meeting time start/stop time, etc.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. Meeting start time)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to enter a value when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Minimum value</td>
                <td>Set the minimum time a user is allowed to enter in the input box.</td>
            </tr>
            <tr>
                <td>Maximum value</td>
                <td>Set the maximum time a user is allowed to enter in the input box.</td>
            </tr>
            <tr>
                <td>Step size</td>
                <td>Enter the step size. This is the size the value is incremented/decremented each time a user presses the up/down arrow in the HTML element</td>
            </tr>
            <tr>
                <td>Default value</td>
                <td>Enter default time to be included in the input box if desired. Note: If 'No' is selected the starting value will be the current user time when the form is loaded</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="ufile_element">Upload (File)</h3>
    <div class="even marg10 boxshadow1">
        <p>The file upload element allows the user to upload a file to the site. It is highly recommended the administrator limit the type of files allowed to be
            uploaded to minimize the risk of abuse. The administrator can also limit the file size and MIME type to further control the file upload. Many of the common
            file extension and MIME types are pre-populated when creating the form. The uploaded file can either be saved to the server or emailed as an attachment
            with the email sent when the form is submitted. Note: The file is always saved to the server - even if it is emailed too.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. Meeting Notes, Invoice, etc.)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to enter a value when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Maximum file size</td>
                <td>Set the maximum size allowed for an uploaded file (in bytes). Setting this value to zero (0) allows an unlimited file size</td>
            </tr>
            <tr>
                <td>Allowed filename extensions</td>
                <td>Enter the allowed file extension for any uploaded file. The administrator may leave this field blank to allow all file extensions but this
                    is not recommended.
                </td>
            </tr>
            <tr>
                <td>Allowed MIME types</td>
                <td>Enter the allowed MIME type for any uploaded file. The administrator may leave this field blank to allow all MIME types but this
                    is not recommended.
                </td>
            </tr>
            <tr>
                <td>Save uploaded file to</td>
                <td>The uploaded file can either be saved to the server or emailed as an attachment with the email sent when the form is submitted.
                    Note: The file is <em>always</em> saved to the server - even if it is emailed. This is to ensure the file is not lost in the event the notification email either failed when sent or
                    not received.
                </td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="uimage_element">Upload (Image)</h3>
    <div class="even marg10 boxshadow1">
        <p>The image upload element allows the user to upload a specific type of file (image) to the site. See <a href="#ufile_element">Upload (File)</a> above for a detailed description.
            This element type is provided to ease administration by pre-populating the appropriate file extension and MIME types for image files.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. Favorite Landscape Picture, Your Selfie, Logo, etc.)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to enter a value when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Maximum file size</td>
                <td>Set the maximum size allowed for an uploaded file (in bytes). Setting this value to zero (0) allows an unlimited file size</td>
            </tr>
            <tr>
                <td>Allowed filename extensions</td>
                <td>Enter the allowed file extension for any uploaded file. The administrator may leave this field blank to allow all file extensions but this
                    is not recommended.
                </td>
            </tr>
            <tr>
                <td>Allowed MIME types</td>
                <td>Enter the allowed MIME type for any uploaded file. The administrator may leave this field blank to allow all MIME types but this
                    is not recommended.
                </td>
            </tr>
            <tr>
                <td>Save uploaded file to</td>
                <td>The uploaded file can either be saved to the server or emailed as an attachment with the email sent when the form is submitted.
                    Note: The file is <em>always</em> saved to the server - even if it is emailed. This is to ensure the file is not lost in the event the notification email either failed when sent or
                    not received.
                </td>
            </tr>
            <tr>
                <td>Maximum width</td>
                <td>Set the maximum width allowed for an uploaded image file (in bytes). Setting this value to zero (0) allows an unlimited image width</td>
            </tr>
            <tr>
                <td>Maximum height</td>
                <td>Set the maximum height allowed for an uploaded image file (in bytes). Setting this value to zero (0) allows an unlimited image height</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <h3 class="odd" id="url_element">Url</h3>
    <div class="even marg10 boxshadow1">
        <p>The Url form element allows the user to enter either an http[s] or ftp[s] url. If the administrator needs to restrict input further it is recommended the administrator use a
            <a href="#pattern_element">Text (Pattern)</a> element instead.</p>
        <table>
            <thead>
            <tr>
                <th>Setting</th>
                <th>Description</th>
            </thead>
            <tbody>
            <tr>
                <td>Caption</td>
                <td>Enter the text to be displayed for the caption (e.g. Meeting start time)</td>
            </tr>
            <tr>
                <td>Required</td>
                <td>Indicate whether the user is required to enter a value when filling out the form</td>
            </tr>
            <tr>
                <td>2 Rows</td>
                <td>Indicate whether to display the item on the same line as the caption or on the next line</td>
            </tr>
            <tr>
                <td>Display</td>
                <td>Indicate whether to show (display) or hide the item</td>
            </tr>
            <tr>
                <td>Order</td>
                <td>Indicate the weight (order) where the item is displayed</td>
            </tr>
            <tr>
                <td>Size</td>
                <td>A number input box to set the size of the Url input field displayed on the form.</td>
            </tr>
            <tr>
                <td>Maximum length</td>
                <td>Set the maximum number of characters a user is allowed to enter in the input box.</td>
            </tr>
            <tr>
                <td>Allowed URL types</td>
                <td>Select to allow either http[s], ftp[s], or both URL types.</td>
            </tr>
            <tr>
                <td>Placeholder</td>
                <td>Text directions to be shown in the input box as a hint. For example: 'http://www.yoursite.com'.</td>
            </tr>
            <tr>
                <td>Apply to form</td>
                <td>Indicate which form this unit applies too. <em>Note that this setting is only
                        displayed when adding a form using the 'Create/Edit form element' tab.</em></td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- -----Help Content ---------- -->
</div>
