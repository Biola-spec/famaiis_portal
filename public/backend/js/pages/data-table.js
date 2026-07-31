//[Data Table Javascript]

//Project:	Sunny Admin - Responsive Admin Template
//Primary use:   Used only for the Data Table

$(function () {
    "use strict";

    var baseOptions = {
      responsive: true,
      autoWidth: false,
      deferRender: true
    };

    if ($.fn.DataTable.isDataTable('#example1') === false && $('#example1').length) {
      $('#example1').DataTable(baseOptions);
    }

    if ($.fn.DataTable.isDataTable('#example2') === false && $('#example2').length) {
      $('#example2').DataTable($.extend({}, baseOptions, {
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true
      }));
    }
	
	
	if ($.fn.DataTable.isDataTable('#example') === false && $('#example').length) {
	  $('#example').DataTable($.extend({}, baseOptions, {
		dom: 'Bfrtip',
		buttons: [
			'copy', 'csv', 'excel', 'pdf', 'print'
		]
	  }));
	}
	
	if ($.fn.DataTable.isDataTable('#tickets') === false && $('#tickets').length) {
	  $('#tickets').DataTable($.extend({}, baseOptions, {
	  'paging'      : true,
	  'lengthChange': true,
	  'searching'   : true,
	  'ordering'    : true,
	  'info'        : true
	  }));
	}
	
	if ($.fn.DataTable.isDataTable('#productorder') === false && $('#productorder').length) {
	  $('#productorder').DataTable($.extend({}, baseOptions, {
	  'paging'      : true,
	  'lengthChange': true,
	  'searching'   : true,
	  'ordering'    : true,
	  'info'        : true
	  }));
	}
	

	if ($.fn.DataTable.isDataTable('#complex_header') === false && $('#complex_header').length) {
	  $('#complex_header').DataTable(baseOptions);
	}
	
	//--------Individual column searching
	
    // Setup - add a text input to each footer cell
    if ($('#example5').length && $.fn.DataTable.isDataTable('#example5') === false) {
      $('#example5 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
      } );
 
      // DataTable
      var table = $('#example5').DataTable(baseOptions);
 
      // Apply the search
      table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                .draw();
            }
        } );
      } );
    }
	
	
	//---------------Form inputs
	if ($('#example6').length && $.fn.DataTable.isDataTable('#example6') === false) {
	  $('#example6').DataTable(baseOptions);
	}
 
	
	
	
	
  }); // End of use strict
