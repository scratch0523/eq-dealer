function searchProductFullScreen() {
    var searchData = {
        'search_product_request': 'search_product'
    };

    var search_category = $("#search_category_full").val();
    var search_product = $("#search_product_full").val();

    if (search_category !== "") {
        searchData.search_category = search_category;
    }

    if (search_product !== "") {
        searchData.search_product = search_product;
    }

    console.log(searchData);

    $.ajax({
        type: 'GET',
        url: 'dealer-admin/admin_actions/admin_action.php',
        data: searchData,
        success: function (response) {
            // console.log('Server response:', response);

            var products = JSON.parse(response);

            if (products.length === 0) {
                $("#searchResultsDropdownFullScreen").html('<p>No products found.</p>');
            } else {
                var html = '<ul>';
                var limit = Math.min(products.length, 10);
                for (var i = 0; i < limit; i++) {
                    html += '<li><a href="product-left-sidebar.html?product_id=' + products[i].product_id + '">' + products[i].product_name + '</a></li>';
                }
                html += '</ul>';

                $("#searchResultsDropdownFullScreen").html(html);
            }

            $("#searchResultsDropdownFullScreen").slideDown();

            $("#searchButton i").removeClass("fi-rr-search").addClass("fa-solid fa-times").css("color", "red");

            $("#searchButton").removeAttr("onclick");

            $("#searchButton").attr("id", "removeSearch");

            $("#removeSearch").attr("onclick", "resetSearch()");
        },
        error: function (error) {
            var error_response = JSON.parse(error.responseText);
            console.log(error_response);
        }
    });
}


function searchProductSmallScreen() {
    var searchData = {
        'search_product_request': 'search_product'
    };

    var search_category = $("#search_category_small").val();
    var search_product = $("#search_product_small").val();

    if (search_category !== "") {
        searchData.search_category = search_category;
    }

    if (search_product !== "") {
        searchData.search_product = search_product;
    }

    console.log(searchData);

    $.ajax({
        type: 'GET',
        url: 'dealer-admin/admin_actions/admin_action.php',
        data: searchData,
        success: function (response) {

            // console.log('Server response:', response);

            var products = JSON.parse(response);

            if (products.length === 0) {
                $("#searchResultsDropdownSmallScreen").html('<p>No products found.</p>');
            } else {
                var html = '<ul>';
                var limit = Math.min(products.length, 10);
                for (var i = 0; i < limit; i++) {
                    html += '<li><a href="product-left-sidebar.html?product_id=' + products[i].product_id + '">' + products[i].product_name + '</a></li>';
                }
                html += '</ul>';

                $("#searchResultsDropdownSmallScreen").html(html);
            }

            $("#searchResultsDropdownSmallScreen").slideDown();

            $("#searchButtonSmall i").removeClass("fi-rr-search").addClass("fa-solid fa-times").css("color", "red");

            $("#searchButtonSmall").removeAttr("onclick");

            $("#searchButtonSmall").attr("id", "removeSearchSmall");

            $("#removeSearchSmall").attr("onclick", "resetSearchSmall()");
        },
        error: function (xhr, status, error) {
            console.error('Ajax request failed:', status, error);
            console.error('Server response:', xhr.responseText);
        }
    });

}

function resetSearch() {
    $("#full_screen_search_form")[0].reset();

    $("#searchResultsDropdownFullScreen").slideUp();

    $("#removeSearch i").removeClass("fa-solid fa-times").addClass("fi-rr-search").css("color", "");

    $("#removeSearch").removeAttr("onclick");

    $("#removeSearch").attr("id", "searchButton");
    $("#searchButton").attr("onclick", "searchProductFullScreen()");
}

function resetSearchSmall() {
    $("#small_search_form")[0].reset();

    $("#searchResultsDropdownSmallScreen").slideUp();

    $("#removeSearchSmall i").removeClass("fa-solid fa-times").addClass("fi-rr-search").css("color", "");

    $("#removeSearchSmall").removeAttr("onclick");

    $("#removeSearchSmall").attr("id", "searchButtonSmall");
    $("#searchButtonSmall").attr("onclick", "searchProductSmallScreen()");
}