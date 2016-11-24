$(document).ready(function() {

	// Custom method to validate unique name
  $.validator.addMethod("checkModel", 
    function(value, element) {

      var result = false;
    	var id = $("#id").val();

      $.ajax({
        type:"POST",
        async: false,
        url: "/car/validateName",
        data: { id: id, name: value},
        success: function(data) {
  				// return true if model is not exist in database
          result = (data.status == true) ? true : false;
        }
      });

      return result; 
  	}
  );

	// Validate car Form
	$("#carForm").validate({
		rules: {
			name: {
				required: true,
				checkModel: true
			},
			max_speed: {
				required: true,
				number: true
			},
			brand: "required",
		},
		messages: {
			name: {
				required: "Model is required",
				checkModel: "Model must be unique"
			},
			brand: "Brand is required",
			max_speed: {
				required: "Max Speed is required",
				number: "Max Speed must be number"
			}
		},
		errorClass : "error text-danger",
	});

});