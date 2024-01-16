$(document).ready(function () {
    updateProductList("", 0, 1000000);

    $("#applyFiltersBtn").click(function () {
        var selectedCategory = $("#categoryFilter").val();
        var minPrice = $("#minPrice").val() || 0;
        var maxPrice = $("#maxPrice").val() || 1000000;
        updateProductList(selectedCategory, minPrice, maxPrice);
    });

    $("#openCartModal").click(function () {
        updateCartModal();
        $("#cartModal").modal("show");
    });

    $(document).on("click", ".cartProductRemoveButton", function () {
        let productId = $(this).data("product-id");
        removeProductFromCart(productId);
        updateCartModal();
    })

    function removeProductFromCart(productId) {
        $.ajax({
            url: 'remove_cart_item.php',
            type: 'POST',
            data: { productId: productId },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                if (response.status === 'success') {
                    updateCartModal();
                } else {
                    console.error("Unexpected response:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.log("Response Text:", xhr.responseText);
            }
        })
    }

    function addToCart(productId) {
        $.ajax({
            url: 'add_to_cart.php',
            type: 'POST',
            data: { productId: productId },
            dataType: 'json',
            success: function (response) {
                console.log(response+"Product added to cart");
                updateCartModal();
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.log("Response Text:", xhr.responseText);
            }
        });
    }

    $("#productContainer").on("click", ".add-to-cart-btn", function () {
        var productId = $(this).data("product-id");
        addToCart(productId);
    });

    $("#rzp-button1").click(function (e) {
        $("#cartModal").modal("hide");

        var options = {
            key: 'rzp_test_7VF0CWufBdLEVg', 
            amount: 100 * $("#totalBill").data("total-bill"), 
            currency: 'INR',
            name: 'Flipkart',
            description: 'Test Payment',
            image: 'uploads/flipkart.png',
            handler: function (response) {
                response.totalBill = $("#totalBill").data("total-bill");
                var products = [];
                $("#cartContainerModal").children().each(function (index, item) {
                    if($(item).hasClass("cart-item")){
                        console.log("Item HTML:", $(item).html());
                        
                        var product_id = $(item).data("product-id");
                        var quantity = $(item).data("quantity");
                    
                        console.log("Product ID:", product_id);
                        console.log("Quantity:", quantity);
                    
                        if (product_id !== undefined && quantity !== undefined) {
                            var product = {
                                product_id: product_id,
                                quantity: quantity
                            };
                            products.push(product);
                        }
                    }
                });
                
                console.log("Final Products Array:", products);
                
                response.products = products;
                console.log("response = ",response);
                if (response.razorpay_payment_id) {
                    $.ajax({
                        url: 'razorpay_callback.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(response),
                        success: function (res) {
                            console.log(res);
                            emptyCart(response.products);
                            displayOrderPlacedAlert();
                            updateCartModal();
                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                            console.log("Response Text:", xhr.responseText);
                        }
                    });
                }
            },
            prefill: {
                name: 'John Doe',
                email: 'john@example.com',
                contact: '9876543210'
            }
        };

        var rzp = new Razorpay(options);
        rzp.open();
        e.preventDefault();
    });

    function emptyCart(products) {
        console.log("In emptyCart() products = ",products);
        $.ajax({
            url:"empty_cart.php",
            type : "POST",
            contentType: "application/json",
            data : JSON.stringify(products),
            success : function(response){
                console.log(response+" \ncart items removed from cart table");
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.log("Response Text:", xhr.responseText);
            }

        })        
    }

    function displayOrderPlacedAlert() {
        $("#orderPlacedMessage").removeClass("d-none");
        setTimeout(function () {
            $("#orderPlacedMessage").addClass("d-none");
        }, 3000); 
    }
    

    function updateProductList(selectedCategory, min, max) {
        $.ajax({
            url: 'filter_products.php',
            type: 'POST',
            data: { category: selectedCategory, minPrice: min, maxPrice: max },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                displayProducts(response);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.log("Response Text:", xhr.responseText);
            }
        });
    }

    function displayProducts(products) {
        var productContainer = $("#productContainer");
        productContainer.empty();

        $.each(products, function (index, product) {
            var productCard =
                '<div class="col-md-4 mb-4" style="height: 400px;">' +
                '<div class="card" style="height: 100%; display: flex; flex-direction: column;">' +
                '<img src="' + product['product_image'] + '" class="card-img-top" alt="Product Image" style="flex: 1; object-fit: cover;">' +
                '<div class="card-body">' +
                '<h5 class="card-title" style="height: 50px; overflow: hidden;">' + product['product_name'] + '</h5>' +
                '<p class="card-text">Price: Rs ' + product['price'] + '</p>' +
                '<button class="btn btn-primary add-to-cart-btn" data-product-id="' + product['product_id'] + '">Add to Cart</button>' +
                '</div></div></div>';
            productContainer.append(productCard);
        });
    }

    function updateCartModal() {
        $.ajax({
            url: 'get_cart_items.php',
            type: 'GET',
            dataType: 'json',
            success: function (cartItems) {
                console.log(cartItems);
                displayCartItems(cartItems);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.log("Response Text:", xhr.responseText);
            }
        });
    }

    function displayCartItems(cartItems) {
        var cartContainerModal = $("#cartContainerModal");
        cartContainerModal.empty();
        var totalBill = 0;

        $.each(cartItems, function (index, cartItem) {
            cartItem['price'] = Number(cartItem['price']);

            if (typeof cartItem['price'] === 'number' && !isNaN(cartItem['price'])) {
                var productTotal = cartItem['quantity'] * cartItem['price'];
                totalBill += productTotal;

                var cartItemHtml =
                    '<div class="cart-item" data-product-id="'+cartItem['product_id']+'" data-quantity="'+cartItem['quantity']+'">' +
                    '<p>' + cartItem['product_name'] + ' - Quantity: ' + cartItem['quantity'] +
                    ' - Price: Rs ' + cartItem['price'].toFixed(2) +
                    ' - Total: Rs ' + productTotal.toFixed(2) +
                    '<a href="#" class="cartProductRemoveButton" data-product-id="' + cartItem['product_id'] + '"> Remove</a></p>' +
                    '</div>';

                cartContainerModal.append(cartItemHtml);
            } else {
                console.error("Invalid price for product:", cartItem['product_name']);
            }
        });

        var totalBillHtml = '<p data-total-bill=' + totalBill.toFixed(2) + ' id="totalBill">Total Bill: Rs ' + totalBill.toFixed(2) + '</p>';
        cartContainerModal.append(totalBillHtml);
    }

});
