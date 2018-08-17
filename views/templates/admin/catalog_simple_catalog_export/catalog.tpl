{**
 * @author    debuss-a <alexandre@common-services.com>
 * @copyright Copyright (c) 2018 Common-Services
 * @license   CC BY-SA 4.0
 *}

{if isset($export_success) && $export_success > 0}
    <ps-alert-success>
        {l s='Catalog exported, you can now download it !' mod='simplecatalogexport'}
    </ps-alert-success>
{elseif isset($export_success) && $export_success == -1}
    <ps-alert-error>
        {l s='The folder [%s%s] does not have permissions to write files. Fix permissions to export your catalog.'|sprintf:_PS_MODULE_DIR_:'simplecatalogexport/export' mod='simplecatalogexport'}
    </ps-alert-error>
{elseif isset($export_success) && $export_success == -2}
    <ps-alert-error>
        {l s='The file [%s%s] does not have write permissions. Fix permissions to export your catalog.'|sprintf:_PS_MODULE_DIR_:'simplecatalogexport/export/catalog.csv' mod='simplecatalogexport'}
    </ps-alert-error>
{/if}

{if isset($download_success) && $download_success === false}
    <ps-alert-error>{l s='Catalog file does not exist, export it first !' mod='simplecatalogexport'}</ps-alert-error>
{/if}

<ps-panel icon="icon-AdminCatalog" header="{l s='Catalog Export' mod='simplecatalogexport'}">

    <ps-alert-hint caret="false">
        <strong>{l s='Last export was on' mod='simplecatalogexport'}</strong> : {$last_export.date}<br>
        <strong>{l s='Line exported' mod='simplecatalogexport'}</strong> : {$last_export.nb_products}
    </ps-alert-hint>

    <form class="form-horizontal" action="{$smarty.server.REQUEST_URI}" method="POST">
        <ps-switch on-switch="lastExportSwitch" name="since_last_export" label="{l s='Only products created since last export' mod='simplecatalogexport'}" yes="{l s='Yes' mod='simplecatalogexport'}" no="{l s='No' mod='simplecatalogexport'}" active="false"></ps-switch>

        <ps-date-picker id="date_picker" name="since_date" label="{l s='Products created since' mod='simplecatalogexport'}" value="2010-01-01" fixed-width="lg"></ps-date-picker>

        <ps-panel-footer>
            <ps-panel-footer-submit title="2. {l s='Download catalog' mod='simplecatalogexport'}" icon="process-icon-download" direction="right" name="downloadcatalog"></ps-panel-footer-submit>
            <ps-panel-footer-submit title="1. {l s='Export catalog' mod='simplecatalogexport'}" icon="process-icon-export" direction="right" name="submitExportCatalog"></ps-panel-footer-submit>

            <ps-panel-footer-link title="{l s='Back to configuration' mod='simplecatalogexport'}" icon="process-icon-back" href="{$module_configuration_link}" direction="left"></ps-panel-footer-link>
        </ps-panel-footer>
    </form>

</ps-panel>

<script>
    function lastExportSwitch(status) {
        var date_picker = $('#date_picker');
        status && date_picker.fadeOut() || date_picker.fadeIn();
    }
</script>