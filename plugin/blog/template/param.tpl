<form method="post" action="index.php?p=blog&action=saveconf">
    {{ show.adminTokenField }}
    <p>
        <input {% IF runPlugin.getConfigVal[hideContent] %} checked {% ENDIF %} type="checkbox" name="hideContent" id="hideContent" /> 
        <label for="hideContent">Masquer le contenu des articles dans la liste</label>
    </p>
    <p>
        <input {% IF runPlugin.getConfigVal[comments] %} checked {% ENDIF %} type="checkbox" name="comments" id="comments" />
        <label for="comments" >Autoriser les commentaires</label>
    </p>
    <p>
        <label for="label" >Titre de page</label><br>
        <input type="text" name="label" id="label" value="{{ runPlugin.getConfigVal[label] }}" />
    </p>
    <p>
        <label for="itemsByPage" >Nombre d'entrées par page</label><br>
        <input type="number" name="itemsByPage" id="itemsByPage"  value="{{ runPlugin.getConfigVal[itemsByPage] }}" />
    </p>
    <p>
        <input type="checkbox" {% IF runPlugin.getConfigVal[displayCategories] %} checked {% ENDIF %} name="displayCategories" id="displayCategories" />
        <label for="displayCategories">Afficher les Catégories dans la sidebar</label>
    </p>
    
    {% HOOK.ACTION.adminDisplayParam[runPlugin.getName] %}

    <p><button type="submit" class="button">Enregistrer</button></p>
</form>