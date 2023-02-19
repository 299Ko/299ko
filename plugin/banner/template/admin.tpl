<script>
    function onClickRadio() {
        if (document.getElementById("simpleContent").checked) {
            document.getElementById("useSimpleContent").style.display = 'block';
            document.getElementById("useSlider").style.display = 'none';
        } else {
            document.getElementById("useSimpleContent").style.display = 'none';
            document.getElementById("useSlider").style.display = 'block';
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        onClickRadio();
        document.querySelectorAll("input[type=radio]").forEach(function (item) {
            item.addEventListener("click", function () {
                onClickRadio();
            });
        });
    });
</script>
<article>
    <header>
        {% Lang.banner.title %}
    </header>
    <form method="post" action="{{ show.siteUrl }}/admin/index.php?p=banner&action=saveConf" enctype="multipart/form-data">
        {{ show.adminTokenField }}
        <fieldset>
            <legend>{% Lang.banner.bannerDisplay %}</legend>
            <label for="simpleContent">
                <input type="radio" id="simpleContent" name="typeContent" value="simple" checked>
                {% Lang.banner.simpleContent %}
            </label>
            <label for="sliderContent">
                <input type="radio" id="sliderContent" name="typeContent" value="slider">
                {% Lang.banner.sliderContent %}
            </label>
            <label for="homepage">
                <input type="checkbox" id="homepage" name="homepage" role="switch">
                {% Lang.banner.switchHomepage %}
            </label>
            <label for="bannerAddToCSS">{% Lang.banner.labelAddCSS %}</label>
            <textarea id="bannerAddToCSS" name="bannerAddToCSS" placeholder="height:auto;"></textarea>
        </fieldset>
        <hr>
        <legend><h4>{% Lang.banner.content %}</h4></legend>
        <div id="useSimpleContent">
            <label for="simpleContentTextarea">{% Lang.banner.simpleTextAreaDesc %}</label>
            <textarea id="simpleContentTextarea" name="simpleContentTextarea" class="editor">Contenu</textarea>
            {{ filesmanagerInsertUploadButtonForEditor(simpleContentTextarea) }}
        </div>
        <div id="useSlider">
            
        </div>
    </form>
</article>