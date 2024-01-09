 // Fetch Category Mega Menu

 function fetchCategory(main_category) {
     var main_category = main_category;
     console.log(main_category);

     $.ajax({
         url: 'dealer-admin/admin_actions/admin_action.php',
         type: 'GET',
         data: {
             fetch_all_category_request: 'fetch_all_category',
             main_category: main_category
         },
         success: function (response) {
             // console.log(response);
             $('.category_menu').empty();

             for (var category in response) {
                 if (response.hasOwnProperty(category)) {
                     var categoryUl = $('<ul class="d-block"></ul>');

                     var categoryLi = $('<li class="menu_title"></li>');
                     var url = 'category.html?main_category=' + main_category + '&category=' + category;
                     categoryLi.append('<a href="' + url + '">' + category + '</a>');
                     categoryUl.append(categoryLi);

                     for (var i = 0; i < response[category].length; i++) {
                         var subCategoryLi = $('<li></li>');
                         var subCategoryName = response[category][i];
                         subCategoryLi.append('<a href="category.html?main_category=' + main_category + '&category=' + category + '&sub_category=' + subCategoryName + '">' + subCategoryName + '</a>');
                         categoryUl.append(subCategoryLi);
                     }

                     // Append the categoryUl, not a string
                     $('.category_menu').append(categoryUl);
                 }
             }
         },
         error: function (xhr, status, responseText) {
             console.log(xhr.responseText);
         }
     });
 }