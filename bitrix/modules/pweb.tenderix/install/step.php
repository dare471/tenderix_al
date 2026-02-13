<?
IncludeModuleLangFile(__FILE__);

$your_company = trim($_REQUEST["your_company"]);

$error_req = false;

if (empty($your_company) && $GLOBALS["install_step"] == 2 && $GLOBALS["new_company"]) {
    echo CAdminMessage::ShowMessage(Array("TYPE" => "ERROR", "MESSAGE" => GetMessage("PW_TD_COMPANY_ERROR"), "DETAILS" => "", "HTML" => true,));
    $error_req = true;
}
?>

<?
if (($error_req && $GLOBALS["install_step"] == 2) || $GLOBALS["install_step"] == 1):
    ?>
    <form action="<? echo $APPLICATION->GetCurPage() ?>">
        <?= bitrix_sessid_post() ?>
        <input type="hidden" name="lang" value="<? echo LANG ?>">
        <input type="hidden" name="id" value="pweb.tenderix">
        <input type="hidden" name="install" value="Y">
        <input type="hidden" name="step" value="2">
        <script type="text/javascript">
            <!--
            function ChangeInstallPublic(val)
            {
                document.getElementById("template_id").disabled = !val;
                document.getElementById("site_lid").disabled = !val;
                document.getElementById("path_site").disabled = !val;
            }
            //-->
        </script>
        <table cellpadding="3" cellspacing="0" border="0" width="0%">
            <tr>
                <td><p><?= GetMessage("PW_TD_YOUR_COMPANY") ?> <span class="required">*</span></p></td>
                <td><input type="text" name="your_company" value="" id="your_company" size="30"></td>
            </tr>
        </table>
        <br /><br />
        <table cellpadding="3" cellspacing="0" border="0" width="0%">
            <tr>
                <td><input type="checkbox" name="install_public" value="Y" id="id_install_public" OnClick="ChangeInstallPublic(this.checked)"></td>
                <td><p><label for="id_install_public"><?= GetMessage("COPY_PUBLIC_FILES") ?></label></p></td>
            </tr>
        </table>
        <table cellpadding="3" cellspacing="0" border="0" width="0%">
            <tr>
                <td><p><?= GetMessage("TENDER_INSTALL_SITE_NAME") ?></p></td>
                <td>
                    <select id="site_lid" name="site_lid">
                        <?
                        $i = 0;
                        $sites = CSite::GetList($by, $order, Array("ACTIVE" => "Y"));
                        while ($site = $sites->Fetch()):
                            ?>
                            <option value="<?= $site["LID"] ?>"><? echo htmlspecialchars($site["NAME"]) ?></option>
                        <? endwhile ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><p><?= GetMessage("TENDER_INSTALL_FILE_NAME") ?></p></td>
                <td>
                    <input type="text" id="path_site" name="path_site" value="/" size="30" />
                </td>
            </tr>
            <tr>
                <td><p><?= GetMessage("TENDER_INSTALL_TEMPLATE_NAME") ?></p></td>
                <td><input type="text" name="template_id" id="template_id" value="tenderix" size="30"></td>
            </tr>
        </table>

        <script language="JavaScript">
            <!--
            ChangeInstallPublic(false);
            //-->
        </script>

        <br />
        <input type="submit" name="inst" value="<?= GetMessage("MOD_INSTALL") ?>">
    </form>
<? endif; ?>

<?
if (!$error_req && $GLOBALS["install_step"] == 2) :
    echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
    ?>
    <form action="<? echo $APPLICATION->GetCurPage() ?>">
        <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>" />
        <input type="submit" name="" value="<?= GetMessage("MOD_BACK") ?>" />
    </form>
<? endif; ?>