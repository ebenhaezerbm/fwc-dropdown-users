(function ($) {
	
	initSelect2 = function(el) 
	{
		$(el).select2({
			ajax: {
				url: fwc_dropdown_users.ajaxurl,
				dataType: 'json',
				data: function (term, page) {
					return {
						action: 'fwc_get_users',
						q: term,
						page: page
					};
				},
				results: function (data, page) {
					var per_page = data.data.item_per_page,
						total_count = data.data.total_count;

					var items = data.data;

					console.log(items);

					delete items.item_per_page;
					delete items.total_count;

					var hashtable = {};
					var results = [];
					$.each(items, function(index, item){
						if ( "undefined" === typeof(hashtable[item.role]) ) {
							hashtable[item.role] = {
								text:item.role, 
								children:[]
							};

							results.push( hashtable[item.role] );
						}

						hashtable[item.role].children.push({
							id:item.id,
							text:item.name
						});

						var users = hashtable[item.role].children;
						users.sort(function(a, b){
							if(a.text < b.text) 
								return -1;
							
							if(a.text > b.text) 
								return 1;
							
							return 0;
						});
					});

					var more = (page * per_page) < total_count;

					return { results: results, more: more };
				},
				quietMillis: 1000, // wait 1000 milliseconds before triggering the request
				delay: 1000, // wait 1000 milliseconds before triggering the request
				cache: true
			},
			escapeMarkup: function (text) { 
				return text; 
			},
			initSelection: function (element, callback) {
				var id = $(element).val(),
					name = $(element).data('text');

				var data = { id: id, text: name };

				callback(data);
			},
			// minimumInputLength: 3,
			// allowClear: true,
			formatNoMatches: 'User not found.',
			placeholder: "Select User"
		});
	}

	$(document).ready(function(){
		initSelect2("#dropdown-users");
	});

	$(document).delegate('a.editinline', 'click', function(){
		var el = $(this),
			tr = el.closest('tr'),
			post_id = parseInt( tr.attr('id').replace('post-','') ),
			author_id = parseInt( el.closest('.row-actions').siblings('.hidden').find('div.post_author').text() );

		var selectTag = tr.siblings('tr.quick-edit-row').find('.dropdown-users');

		selectTag.attr('name','post_author');

		selectTag.attr('id').replace('dropdown-users', 'author-post-'+post_id);

		var dataForm = {
			post_id: post_id
		};

		var dataPost = {
			'action': '_fwc_get_selected_user',
			'dataForm': dataForm
		};

		$.ajax({
			url: fwc_dropdown_users.ajaxurl,
			data: dataPost,
			dataType: 'json',
			type: 'POST',
			success: function(response){
				// console.log(response);

				selectTag.val(response.post_author);
				selectTag.data('text', response.name);

				initSelect2(selectTag);
			}
		});
	});

}(jQuery));