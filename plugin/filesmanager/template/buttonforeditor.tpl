<button type="button" id="filesmanagerMediaManagerButton{{ buttonId }}">
    <i class="bi bi-image"></i> {% Lang.filesmanager.buttonLabel %}</button>

<script>
    const button = document.getElementById('filesmanagerMediaManagerButton{{ buttonId }}');
    button.addEventListener('click', event => {
        Fancybox.show([{src: "{{ ajaxUrl }}", type: "ajax"}]);
    });
</script>

