$(document).ready(function () {
    $(document).on('click', '.like', function (e) {
        let article_id = $(this).attr("data-id");
        let like_element = $(this);
        $.ajax({
            url: "/article/like/" + article_id,
            type: "post",
            dataType: 'json',
            data: null,
            beforeSend: function () {
            },
            success: function (data) {
                if (data.result === "like") {
                    like_element.addClass("text-danger");
                    like_element.removeClass("text-muted");
                } else if (data.result === "unlike") {
                    like_element.addClass("text-muted");
                    like_element.removeClass("text-danger");
                } else {
                    console.log(data);
                }
            }
        });
    });
});