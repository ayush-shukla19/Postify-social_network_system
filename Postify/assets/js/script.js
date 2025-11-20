$(document).ready(function(){
    $("#signupForm").on("submit", function(e){
        let age = $("input[name=age]").val();
        let password = $("input[name=password]").val();

        if (age < 13) {
            alert("❌ Age must be 13 or above.");
            e.preventDefault();
        }
        if (password.length < 6) {
            alert("❌ Password must be at least 6 characters.");
            e.preventDefault();
        }
    });
});
$(document).ready(function(){
    // Signup validation already here...

    // Delete Post
    $(".delete-post").on("click", function(){
        let postCard = $(this).closest(".post-card");
        let postId = postCard.data("id");

        $.post("ajax_post_action.php", {action: "delete", post_id: postId}, function(response){
            if (response === "deleted") {
                postCard.remove();
            } else {
                alert("❌ Error deleting post");
            }
        });
    });

    // Like
    $(".like-btn").on("click", function(){
        let postCard = $(this).closest(".post-card");
        let postId = postCard.data("id");

        $.post("ajax_post_action.php", {action: "like", post_id: postId}, function(response){
            let data = JSON.parse(response);
            postCard.find(".like-count").text(data.likes);
            postCard.find(".dislike-count").text(data.dislikes);
        });
    });

    // Dislike
    $(".dislike-btn").on("click", function(){
        let postCard = $(this).closest(".post-card");
        let postId = postCard.data("id");

        $.post("ajax_post_action.php", {action: "dislike", post_id: postId}, function(response){
            let data = JSON.parse(response);
            postCard.find(".like-count").text(data.likes);
            postCard.find(".dislike-count").text(data.dislikes);
        });
    });
});

