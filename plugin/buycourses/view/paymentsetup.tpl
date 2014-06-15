<script type='text/javascript' src="../js/funciones.js"></script>

<link rel="stylesheet" type="text/css" href="../resources/plugin.css"/>

<div class="row">
    <div class="span12">
        <h3>{{ 'CurrencyType'|get_plugin_lang('Buy_CoursesPlugin') }}:</h3>
        <select id="currency_type">
            <option value="" selected="selected">{{ 'SelectACurrency'|get_plugin_lang('Buy_CoursesPlugin') }}</option>
            {% for currency in currencies %}
                {% if currency.status == 1 %}
                    <option value="{{ currency.id_country }}" selected="selected">{{ currency.country_name }} => {{ currency.currency_code }}
                    </option>
                {% else %}
                    <option value="{{ currency.id_country }}">{{ currency.country_name }} => {{ currency.currency_code }}</option>
                {% endif %}
            {% endfor %}
        </select>
        <input type="button" id="save_currency" class="btn btn-primary" value="{{ 'Save'|get_lang }}" />

        {% if paypal_enable == "true" %}
            <hr />
            <h3>Configuraci&oacute;n PayPal:</h3>
                {% if paypal.sandbox == "YES" %}
                    {{ 'Sandbox'|get_plugin_lang('Buy_CoursesPlugin') }}: <input type="checkbox" id="sandbox" value="YES" checked="checked"/>
                {% else %}
                    {{ 'Sandbox'|get_plugin_lang('Buy_CoursesPlugin') }}: <input type="checkbox" id="sandbox" value="YES" />
                {% endif %}
            <br />
            API_UserName: <input type="text" id="username" value="{{ paypal.username | e}}" /><br/>
            API_Password: <input type="text" id="password" value="{{ paypal.password | e }}"/><br/>
            API_Signature: <input type="text" id="signature" value="{{ paypal.signature | e }}"/><br/>
            <input type="button" id="save_paypal" class="btn btn-primary" value="{{ 'Save'|get_lang }}"/>
        {% endif %}

        {% if transference_enable == "true" %}
            <hr />
            <h3>Configuraci&oacute;n Transferencia: </h3>
            <table id="transference_table" class="data_table">
                <tr class="row_odd">
                <th>{{ 'Name'|get_lang }}</th>
                <th>{{ 'BankAccount'|get_plugin_lang('Buy_CoursesPlugin') }}</th>
                <th>{{ 'SWIFT'|get_lang }}</th>
                <th class="span1 ta-center">{{ 'Option'|get_lang }}</th>
                </tr>
                {% set i = 0 %}

                {% for transf in transference %}
                {{ i%2==0 ? '
                <tr class="row_even">' : '
                <tr class="row_odd">' }}
                    {% set i = i + 1 %}
                    <td>{{ transf.name | e }}</td>
                    <td>{{ transf.account | e }}</td>
                    <td>{{ transf.swift | e }}</td>
                    <td class="ta-center" id="account{{ transf.id }}">
                        <img src="{{ delete_img }}" class="cursor delete_account" alt="ok"/>
                        <input type="hidden" id="id_account{{ transf.id }}" name="id_account{{ transf.id }}" value="{{ transf.id }}" />
                    </td>
                </tr>
                {% endfor %}
                {{ i%2==0 ? '
                <tr class="row_even">' : '
                <tr class="row_odd">' }}
                    <td><input class="span4" type="text" id="tname"/></td>
                    <td><input type="text" id="taccount"/></td>
                    <td><input class="span2" type="text" id="tswift"</td>
                    <td class="ta-center">
                        <img class="cursor" id="add_account" src="{{ more_img }}" alt="add account"/>
                    </td>
                </tr>
            </table>
        {% endif %}
</div>
<div class="cleared"></div>
</div>