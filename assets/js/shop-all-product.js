$(document).ready(function () {
    $(".ec-btn-group.add-to-cart").click(function () {
        $("#addtocart_toast").addClass("show");
        setTimeout(function () {
            $("#addtocart_toast").removeClass("show")
        }, 3000);
    });

    $(".ec-btn-group.wishlist").click(function () {
        $("#wishlist_toast").addClass("show");
        setTimeout(function () {
            $("#wishlist_toast").removeClass("show")
        }, 3000);
    });

    // Load All products

    shopAllProducts();
    showAllFilters()
});

// Sort By Value

function sortBy() {
    var sort_value = $("#ec-select").val();
    alert(sort_value);
    shopAllProducts(1, sort_value);
}

// Shop All Products

function shopAllProducts(page = 1, sortValue, categoryName, sizeValue) {

    var sortValue = $("#ec-select").val();

    var checkedCategories = [];
    $("#category_checkbox input:checked").each(function () {
        checkedCategories.push($(this).next("a").text());
    });

    var categoryName = checkedCategories.join(', ');

    var sizeValues = [];
    $("#size_checkbox input:checked").each(function () {
        sizeValues.push($(this).next("a").text());
    });

    var clickedColorName = $("#product_color_name").val();

    $("#product_color_name").val('');
    var sizeValue = sizeValues.join(', ');


    var minPriceInput = $('#minPrice').val();
    var maxPriceInput = $('#maxPrice').val();

    // console.log("Sort Value :", sortValue);
    // console.log("Category Name :", categoryName);
    // console.log("Size Values :", sizeValue);
    // console.log("Color Values :", clickedColorName);
    // console.log("Color maxPriceInput :", maxPriceInput);
    // console.log("Color minPriceInput :", minPriceInput);

    // console.log(categoryName);
    $.ajax({
        url: 'dealer-admin/admin_actions/admin_action.php',
        type: 'GET',
        data: {
            shopAllproducts_request: 'shop_all_products',
            sort_by: sortValue,
            category_name: categoryName,
            size: sizeValue,
            color_name: clickedColorName,
            min_price: minPriceInput,
            max_price: maxPriceInput
        },
        success: function (response) {
            // console.log(response);
            var shopAllProducts = JSON.parse(response);
            var shopAllProductContainer = $('.shop-all-product');

            shopAllProductContainer.empty();

            for (var i = 0; i < shopAllProducts.length; i++) {
                var productObject = shopAllProducts[i].shop_all_product;

                var productHTML = `
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 mb-6 pro-gl-content">
                <div class="ec-product-inner">
                    <div class="ec-pro-image-outer">
                        <div class="ec-pro-image">
                            <a href="product-left-sidebar.html" class="image">
                                <img class="main-image" loading="lazy" src="dealer-admin/product_images/${productObject.product_image}" alt="${productObject.product_name}" data-product-id="${productObject.product_id}"/>
                                <img class="hover-image" loading="lazy" src="dealer-admin/product_images/${productObject.product_image}" alt="${productObject.product_name}" data-product-id="${productObject.product_id}"/>
                            </a>
                            <span class="percentage">20%</span>
                            <a href="#" class="quickview" data-link-action="quickview" title="Quick view"
                                data-bs-toggle="modal" data-bs-target="#ec_quickview_product_modal"><i class="fi-rr-eye"></i></a>
                            <div class="ec-pro-actions">
                                <a title="Add To Cart" class="ec-btn-group add-to-cart"><i
                                        class="fi-rr-shopping-basket"></i></a>
                                <a class="ec-btn-group" title="Wishlist"><i class="fi-rr-heart"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="ec-pro-content">
                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">${productObject.product_name}</a></h5>
                        <div class="ec-pro-list-desc">${productObject.product_description}</div>
                        <span class="ec-price">
                            <h6>Price &nbsp;:&nbsp;&nbsp;</h6>
                            <span class="new-price">€${productObject.product_price.toFixed(2)}</span>
                            <span class="old-price">€&nbsp;${productObject.product_price.toFixed(2)}</span>
                        </span>
                        <span class="ec-price">
                            <h6>MSRP :&nbsp;&nbsp;</h6>
                            <span class="new-price">€${productObject.msrp_price.toFixed(2)}</span>
                            <span class="old-price">&nbsp;€${productObject.msrp_price.toFixed(2)}</span>
                            </span>
                        <div class="ec-pro-option">
                            <div class="ec-pro-color">
                                <span class="ec-pro-opt-label">Color</span>
                                <ul class="ec-opt-swatch ec-change-img">
                                    ${productObject.color.map(colorObject => `
                                        <li${colorObject.color_product_id === 1 ? ' class="active"' : ''} onclick="handleColorClick(event, '${colorObject.color_value}', '${colorObject.color_name}', ${productObject.product_id}, ${colorObject.color_product_id}, '${colorObject.product_image}')" onmouseover="handleColorClick(event, '${colorObject.color_value}', '${colorObject.color_name}', ${productObject.product_id}, ${colorObject.color_product_id}, '${colorObject.product_image}')">
                                            <a href="#" class="ec-opt-clr-img"
                                                data-src="dealer-admin/color_images/${colorObject.color_value}"
                                                data-src-hover="dealer-admin/color_images/${colorObject.color_value}"
                                                data-tooltip="${colorObject.color_name}"
                                                data-product-id="${colorObject.color_product_id}"
                                                data-main-image="dealer-admin/product_images/${colorObject.product_image}"
                                                data-hover-image="dealer-admin/product_images/${colorObject.product_image}">
                                                <span style="${colorObject.color_type === 'code' ? 'background-color:' + colorObject.color_value : 'background-image: url(dealer-admin/color_images/' + colorObject.color_value + ')'}"></span>
                                            </a>
                                        </li>
                                    `).join('')}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

                shopAllProductContainer.append(productHTML);
                updatePagination(shopAllProducts.length, page);
            }
        },
        error: function (xhr, status, error) {
            var error_response = JSON.parse(xhr.responseText);

            if (error_response.error == 'No_products_found') {
                // console.log("ef");
                var shopAllProductContainer = $('.shop-all-product');
                shopAllProductContainer.empty();

            }

        }
    });
}

// Show Selected Colour's Image

function handleColorClick(event, colorValue, colorName, productId, colorProductId, productImage) {
    $('.main-image[data-product-id="' + productId + '"]').attr('src', 'dealer-admin/product_images/' +
        productImage);

    $('.hover-image[data-product-id="' + productId + '"]').attr('src', 'dealer-admin/product_images/' +
        productImage);
}

// Pagination

function updatePagination(totalItems, currentPage) {
    var itemsPerPage = 12;
    var totalPages = Math.ceil(totalItems / itemsPerPage);

    var paginationInner = $('#pagination-links');
    paginationInner.empty();

    for (var i = 1; i <= totalPages; i++) {
        var isActive = i === currentPage ? ' class="active"' : '';
        paginationInner.append('<li><a' + isActive + ' onclick="shopAllProducts(' + i + ')">' + i +
            '</a></li><li><a class="next" onclick="shopAllProducts(' + (currentPage + 1) +
            ')">Next <i class="ecicon eci-angle-right"></i></a></li>');
    }

    var startItem = (currentPage - 1) * itemsPerPage + 1;
    var endItem = Math.min(currentPage * itemsPerPage, totalItems);
    $('#product-count').text('Showing ' + startItem + '-' + 12 + ' of ' + totalItems + ' item(s)');
}

// Show ALL Categories

function showAllFilters() {
    $.ajax({
        url: 'dealer-admin/admin_actions/admin_action.php',
        type: 'GET',
        data: {
            loadCategory_request: 'loadCategory_request',
        },
        success: function (response) {
            var success_response = JSON.parse(response);
            // console.log(success_response);

            if (success_response) {
                var ulList = $('#category_checkbox');
                var ulListSize = $("#size_checkbox");
                var ulListColors = $('#product_colors');
                var maxItemsToShow = 5;

                // Max Price

                // if (success_response.max_price) {
                //     var maxPrice = parseInt(success_response.max_price);
                //     var lastDigit = maxPrice % 10;

                //     if (lastDigit !== 0) {
                //         maxPrice += (10 - lastDigit);
                //     }

                //     // $('#maxPrice').val(maxPrice);

                // }

                for (var i = 0; i < success_response.category.length; i++) {
                    var category = success_response.category[i];

                    var checkboxName = 'category_checkbox_' + i;
                    var liItem = $(
                        '<li class="' + (i >= maxItemsToShow ? 'hidden' : '') +
                        '"><div class="ec-sidebar-block-item"><input type="checkbox" class="category_checkbox" ' +
                        'onchange="handleCheckboxChange(this)" data-category-name="' +
                        category + '" name="' +
                        checkboxName + '" /> <a>' +
                        category +
                        '</a><span class="checked"></span></div></li>'
                    );
                    ulList.append(liItem);
                }

                // Show Sizes
                for (var j = 0; j < success_response.sizes.length; j++) {
                    var size = success_response.sizes[j];
                    var checkboxNameSize = 'size_checkbox_' + j;
                    var liItemSize = $(
                        '<li><div class="ec-sidebar-block-item"><input type="checkbox" class="size_checkbox" ' +
                        'onchange="handleSizeCheckboxChange(this)" data-size-name="' +
                        size + '" name="' +
                        checkboxNameSize + '" /> <a>' +
                        size +
                        '</a><span class="checked"></span></div></li>'
                    );
                    ulListSize.append(liItemSize);
                }

                // Show Colors

                for (var k = 0; k < success_response.colors.length; k++) {
                    var color = success_response.colors[k];
                    var colorName = color.color_name;
                    var colorType = color.color_type;
                    var colorValue = color.color_value;

                    var liItemColor = $(
                        '<li><div class="ec-sidebar-block-item"><span></span></div></li>');
                    var colorSpan = liItemColor.find('span');

                    if (colorType === 'code') {
                        colorSpan.css('background-color', colorValue);
                    } else if (colorType === 'image') {
                        colorSpan.css('background-image', 'url(dealer-admin/color_images/' +
                            colorValue + ')');
                    }

                    liItemColor.on('click', function () {
                        // alert("sdf");
                        var clickedColorName = $(this).data('color-name');
                        // console.log('Clicked color: ' + clickedColorName);
                        $("#product_color_name").val(clickedColorName);
                        shopAllProducts(1, colorName);

                    });

                    liItemColor.data('color-name', colorName);

                    ulListColors.append(liItemColor);
                }
            }

        },
        error: function (xhr, status, error) {
            var error_response = JSON.parse(xhr.responseText);
            console.log(error_response);
        }
    });
}

// Category Checkbox change event

function handleCheckboxChange(category_checkbox, sortValue) {
    var checkedCheckboxes = $('.category_checkbox:checked');

    var categoryNames = [];

    checkedCheckboxes.each(function () {
        var isChecked = $(this).prop('checked');
        var categoryName = $(this).data('category-name');

        if (isChecked) {
            categoryNames.push(categoryName);
        }
    });

    console.log("Selected category names: ", categoryNames);

    shopAllProducts(1, sortValue, categoryNames);
}


function handleSizeCheckboxChange(category_checkbox, sortValue) {
    var checkedCheckboxes = $('.size_checkbox:checked');
    var sizeValues = [];

    checkedCheckboxes.each(function () {
        var isChecked = $(this).prop('checked');
        var sizeValue = $(this).data('size-name');

        if (isChecked) {
            sizeValues.push(sizeValue);
        }
    });

    console.log("Selected sizes : ", sizeValues);

    shopAllProducts(sizeValues);
}

var slider = document.getElementById('ec-sliderPrice');
slider.noUiSlider.on('slide', function () {
    updatePriceFilter();
});

function updatePriceFilter() {
    var slider = document.getElementById('ec-sliderPrice');
    var minPriceInput = document.getElementById('minPrice');
    var maxPriceInput = document.getElementById('maxPrice');

    // Get the current values of the slider
    var values = slider.noUiSlider.get();
    var minPrice = values[0];
    var maxPrice = values[1];

    // Update the input fields
    minPriceInput.value = minPrice;
    maxPriceInput.value = maxPrice;

    // You can perform additional actions here based on the updated values
    console.log('Min Price:', minPrice);
    console.log('Max Price:', maxPrice);

    shopAllProducts(minPriceInput, maxPriceInput);

}