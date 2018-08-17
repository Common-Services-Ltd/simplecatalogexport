{**
 * @author    debuss-a <alexandre@common-services.com>
 * @copyright Copyright (c) 2018 Common-Services
 * @license   CC BY-SA 4.0
 *}

{if $post_process !== null}
    {if $post_process}
        <ps-alert-success>{l s='Configuration saved with success !' mod='simplecatalogexport'}</ps-alert-success>
    {else}
        <ps-alert-error>{l s='Oops, something went wrong !' mod='simplecatalogexport'}</ps-alert-error>
    {/if}
{/if}

<ps-panel icon="icon-tags" header="{l s='Documentation & Support' mod='simplecatalogexport'}">
    <div class="col-lg-9 col-sm-12 col-xs-12">
        <p>
            &raquo; {l s='You can get a PDF documentation to configure this module' mod='simplecatalogexport'} :
        </p>
        <ul>
            <li>
                <a href="{$module_dir|escape:'htmlall':'UTF-8'}documentation/readme_{$iso_lang}.pdf" target="_blank">DOCUMENTATION</a>
                {if $iso_lang == 'fr'}
                /
                <a href="{$module_dir|escape:'htmlall':'UTF-8'}documentation/readme_fr.html" target="_blank">README</a>
                {/if}
                /
                <a href="{$module_dir|escape:'htmlall':'UTF-8'}documentation/license.html" target="_blank">LICENSE</a>
            </li>
        </ul>
        <br>

        <p>
            &raquo; {l s='Bug report on GitHub only' mod='simplecatalogexport'} :
            <a href="https://github.com/Common-Services/simplecatalogexport/issues" target="_blank">https://github.com/Common-Services/simplecatalogexport/issues</a><br>
            &nbsp;&nbsp;&nbsp;{l s='For any bug report, please follow the following process' mod='simplecatalogexport'}
            : <a href="{$module_dir|escape:'htmlall':'UTF-8'}documentation/contributing.html" target="_blank">CONTRIBUTING</a>
        </p>
        <br>

        <p>
            &raquo; {l s='This is a free module powered by' mod='simplecatalogexport'}
            <a href="https://blog.common-services.com" target="_blank">Common-Services</a>
            {l s='under the licence' mod='simplecatalogexport'}
            <a href="https://creativecommons.org/licenses/by-sa/4.0/" target="_blank">CC BY-SA 4.0</a>.<br>
            &nbsp;&nbsp;&nbsp;{l s='You will appreciate our other modules' mod='simplecatalogexport'} :
            <a href="http://addons.prestashop.com/fr/58_common-services" target="_blank">http://addons.prestashop.com/fr/58_common-services</a>
        </p>
    </div>

    <div class="col-lg-3 visible-lg">
        <a href="http://addons.prestashop.com/fr/58_common-services" target="_blank">
            <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/common-services-banner.png" class="img-responsive pull-right " width="250px">
        </a>
        <div class="clearfix">&nbsp;</div>
        <br>
        <img src="{$module_dir|escape:'htmlall':'UTF-8'}logo.png" class="img-responsive pull-right">
    </div>

    <div class="clearfix"></div>
</ps-panel>

<form class="form-horizontal" action="{$smarty.server.REQUEST_URI}" method="POST">
    <div id="categories" class="panel">
        <div class="panel-heading">
            <i class="icon-folder-open"></i> {l s='Categories to export' mod='simplecatalogexport'}
        </div>

        {$categories_tree}

        <ps-panel-footer>
            <ps-panel-footer-submit title="{l s='Save' mod='simplecatalogexport'}" icon="process-icon-save" direction="right" name="submitsimplecatalogexport"></ps-panel-footer-submit>
            <ps-panel-footer-link title="{l s='Go to export page' mod='simplecatalogexport'}" icon="process-icon-export" href="{$catalog_export_link}" direction="left"></ps-panel-footer-link>
        </ps-panel-footer>
    </div>
</form>