// Wait for the DOM to be ready
$(function() {
    // Initialize form validation on the registration form.
    // It has the name attribute "registration"
    $("form[name='createCar']").validate({
        // Specify validation rules
        rules: {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            name: "required",
            max_speed: "required",
            brand_id: "required",
        },
        // Specify validation error messages
        messages: {
            name: "Veuillez renseigner le modèle de la voiture",
            max_speed: "Veuillez renseigner la vitesse maximale de la voiture",
            brand_id: "Veuillez choisir une marque",
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form) {
            form.submit();
        }
    });
});