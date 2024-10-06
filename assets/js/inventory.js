var pathArray = window.location.pathname.split('/');
var baseURL = window.location.origin + "/";
var authImage = {};
var current = 0;
var products = [];

document.addEventListener('DOMContentLoaded', function() {
	// Setting datatable defaults
	$.extend( $.fn.dataTable.defaults, {
		responsive: true, 
		scrollX: true,
		//autoWidth: false,
		columnDefs: [{ 
			//orderable: false,
			width: '100px',
			//targets: [ 6 ]
		}],
		dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
		language: {
			search: '<span>Filter:</span> _INPUT_',
			searchPlaceholder: 'Type to filter...',
			lengthMenu: '<span>Show:</span> _MENU_',
			paginate: { 'first': 'First', 'last': 'Last', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
		},
		drawCallback: function () {
			$(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
		},
		preDrawCallback: function() {
			$(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
		}
	});
});

$(document).ready(function() {
	var mouseDown = false;
	var mouseClick = true;

	var reload = false;
	var openedRows = [];

	var org_id = getOrgID();

	var attachedImage = "";
	
	closeAddToInventoryModal();
	reloadNotifications();
	
	var table = $('#main-table').DataTable({
		dom: 'Bfrtip',
		orderCellsTop: true,
		scrollX: true,
		saveState: true, 
		responsive: true, 
		"deferRender": true,
		ajax: {
			method: 'POST',
			data: function(d) {
				d.o_id = getOrgID();
				d.d_id = getDeptID();
			},
			url: baseURL+'inventory/reloadTableGroup/',
			dataSrc: ""
		},
		'columnDefs': [
			{
				'targets': 0,
				className: 'ignore',
				'searchable': false,
				'orderable': false,
				'render': function (data, type, full, meta){
					 return '<input class="main-t" type="checkbox" value="' + $('<div/>').text(data).html() + '">';
				 }
			},
			{
				'targets': 2,
				'render': function(data) {
					if (data.includes('assets')) {
						return '<img src="' + baseURL + data + '" style="margin: auto; max-width: 256px; max-height: 125px;">';
					} else {
						return '<img src="' + data + '" style="margin: auto; max-width: 256px; max-height: 125px;">';
					}
				}
			},
			{
				'targets': 9,
				"createdCell": function (td, cellData, rowData, row, col) {
				  if ( cellData < 3 ) {
					$(td).css('color', 'red')
				  }
				}
			}
		],
		'order': [[1, 'asc']],
		columns: [
			{ "data": null },
			{ "data": "s_id" },
			{ "data": "image" },
			{ "data": "name" },
			{ "data": "description" },
			{ "data": "item_type" },
			{ "data": "type" },
			{ "data": "quality" },
			{ "data": "value" },
			{ "data": "qty" }
		],
		'fnCreatedRow': function (nRow, aData, iDataIndex) {
			$(nRow).attr('id', 'row-' + iDataIndex);
			var tr = $('#row-' + iDataIndex);
			var row = table.row( tr );
		},
		buttons: [
			{
				text: '<i class="fa fa-refresh" aria-hidden="true"></i>',
				className: "btn btn-secondary btn-sm",
				attr: {
					id: 'main-refresh',
				},
				action: function ( e, dt, node, config ) {
					var rowsRemaining = table.rows().nodes().length;
					
					reload = true;
					if (rowsRemaining == 0) {
						table.ajax.reload();
					} else {
						table.ajax.reload(null, false);
					}
				}
			},
			{
				text: 'New',
				className: "btn btn-secondary btn-sm",
				attr: {
					id: 'main-new',
				},
				action: function ( e, dt, node, config ) {
					showAddToInventoryModal();
				}
			},
			{
				text: 'Edit',
				className: "btn btn-secondary btn-sm",
				attr: {
					id: 'main-edit',
				},
				action: function ( e, dt, node, config ) {
					$("input:checked", this.rows().nodes()).each(function(){
						var tr = $(this).closest('tr');
						var row = table.row( tr );
						var data = row.data();
					});
					
					showExistingModal();
					prePopulateForm(table.row($('#main-table input:checked').closest('tr')).data());
				}
			},
			{
				text: 'Delete',
				className: "btn btn-secondary btn-sm",
				attr: {
					id: 'main-delete',
				},
				action: function ( e, dt, node, config ) {
					$("input:checked", this.rows().nodes()).each(function(){
						var tr = $(this).closest('tr');
						var row = table.row( tr );
						var data = row.data();
						
						showSweetConfirm("Delete stock of '" + data.name + "'?", "Attention", $icon='info', function(confirmed) {
							if(!confirmed){
								e.preventDefault();
							}
							else {
								deleteStock(data);
							}
						});
					});
					
				}
			}
		]
	});

	toggleButton();

	table.buttons().container().appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );
	
	//parent table - select all
	$('#main-select-all').on('click', function(){
		$('#main-table input[class="main-t"]').prop('checked', this.checked);
		toggleButton();
	});

	$('#st-select-all').on('click', function(){
		var id = $(this).closest('table').attr(id);
		var id = $(this).closest('table').attr(id);

		$('#' + id + ' input[class="main-st"]').prop('checked', this.checked);
		toggleSubTableButton(s_id);
	});

	$('#addToInventoryCreateButton').click(function() {
		submitNewStock();
	});

	$('#productCreateButton').click(function() {
		submitNewProduct();
	});

	$('#cancelExistingUpdateButton').click(function() {
		updateStock();
	});
	
	$('#addToInventoryCancelButton').click(function() {
		closeAddToInventoryModal();
	});

	$('#addToInventoryBackButton').click(function() {
		showStockModal();
	});

	$('#addToInventoryNextButton').click(function() {
		showProductModal();
	});

	$('#cancelExistingButton').click(function() {
		closeExistingModal();
	});

	$('#editExistingProductCancelButton').click(function() {
		closeEditExistingProduct();
	});

	$('#productCreateCancelButton').click(function() {
		closeAddNewProduct();
	});

	$('#editExistingProductUpdateButton').click(function() {
		updateProduct();
	});
	
	$('#main-table').on('click', 'input[type="checkbox"]', function() {
		if ($('#EditExistingStock').is(":visible") || $("#main-table input:checkbox:checked").length <= 1) {
			closeExistingModal();
		}

		$(this).prop('checked', this.checked);
		toggleButton();
	}); 

	$(document).on('click', 'input[id*="st-select-all"]', function(){
		var table = $(this).closest('table');
		var id = table.find().attr('id');
		var s_id = table.attr('data-s_id');

		$("#sub-table-" + s_id + ' input[class="main-st"]').prop('checked', this.checked);
		toggleSubTableButton(s_id);
	});
	
	$(document).on('click', 'input[class*="main-st"]', function(){
		var table = $(this).closest('table');
		var s_id = table.attr('data-s_id');
		
		$(table.attr('id') + ' input[class="main-st"]').prop('checked', this.checked);
		toggleSubTableButton(s_id);
	});

	//show products of each stock
	$('#main-table tbody').on('dblclick', 'tr td', function () {
		var tr = $(this).closest('tr');
		var td = $(this).closest('td')
		var className = $(td).attr('class');

		var idx = $.inArray( tr.attr('id'), openedRows );
		
		if (className == null || !className.includes('ignore')) {
			var row = table.row( tr );
			
			if (row.data() == null) {
				return;
			}
			
			var s_id = row.data().s_id;
	 
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
				openedRows.splice( idx, 1 );
			}
			else {
				// Open this row
				row.child(addSubTable(row.data().s_id)).show();
				configureSubTable(row.data().s_id);
				toggleSubTableButton(row.data().s_id);
				
				tr.addClass('shown');

				if ( idx === -1 ) {
					openedRows.push( tr.attr('id') );
				}
			}
		}
	} );

	//change organization
	$("#org").on('change', function() {
		var open = $(this).data("isopen");
		
		reloadDepartments();
		
		$(this).data("isopen", !open);
	});
	
	//change department
	$("#dept").on('change', function() {
		var open = $(this).data("isopen");
		
		reload = true;
		table.ajax.reload();
		
		$(this).data("isopen", !open);
	});
	
	//redraw table
	table.on( 'draw', function () {
		if (reload) {
			reload = false;
			var tempOpenedRows = openedRows;
			openedRows = [];

			$.each( tempOpenedRows, function ( i, id ) {
				var tr = $('#'+id).closest('tr');
				var row = table.row( tr );
				var idx = $.inArray( tr.attr('id'), openedRows );

				row.child(addSubTable(row.data().s_id)).show();
				configureSubTable(row.data().s_id);

				if ( idx === -1 ) {
					openedRows.push( tr.attr('id') );
				}
			} );
			
			reloadNotifications();
			closeAllModals();
			toggleButton();
		}
	} );

	function reloadDepartments() {
		var o = getOrgID();
		
		$.ajax({
			method: 'POST',
			data: ({ o_id: o}),
			url: baseURL+'inventory/reloadDepartments/',
			dataSrc: "",
			success: function(d) {
				loadDepartments(JSON.parse(d));	
				reload = true;
				table.ajax.reload();
			}
		});
	}

	function loadDepartments(d) {
		$('#dept').empty();

		$.each(d, function (index, department) {
			var newOption = $('<option value=' + department.d_id +'> ' + department.dept_name + ' </option>');
						
			$('#dept').append(newOption);
		});
		
		$('#dept').trigger("chosen:updated");
	}
	
	function getOrgID() {
		return $('#org').val();
	}
	
	function getDeptID() {
		var id = $('#dept').val();
		
		if (id == null) {
			return 1;
		} else {
			return id;
		}
	}
	
	function addSubTable(s) {
		var tableName = 'sub-table-'+ s ;
		
		return '<table id="' + tableName + '" data-s_id='+s+ ' class="table table-striped table-bordered" style="width:100%;">'+
					'<thead>'+
						'<tr>'+
							'<th id = "select"><input type="checkbox" name="select_all_st" value="1" id="st-select-all"></th>'+
							'<th id="p_id">Product ID</th>'+
							'<th id="avail">Availability</th>'+
							'<th id="game_id">Game ID</th>'+
							'<th id="serial">Serial Number</th>'+
							'<th id="serial">Authenticity</th>'+
						'</tr>' +
					'</thead>' +
					'<tbody>'+
						'<tr class="inventoryRow">'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
						'</tr>' +
					'</tbody>' +
				'</table>';
	}

	function configureSubTable(s) {
		var tableName = '#sub-table-'+ s ;

		var table = $(tableName).DataTable({
			dom: 'Bfrtip',
			orderCellsTop: true,
			scrollX: true,
			saveState: true, 
			responsive: true, 
			"deferRender": true,
			ajax: {
				method: 'POST',
				data: ({s_id: s}),
				url: baseURL+'inventory/reloadTable/',
				dataSrc: ""
			},
			columnDefs: [
				{
					'targets': 0,
					className: 'ignore',
					'searchable': false,
					'orderable': false,
					'render': function (data, type, full, meta){
						 return '<input class="main-st" type="checkbox" value="' + $('<div/>').text(data).html() + '">';
					 }
				},
				{
					'targets': 2,
					"createdCell": function (td, cellData, rowData, row, col) {
					  if ( cellData == 'available' ) {
						$(td).css('color', 'green')
					  } else if (cellData == 'listed') {
						$(td).css('color', 'red')
					  } else {
						$(td).css('color', 'blue')
					  }
					}
				}
			],
			columns: [
				{ "data": null},
				{ "data": "p_id"},
				{ "data": "status"},
				{ "data": "game_id"},
				{ "data": "serial_number"},
				{ "data": "authenticity"}
			],
			buttons: [
			{
				text: '<i class="fa fa-refresh" aria-hidden="true"></i>',
				className: "btn btn-secondary btn-sm",
				attr: {
					id: 'child-refresh-' + s,
				},
				action: function ( e, dt, node, config ) {
					var rowsRemaining = table.rows().nodes().length; 

					reloadNotifications();
					closeAllModals();
					toggleButton();
					
					if (rowsRemaining == 0) {
						table.ajax.reload();
					} else {
						table.ajax.reload(null, false);
					}
				}
			},
			{
				text: 'New',
				className: "btn btn-secondary btn-sm",
				attr: {
					id: 'child-new-' + s,
				},
				action: function ( e, dt, node, config ) {
					showAddNewProduct(s);
				}
			},
			{
				text: 'Edit',
				className: "btn btn-secondary btn-sm",
				attr: {
					id: 'child-edit-' + s,
				},
				action: function ( e, dt, node, config ) {
					var length = this.rows().nodes().length;
					var multi = [];

					if ( $("input:checked", this.rows().nodes()).length > 0) {
						$("input:checked", this.rows().nodes()).each(function(){
							var tr = $(this).closest('tr');
							var row = table.row( tr );
							var data = row.data();
							
							multi.push({data});
						});
					}
					
					showEditExistingProduct();
					prePopulateExistingProduct(multi);
				}
			},
						{
				text: 'Assign',
				className: "btn btn-secondary btn-sm",
				attr: {
					id: 'child-assign-' + s,
				},
				action: function ( e, dt, node, config ) {
					assignProduct();
				}
			},
			{
				text: 'Delete',
				className: "btn btn-secondary btn-sm",
				attr: {
					id: 'child-delete-' + s,
				},
				action: function ( e, dt, node, config ) {
					var length = this.rows().nodes().length;
					var count = 0;
					var multi = [];

					if ( $("input:checked", this.rows().nodes()).length > 0) {
						$("input:checked", this.rows().nodes()).each(function(){
							var tr = $(this).closest('tr');
							var row = table.row( tr );
							var data = row.data();
							
							multi.push({data});

							count++;
						});

						if (count > 1) {
							var ids = '';
							$.each(multi, function (key, value) {
								if (key+1 != multi.length) {
									ids += value['data'].name + ":" + value['data'].p_id + ', ';
								} else {
									ids += ' and ' + value['data'].name + ":" + value['data'].p_id;
								}
							});

							showSweetConfirm("Delete products with ids of " + ids + "'?", "Attention", $icon='info', function(confirmed) {
								if(!confirmed){
									e.preventDefault();
								}
								else {
									deleteProduct(multi);
								}
							});
						} else {
							var data = multi[0]['data'];
							showSweetConfirm("Delete '" + data.name + "' with id of " + data.p_id + "'?", "Attention", $icon='info', function(confirmed) {
								if(!confirmed){
									e.preventDefault();
								}
								else {
									deleteProduct(data);
								}
							});
						}
					}
				}
			}
		]
		});
	}

	function toggleButton(){
	   //console.log('toggleButton');
	   if ($("input:checked", table.rows().nodes()).length == 1) {
		   $('#main-delete').prop('disabled', false);
		   $('#main-delete').show();
		   $('#main-edit').prop('disabled', false);
		   $('#main-edit').show();
	   } else if ($("input:checked", table.rows().nodes()).length > 1) {
			$('#main-delete').show();
			$('#main-delete').prop('disabled', false);
			$('#main-edit').hide();
			$('#main-edit').prop('disabled', true);
		  
	   } else {
		   $('#main-delete').prop('disabled', true);
		   $('#main-delete').hide();
		   $('#main-edit').prop('disabled', true);
		   $('#main-edit').hide();
	   }
   }
   
    function disableButtons(b) {
	   //console.log('disableButtons');
	   if (b == true) {
			$('#main-delete').prop('disabled', true);
			$('#main-edit').prop('disabled', true);
			$('#main-new').prop('disabled', true);
			$('#main-refresh').prop('disabled', true);
	   } else {
			$('#main-new').prop('disabled', false);
			$('#main-refresh').prop('disabled', false);
			toggleButton();
	   }
   }
	
	function toggleSubTableButton(s_id){
		//console.log('toggleSubTableButton');
	   if ($("input:checked", $('#sub-table-' + s_id).DataTable().rows().nodes()).length == 1) {
		   $('#child-delete-' + s_id).show();
		   $('#child-delete-' + s_id).prop('disabled', false);
		   $('#child-edit-' + s_id).show();
		   $('#child-edit-' + s_id).prop('disabled', false);
		   $('#child-assign-' + s_id).show();
		   $('#child-assign-' + s_id).prop('disabled', false);
	   } else if ($("input:checked", $('#sub-table-' + s_id).DataTable().rows().nodes()).length > 1) {
		   $('#child-delete-' + s_id).show();
		   $('#child-delete-' + s_id).prop('disabled', false);
		   $('#child-edit-' + s_id).show();
		   $('#child-edit-' + s_id).prop('disabled', false);
		   $('#child-assign-' + s_id).hide();
		   $('#child-assign-' + s_id).prop('disabled', true);
	   } else {
		   $('#child-delete-' + s_id).prop('disabled', true);
		   $('#child-delete-' + s_id).hide();
		   $('#child-edit-' + s_id).prop('disabled', true);
		   $('#child-edit-' + s_id).hide();
		   $('#child-assign-' + s_id).prop('disabled', true);
		   $('#child-assign-' + s_id).hide();
	   }
	}
	
	function disableSubTableButtons(b) {
		//console.log('disableSubTableButtons');
		$.each( openedRows, function ( i, id ) {
			var tr = $('#'+id).closest('tr');
			var row = table.row( tr );
			var s_id = row.data().s_id;
			
			if (b == true) {
				$('#child-new-' + s_id).prop('disabled', true);
				$('#child-delete-' + s_id).prop('disabled', true);
				$('#child-edit-' + s_id).prop('disabled', true);
				$('#child-refresh-' + s_id).prop('disabled', true);
			} else {
				$('#child-refresh-' + s_id).prop('disabled', false);
				$('#child-new-' + s_id).prop('disabled', false);
				toggleSubTableButton(s_id);
			}
		} );
   }
	
	function reloadNotifications() {
		hideLoadingAnim(false);
		
		var o = getOrgID();
		var d = getDeptID();
		
		$.ajax({
			method: 'POST',
			data: ({ o_id: o, d_id: d}),
			url: baseURL+'inventory/getNotifications/',
			dataSrc: "",
			success: function(d) {
				loadNotifications(JSON.parse(d));	
				hideLoadingAnim(true);
			}
		});
	}

	function loadNotifications(d) {
		d = d.reverse();

		var newNotifitcations = d.length;
		var currentNotifications = $("#messages-tue > li").length;
		
		var newIds = [];
		var oldIds = [];
		
		d.forEach(element => newIds.push(parseInt(element.id)));
		
		$('#messages-tue').children('li').each(function () {
			oldIds.push($(this).data('id'));
		});

		var sameElementsNew = newIds.every(id => oldIds.includes(id));
		var sameElementsOld = oldIds.every(id => newIds.includes(id));
		
		if (newNotifitcations > currentNotifications || sameElementsNew === false) {
			if (sameElementsOld === false) {
				var diff = diffArray(oldIds,newIds);
				diff.forEach(element => $('.noti[data-id="'+ element +'"]').remove());
			}

			$.each(d, function (index, notification) {
				var image;
				
				if (oldIds.length != 0) {
					if (oldIds.includes(parseInt(notification.id))) {
						return true;
					}
				}
				
				if (!notification.image) {
					image = 'https://dg7ltaqbp10ai.cloudfront.net/fit-in/244x151/imageuploadplaceholder2.jpg';;
				}
				
				var tag = '';
				if (parseInt(notification.noti_type) == 0) {
					tag = 'green_tag';
				} else if (parseInt(notification.noti_type) == 1) {
					tag = 'teal_tag';
				} else {
					tag = 'red_tag';
				}

				if (parseInt(notification.noti_class) == 0) {
					tag += ' stock';
				} else {
					tag += ' product';
				}
				
				if (notification.image.includes('asset')) {
					var toAppend = '<li class="media noti" data-id="' + notification.id + '">' +
										'<div class="row noti_row">' +
											'<div class="mr-2 col noti_image_contain">' +
												'<img class="rounded-circle noti_image align-middle" src="' + baseURL + notification.image + '" max-width="52px" max-height="52px" alt="">' +
											'</div>' +
											'<div class="mr-2 col noti_body">' +
												'<div class="media-body">' +
														'<span class="font-size-sm text-muted noti_notes">' +
															notification.notes + '</span>' +
												'</div>' +
											'</div>' +
										'</div>' +
										'<div class="noti_tag ' + tag + '">' + 
										'</div>' +
							'</li>';
				} else {
					var toAppend = '<li class="media noti" data-id="' + notification.id + '">' +
										'<div class="row noti_row">' +
											'<div class="mr-2 col noti_image_contain">' +
												'<img class="rounded-circle noti_image align-middle" src="' + notification.image + '" max-width="52px" max-height="52px" alt="">' +
											'</div>' +
											'<div class="mr-2 col noti_body">' +
												'<div class="media-body">' +
														'<span class="font-size-sm text-muted noti_notes">' +
															notification.notes + '</span>' +
												'</div>' +
											'</div>' +
										'</div>' +
										'<div class="noti_tag ' + tag + '">' + 
										'</div>' +
							'</li>';
				}

				
							
				$('#messages-tue').prepend(toAppend);
			});
		} else {
			if (sameElementsOld === false) {
				var diff = diffArray(oldIds,newIds);
				diff.forEach(element => $('.noti[data-id="'+ element +'"]').remove());
			}
		}
	}

	function submitNewStock() {
		disableSubTableButtons(true);
		disableButtons(true);
		
		var o = $('#org').val();
		var d = $('#dept').val();
		
		var t = $('#stockname').val();
		var desc = $('textarea[name*="stockdescription"]').val();
		var img = $('#edit_stock_main_images').attr('set-hidden-value'); //$('#prize_main_images').val();
		var pt = $('input[name*=prize_type]').val();
		
		var ty = $('#itemtype option:selected').text();;
		
		var qual = $('#newquality').val();
		var v = $('#newvalue').val();
		var quan = $('#newproductquantity').val();

		var formValid = true;
		var fields = [t, desc, img, pt, ty, qual, v, quan]
		for (var i = 0; i < fields.length; i++) {
			if (fields[i] == null || fields[i] == "") {
				formValid = false;
				console.log(i);
			}
		}

		if (formValid) {
			var p = [];
			for (var i = 0; i < current; i++) {
				var s = $('#serial'+ i).val();
				var a = $('#auth'+ i).val();
				p.push({serial_number:s, authenticity: a});
			}
			
			var d = { stock : {o_id: o, d_id: d, title: t, description: desc, image: attachedImage, prize_type: pt, type: ty, quality: qual, value: v, quantity: quan}, products: p};
			
			$.ajax({
				method: 'POST',
				data: d,
				url: baseURL+'inventory/addNewStock/'
			}).done(function (d) {
				$('#main-table').DataTable().ajax.reload(null, false);
				reloadNotifications();
			});
			
			document.getElementById("createStock").reset();
			document.getElementById("createProduct").reset();
			removeAllAddedProducts(current, quan);
			current = 0;

			closeAddToInventoryModal();
		} else {
			showSweetAlert("Fields are empty.","Whoops!", "error");
		}
		
		disableSubTableButtons(false);
		disableButtons(false);
	}

	function submitNewProduct() {
		disableButtons(true);
		disableSubTableButtons(true);
		
		var o = $('#org').val();
		var d = $('#dept').val();
		var s = $('#AddNewProduct').data('s_id');

		var quan = $('#newproductquantity').val();
		
		var p = [];
		for (var i = 0; i < current; i++) {
			var ser = $('#serial'+ i).val();
			var a = $('#auth'+ i).attr('set-hidden-value');
			p.push({serial_number:ser, authenticity: a});
		}
		
		var d = {info: {o_id: o, d_id: d, s_id: s}, products: p};
		
		$.ajax({
			method: 'POST',
			data: d,
			url: baseURL+'inventory/addNewProducts/'
		}).done(function (d) {
			reload = true;
			table.ajax.reload(null, false);
			reloadNotifications();
			
			disableSubTableButtons(false);
			disableButtons(false);
		});
		
		document.getElementById("createNewProduct").reset();
		removeAllExistingProducts(current, 0);

		closeAddNewProduct();
	}
	
	function updateStock() {
		disableSubTableButtons(true);
		disableButtons(true);
		
		var o = $('#org').val();
		var d = $('#dept').val();
		var s = $('#editStockTitle').attr('data-test');
		
		var t = $('#edit_stockname').val();
		var desc = $('textarea[name*="edit_stockdescription"]').val();
		var img = $('#edit_stock_main_images').attr('set-hidden-value'); //$('#edit_stock_main_images').val();
		var pt = $('input[name*=edit_prize_type]').val();
		
		var ty = $('#edit_itemtype option:selected').text();
		
		var qual = $('#edit_quality :selected').val();
		var v = $('#edit_value').val();
		
		var d = { o_id: o, d_id: d, s_id: s, title: t, description: desc, image: img, prize_type: pt, type: ty, quality: qual, value: v};

		$.ajax({
			method: 'POST',
			data: d,
			url: baseURL+'inventory/updateStock/'
		}).done(function (d) {
			$('#main-table').DataTable().ajax.reload(null, false);
			reloadNotifications();
		});
		
		document.getElementById("editStock").reset();

		closeExistingModal();
		disableSubTableButtons(false);
		disableButtons(false);
	}

	function updateProduct() {
		disableSubTableButtons(true);
		disableButtons(true);
		
		var count = 0;
		
		var data = [];
		$('div [id*="existingProduct"]').each(function() {
			var s = $(this).children().closest('div.form-group').data('s_id');
			var p = $(this).children().closest('div.form-group').data('p_id');

			var serial = $('input[id*="serial' + count + '"]').val();
			var a = $('#auth'+ count).attr('set-hidden-value');
			if (a == undefined)
			{
				a = null;
			}

			var row = {'s_id': s, 'p_id': p, 'serial': serial, 'auth': a};
			data.push(row);
			
			count++;
		});

		var d = { 'length': $('div [id*="existingProduct"]').length, 'o_id': getOrgID(), 'd_id': getDeptID(), 'products': data};
		
		var rowsRemaining = table.rows().nodes().length;

		$.ajax({
			method: 'POST',
			data: d,
			url: baseURL+'inventory/updateProducts/',
			success: function(d) {
				reload = true;
				
				console.log(rowsRemaining);
				if (rowsRemaining == 0) {
					table.ajax.reload();
				} else {
					table.ajax.reload(null, false);
				}
			}
		});
		
		document.getElementById("editStock").reset();
		removeAllExistingProducts(current, 0);
		
		closeEditExistingProduct();
		disableSubTableButtons(false);
		disableButtons(false);
	}

	function deleteStock(data) {
		disableSubTableButtons(true);
		disableButtons(true);
		
		var rowsRemaining = table.rows().nodes().length;
		
		$.ajax({
			method: 'POST',
			data: ({ 'o_id': data.o_id, 'd_id': data.d_id, 's_id': data.s_id, 'name': data.name, 'image': data.image}),
			url: baseURL+'inventory/removeStock/',
			success: function(d) {
				reload = true;

				if (rowsRemaining == 0) {
					table.ajax.reload();
				} else {
					table.ajax.reload(null, false);
				}
			}
		});
		
		disableSubTableButtons(false);
		disableButtons(false);
	}

	function deleteProduct(data) {
		disableSubTableButtons(true);
		disableButtons(true);
		
		var d = { 'length': 0, 'o_id': getOrgID(), 'd_id': getDeptID(), 's_id': data.s_id, 'p_id': data.p_id, 'name': data.name};

		if (data.length > 1) {
			d = { 'length': data.length, 'o_id': getOrgID(), 'd_id': getDeptID(), 'data':data};
		} 
		
		var rowsRemaining = table.rows().nodes().length;
		
		$.ajax({
			method: 'POST',
			data: d,
			url: baseURL+'inventory/removeProduct/',
			success: function(d) {
				reload = true;
				
				console.log(rowsRemaining);
				if (rowsRemaining == 0) {
					table.ajax.reload();
				} else {
					table.ajax.reload(null, false);
				}
			}
		});
		
		disableSubTableButtons(false);
		disableButtons(false);
	}
	
	function formatDate(data) {
		date = new Date(data);
		y = date.getFullYear().toString().substr(-2);
		m = (date.getMonth() + 1).toString();
		d = date.getDate().toString();
		
		dateStr = m + "/" + d + "/" + y
		return dateStr;
	}
	
	function hideLoadingAnim(b) {
		if (b) {
			$('#loadingNoti').hide();
		} else {
			$('#loadingNoti').show();
		}
	}
	
	function diffArray(arr1, arr2) {
		return arr1.concat(arr2).filter(function (val) {
			if (!(arr1.includes(val) && arr2.includes(val)))
				return val;
		});
	}

	$('#prize_main_images').change(function(){
		var fileTypes= this.files[0].type;
		if($.inArray(fileTypes,['image/jpeg','image/png','image/PNG','image/jpg','image/gif'])==-1)
		{
			showSweetAlert("Not a valid image, only JPEG , PNG, or GIF allowed","Whoops!", "error");
		}
		else
		{
			var image= this.files[0];
			
			data = new FormData();
			data.append('file', this.files[0]);
			
			$.ajax({
			  url: baseURL + "ajax/uploadImage",
			  type: "POST",
			  data: data,
			  enctype: 'multipart/form-data',
			  processData: false,  // tell jQuery not to process the data
			  contentType: false   // tell jQuery not to set contentType
			}).done(function(data) {
				$(this).attr('set-hidden-value', data);
				$("#imagePreview").attr('src', data);

			   if(data != 'error' ) {
					$(this).val(data);
			   } else {
					if ($.inArray(fileTypes,['image/jpeg','image/png', 'image/PNG', 'image/jpg','image/gif'])==-1) {
						showSweetAlert("Not a valid image, only JPEG , PNG, or GIF allowed","Whoops!", "error");
					}
			   }
			});
		}
	});
	
	$(document).on('change', 'input[id*="auth"]', function(){
		console.log(111);
		var ele = $(this);
		var fileTypes= this.files[0].type;
		
		if($.inArray(fileTypes,['image/jpeg','image/png','image/PNG','image/jpg','image/gif'])==-1)
		{
			showSweetAlert("Not a valid image, only JPEG , PNG, or GIF allowed","Whoops!", "error");
		}
		else
		{
			var image= this.files[0];
			
			data = new FormData();
			data.append('file', this.files[0]);
			
			$.ajax({
			  url: baseURL + "ajax/uploadImage",
			  type: "POST",
			  data: data,
			  enctype: 'multipart/form-data',
			  processData: false,  // tell jQuery not to process the data
			  contentType: false   // tell jQuery not to set contentType
			}).done(function(data) {
				
				var id = $(this).find('input [id*="auth"]').attr('id');
				console.log(id);
				console.log();
				console.log($(ele).attr('id'));
				$(ele).attr('set-hidden-value', data);
				$(ele).parent().find('img').attr('src', data);

			   if(data != 'error' ) {
					$(this).val(data);
			   } else {
					if ($.inArray(fileTypes,['image/jpeg','image/png', 'image/PNG', 'image/jpg','image/gif'])==-1) {
						showSweetAlert("Not a valid image, only JPEG , PNG, or GIF allowed","Whoops!", "error");
					}
			   }
			});
		}
	});
		
	$("#appendExistingProduct").on('change', 'input[id*="auth"]', function(){
		$('#editExistingProductUpdateButton').prop('disabled', true);
		
		setHiddenValue = $(this).attr('set-hidden-value');
		
		var fileTypes= this.files[0].type;
		
		if($.inArray(fileTypes,['image/jpeg','image/png','image/PNG','image/jpg','image/gif'])==-1)
		{
			showSweetAlert("Not a valid image, only JPEG , PNG, or GIF allowed","Whoops!", "error");
		}
		else
		{
			var image= this.files[0];
			
			data = new FormData();
			data.append('file', this.files[0]);
			
			var p_id = parseInt($(this).data('p_id'));
			
			$.ajax({
			  url: baseURL + "ajax/uploadImage",
			  type: "POST",
			  data: data,
			  enctype: 'multipart/form-data',
			  processData: false,  // tell jQuery not to process the data
			  contentType: false   // tell jQuery not to set contentType
			}).done(function(data) {
				
				authImage[p_id] = data.toString();

			   if(data != 'error' ) {
					$(this).val(data);
			   } else {
					if ($.inArray(fileTypes,['image/jpeg','image/png', 'image/PNG', 'image/jpg','image/gif'])==-1) {
						showSweetAlert("Not a valid image, only JPEG , PNG, or GIF allowed","Whoops!", "error");
					}
			   }
			   $('#editExistingProductUpdateButton').prop('disabled', false);
			});
		}
	});

	$('#edit_stock_main_images').change(function(){
		var fileTypes= this.files[0].type;
		
		if($.inArray(fileTypes,['image/jpeg','image/png','image/PNG','image/jpg','image/gif'])==-1)
		{
			showSweetAlert("Not a valid image, only JPEG , PNG, or GIF allowed","Whoops!", "error");
		}
		else
		{
			var image= this.files[0];
			
			data = new FormData();
			data.append('file', this.files[0]);
			
			$.ajax({
			  url: baseURL + "ajax/uploadImage",
			  type: "POST",
			  data: data,
			  enctype: 'multipart/form-data',
			  processData: false,  // tell jQuery not to process the data
			  contentType: false   // tell jQuery not to set contentType
			}).done(function(data) {
				$(this).attr('set-hidden-value', data);
				$("#imagePreviewExisting").attr('src', data);
				
			   if(data != 'error' ) {
					$(this).val(data);
			   } else {
					if ($.inArray(fileTypes,['image/jpeg','image/png', 'image/PNG', 'image/jpg','image/gif'])==-1) {
						showSweetAlert("Not a valid image, only JPEG , PNG, or GIF allowed","Whoops!", "error");
					}
			   }
			});
		}
	});

	$('#newquantity').on('change', function () {
		if ($('#newquantity').val() > current) {
			
			for (var i = current; i < $(this).val(); i++) {
				$('#appendProduct').append('<div id="product' + i + '">' +
							'<hr>'  +
								'<div class="form-group">' +
									'<label>Serial Number<i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i></label>' +
									'<input class="form-control" id="serial'+ i +'" name="serial" type="text" value="">' +
								'</div>' +
								
								'<div class="col-sm-12 prize text-center mb-2 game-role-checkbox">' +
									'<label class="btn btn-fill btn-danger">Authenticity' +
										'<img class="imagePreview" id="imagePreview' + i + '" src="#">' +
										'<input type="file" id="auth'+ i +'" name="auth" class="commonImageUpload" show-preview-on="mainPrizeImagePreview" set-hidden-value="mainPrizeImageHidden">' +
									'</label>' +
								'</div>' +
							'</div>'
				);
			}

			
		} else {
			removeAllAddedProducts(current, $(this).val());
		}
		
		current = $('#newquantity').val();
	});

	$('#newproductquantity').on('change', function () {
		if ($('#newproductquantity').val() > current) {
			for (var i = current; i < $(this).val(); i++) {
				$('#appendNewProduct').append('<div id="product' + i + '">' +
							'<hr>'  +
								'<div class="form-group">' +
									'<label>Serial Number<i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i></label>' +
									'<input class="form-control" id="serial'+ i +'" name="serial" type="text" value="">' +
								'</div>' +
								
								'<div class="col-sm-12 prize text-center mb-2 game-role-checkbox">' +
									'<label class="btn btn-fill btn-danger">Authenticity' +
										'<img class="imagePreview" id="imagePreview' + i + '" src="#">' +
										'<input type="file" id="auth'+ i +'" name="auth" class="commonImageUpload" show-preview-on="mainPrizeImagePreview" set-hidden-value="mainPrizeImageHidden">' +
									'</label>' +
								'</div>' +
							'</div>'
				);
			}

			
		} else {
			removeAllAddedProducts(current, $(this).val());
		}
		
		current = $('#newproductquantity').val();
	});
	
} );

function showStockModal() {
	$('#AddToInventoryModalTitle').text('Add New Stock');
	
	$('#addStock').show();
	$('#addToInventoryNextButton').show();
	$('#addToInventoryBackButton').hide();
	$('#addProduct').hide();
	$('#addToInventoryCreateButton').hide();
}
					
function showProductModal() {
	$('#AddToInventoryModalTitle').text('Add Products');
	
	$('#addStock').hide();
	$('#addToInventoryNextButton').hide();
	$('#addToInventoryBackButton').show();
	$('#addProduct').show();
	$('#addToInventoryCreateButton').show();
}

function showAddToInventoryModal() {
	closeAllOtherModalsExcept("AddToInventory");

	$('#AddToInventoryModal').show();
}

function showExistingModal() {
	closeAllOtherModalsExcept("EditExistingStock");

	$('#EditExistingStock').show();
}

function showAddNewProduct(s) {
	closeAllOtherModalsExcept("AddNewProduct");
	
	$('#AddNewProduct').show();
	$('#AddNewProduct').data('s_id', s);
}

function showEditExistingProduct() {
	closeAllOtherModalsExcept("EditExistingProduct");

	$('#EditExistingProduct').show();
}

function closeAddToInventoryModal() {
	current = 0;

	document.getElementById("createStock").reset();
	document.getElementById("createProduct").reset();
	
	$(".imagePreview").attr('src', "#");

	$('#appendProduct').empty();
	$('textarea[name*="stockdescription"]').siblings().find('.note-editing-area').find('p').text("");

	$('#AddToInventoryModal').hide();

	showStockModal();

	closeNewTypeForm(true);
	closeEditTypeForm(true);
}

function closeExistingModal() {
	current = 0;

	document.getElementById("editStock").reset();
	
	$(".imagePreview").attr('src', "#");

	$('#EditExistingStock').hide();

	$('textarea[name*="edit_stockdescription"]').siblings().find('.note-editing-area').find('p').text("");
}		

function closeAddNewProduct() {
	document.getElementById("createNewProduct").reset();
	
	$(".imagePreview").attr('src', "#");
	
	removeAllAddedProducts(current, 0);
	$('#appendNewProduct').empty();

	$('#AddNewProduct').hide();	
}

function closeEditExistingProduct() {
	document.getElementById("editProduct").reset();
	
	$(".imagePreview").attr('src', "#");
	
	removeAllExistingProducts(current, 0);

	$('textarea[name*="edit_stockdescription"]').text("");
	
	$('#EditExistingProduct').hide();	
}

function closeAllOtherModalsExcept(e) {
	if (e == "AddToInventory") {
		closeExistingModal();
		closeAddNewProduct();
		closeEditExistingProduct();
	} else if (e == "EditExistingStock") {
		closeAddToInventoryModal();
		closeAddNewProduct();
		closeEditExistingProduct();
	} else if (e == "AddNewProduct") {
		closeAddToInventoryModal();
		closeExistingModal();
		closeEditExistingProduct();
	} else if (e == "EditExistingProduct") {
		closeAddToInventoryModal();
		closeExistingModal();
		closeAddNewProduct();
	}
}

function closeAllModals() {
	closeAddToInventoryModal();
	closeExistingModal();
	closeAddNewProduct();
	closeEditExistingProduct();
}

function removeAllAddedProducts(current, limit) {
	for (var i = current; i > limit; i--) {
		var toRemove = '#product'+(i-1);
		$(toRemove).remove();
	}
}

function removeAllExistingProducts(current, limit) {
	console.log(current + " " + limit);
	for (var i = current; i > limit; i--) {
		var toRemove = '#existingProduct'+(i-1);
		$(toRemove).remove();
	}
}

function closeNewTypeForm(b) {
	if (b === true) {
		if ($('#addType').css('display') != 'none') {
			$('#addType').hide();
			$('#existingType').show();
		}
	} else {
		if ($('#addType').css('display') === 'none') {
			$('#addType').show();
			$('#existingType').hide();
		}
	}
}

function addNewType() {
	var type = $('#newType').val();
	var val = parseInt($('#itemtype option:last').val()) + 1;
	
	if (type.trim() != "") {
		$('#itemtype').append($('<option>', {
			value: val,
			text: type
		}));
	}
	
	closeNewTypeForm(true);
}

function closeEditTypeForm(b) {
	if (b === true) {
		if ($('#edit_addType').css('display') != 'none') {
			$('#edit_addType').hide();
			$('#edit_existingType').show();
		}
	} else {
		if ($('#edit_addType').css('display') === 'none') {
			$('#edit_addType').show();
			$('#edit_existingType').hide();
		}
	}
}

function addEditNewType() {
	var type = $('#edit_newType').val();
	var val = parseInt($('#edit_itemtype option:last').val()) + 1;
	
	if (type.trim() != "") {
		$('#itemtype').append($('<option>', {
			value: val,
			text: type
		}));
	}
	
	closeEditTypeForm(true);
}

function prePopulateForm(data){
	$('#editStockTitle').attr('data-test', data.s_id);
	$('#editStockTitle').text('Edit Existing - Stock ' + data.name);

	$('#edit_stockname').val(data.name);
	$('textarea[name*="edit_stockdescription"]').text(data.description);
	
	$('#edit_stock_main_images').attr('set-hidden-value', data.image);
	$('#imagePreviewExisting').attr('src', data.image);

	if (data.item_type == 'product') {
		$('input[id=edit_prize_type_product]').prop('checked', true);
		$('input[id=edit_prize_type_service]').prop('checked', false);
	} else {
		$('input[id=edit_prize_type_product]').prop('checked', false);
		$('input[id=edit_prize_type_service]').prop('checked', true);
	}
	
	$("#edit_itemtype > option").each(function() {
		if (data.type == this.text) {
			$("#edit_itemtype").val(this.value);
			return false;
		}
	});

	if (data.quality == "bad") {
		var ty = $('#edit_quality').val(0);
	} else if (data.quality == "poor") {
		var ty = $('#edit_quality').val(1);
	} else if (data.quality == "fair") {
		var ty = $('#edit_quality').val(2);
	} else if (data.quality == "good") {
		var ty = $('#edit_quality').val(3);
	} else if (data.quality == "excellent") {
		var ty = $('#edit_quality').val(4);
	} else {
		var ty = $('#edit_quality').val(5);
	}

	$('#edit_value').val(data.value);
}

function prePopulateExistingProduct(data){
	var count = 0
	for (var i = 0; i < data.length; i++) {
		$('#appendExistingProduct').append('<div id="existingProduct' + i + '">' +
					'<hr>'  +
						'<div class="form-group" data-s_id="' + data[i].data.s_id +'" data-p_id="' + data[i].data.p_id +'" data-name="' + data[i].data.name +'">' +
							'<label>Serial Number<i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i></label>' +
							'<input class="form-control" id="serial'+ i +'" name="serial'+ i +'" type="text" value="' + data[i].data.serial_number + '">' +
						'</div>' +
						
						'<div class="col-sm-12 prize text-center mb-2 game-role-checkbox">' +
							'<label class="btn btn-fill btn-danger">Authenticity' +
								'<img class="imagePreview" id="imagePreviewExistingProduct' + i + '" src="#">' +
								'<input type="file" class="commonImageUpload" id="auth'+ i +'" data-p_id="' + data[i].data.p_id +'" data-image="'+ data[i].data.authenticity +'" name="auth'+ i +'" show-preview-on="mainPrizeImagePreview" set-hidden-value="mainPrizeImageHidden">' +
							'</label>' +
						'</div>' +
					'</div>'
		);
		
		count++;
	}
	
	current=count;
}


