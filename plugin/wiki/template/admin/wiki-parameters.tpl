<!-- templates/admin/wiki-parameters.tpl -->
<h1>Paramètres du Wiki</h1>
<div class="admin-menu">
    {{ adminMenu }}
</div>
<form method="post" action="{{ router.generate("admin-wiki-parameters-save") }}">
    <label>Items par page :</label>
    <input type="number" name="itemsByPage" value="{{ config.itemsByPage }}">
    <br>
    <label>Version Limit :</label>
    <input type="number" name="versionLimit" value="{{ config.versionLimit }}">
    <br>
    <button type="submit">Enregistrer</button>
</form>