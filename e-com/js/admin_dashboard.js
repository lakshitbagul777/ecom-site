$(document).ready(function () {
    console.log("admin _dashboard.js is running");
    $("#productManagement").hide();
    getOrders();

    $("#products").click(function () {
        $("#orderManagement").hide();
        $("#productManagement").show();
    });

    $("#orders").click(function () {
        $("#productManagement").hide();
        getOrders();
    });

    function getOrders(){
        $.ajax({
            url : "get_orders.php",
            type : "POST",
            data : {},
            contentType: 'application/json',
            success : function(orders){
                displayOrders(JSON.parse(orders));
                $("#orderManagement").show();
            },
            error : function(e){
                console.log(e);
            }
        })
    }

    function displayOrders(orders) {
        $("#ordersTable").empty();
        $.each(orders, function (index, order) {
            let products = JSON.parse(order.products);
            let rowspan = products.length;
            console.log(products);
            let orderHtml = `<tr><td scope="row" rowspan="${rowspan}">${order['order_id']}</td><td scope="row" rowspan="${rowspan}">${order['order_date']}</td><td scope="row" rowspan="${rowspan}">${order['user_id']}</td><td scope="row" rowspan="${rowspan}">${order['amount']}</td><td scope="row" rowspan="${rowspan}">${order['payment_status']}</td>`;
    
            if (products.length > 0) {
                orderHtml += `<td>Product ID:${products[0].product_id}, Qty: ${products[0].quantity}</td></tr>`;
    
                for (let i = 1; i < products.length; i++) {
                    orderHtml += `<tr><td>Product ID:${products[i].product_id}, Qty: ${products[i].quantity}</td></tr>`;
                }
            } else {
                orderHtml += '</tr>';
            }
            $("#ordersTable").append(orderHtml);
        });
    }
    

    $(".remove-product-form").submit(function (event) {
        event.preventDefault();

        var formData = new FormData($(this)[0]);
        console.log("Remove product form submitted");
        $.ajax({
            url: 'remove_product.php',
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                try {
                    var jsonResponse = JSON.parse(response);
                    alert(jsonResponse.message); 
                    location.reload();
                } catch (e) {
                    alert(response);
                }
            },
            error: function (response) {
                alert("Error: " + response.statusText);
            }
        });
    });

    $("#productForm").submit(function (event) {
        event.preventDefault();

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: 'add_product.php',
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                try {
                    var jsonResponse = JSON.parse(response);
                    alert(jsonResponse.message); 
                    location.reload();
                } catch (e) {
                    alert(response);
                }
            },
            error: function (response) {
                alert("Error: " + response.statusText);
            }
        });
    });
});
