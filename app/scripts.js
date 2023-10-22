document.addEventListener("DOMContentLoaded", function() {

    window.setAllCheckboxes = function(checked) {
        const checkboxes = document.form.elements['type[]'];
        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = checked;
        }
    };
});
