jQuery(document).ready(function(){
	// LOAD MORE on the blog
	jQuery('#true_loadmore').click(function(e){
		e.preventDefault();
		jQuery(this).text('Загружаю...'); // изменяем текст кнопки
		var data = {
			'action': 'loadmore',
			'query': true_posts,
			'page' : current_page
		};
		jQuery.ajax({
			url:ajaxurl, // обработчик
			data:data, // данные
			type:'POST', // тип запроса
			success:function(data){
				if( data ) { 
					jQuery('#true_loadmore').text('-> Загрузить еще статьи <-');
					jQuery('#main > article:last').after(data) // вставляем новые посты
					current_page++; // увеличиваем номер страницы на единицу
					if (current_page == max_pages) jQuery("#true_loadmore").remove(); // если последняя страница, удаляем кнопку
				} else {
					jQuery('#true_loadmore').remove(); // если мы дошли до последней страницы постов, скроем кнопку
				}
			}
		});
	});
});