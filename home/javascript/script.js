$(document).ready(function() {
    $("#search-input").on("keyup", function () {
        const query = $(this).val().trim();

        if (query.length > 0) {
            $.ajax({
                url: "search.php",
                type: "GET",
                data: { query: query },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success" && response.data.length > 0) {
                        let results = response.data.map(film => {
                            const backgroundImage = `data:image/png;base64,${film.background}`;
                            return `
                                <div class="search-result-item" data-id="${film.id}">
                                    <span>${film.judul_film}</span>
                                </div>
                            `;
                        }).join("");
                        $("#search-results").html(results).fadeIn();
                    } else {
                        $("#search-results").html('<div class="no-results">No films found</div>').fadeIn();
                    }
                },
                error: function () {
                    console.error("An error occurred while fetching the films");
                }
            });
        } else {
            $("#search-results").fadeOut();
        }
    });

    $(document).on('click', '.search-result-item', function () {
        const filmId = $(this).data('id');
        window.location.href = `detail.php?id=${filmId}`;
    });

    $(document).click(function (e) {
        if (!$(e.target).closest('.search-container, .search-wrapper').length) {
            $('#search-results').hide();
        }
    });

    $(".add-to-cart-btn").click(function() {
        var filmId = $(this).data("id");

        $.ajax({
            url: "add_to_cart.php",
            type: "POST",
            data: { film_id: filmId },
            success: function(response) {
                alert(response);
            },
            error: function() {
                alert("An error occurred while adding the film to the cart.");
            }
        });
    });
});
