$(document).ready(function() {

	// Ajax call to delete car
	$('#car_wrapper').on('click', '.delete-car', function() {

		var id = $(this).attr('rel');

		// Ajax call to delete content and update
	  $.ajax({
      type:"GET",
      //async: false,
      url: "/car/" + id + "/delete",
      success: function(data) {
        if (data) {
        	$( "#car_wrapper" ).html(data);
        	$('#cars_tbl_wrap').DataTable({
				  	"columns": [
							{"name": "name", "orderable": true, "searchable": true},
							{"name": "brand", "orderable": true, "searchable": true},
							{"name": "max_speed", "orderable": false, "searchable": false},
							{"name": "delete", "orderable": false, "searchable": false}
						],
				  });
        }
      }
    });
	});

	// Update car list page
  $('#cars_tbl_wrap').DataTable({
  	"columns": [
			{"name": "name", "orderable": true, "searchable": true},
			{"name": "brand", "orderable": true, "searchable": true},
			{"name": "max_speed", "orderable": false, "searchable": false},
			{"name": "delete", "orderable": false, "searchable": false}
		],
  });

});