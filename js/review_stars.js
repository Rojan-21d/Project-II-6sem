const stars = document.querySelectorAll("#star-rating .star");
const ratingInput = document.getElementById("rating");

stars.forEach((star, index) => {
    star.addEventListener("click", function() {
        ratingInput.value = this.getAttribute("data-value");

        // Highlight the selected star and previous ones
        stars.forEach((s, i) => {
            if (i <= index) {
                s.classList.add("selected");
            } else {
                s.classList.remove("selected");
            }
        });
    });
});