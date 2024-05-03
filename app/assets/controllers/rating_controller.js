import {Controller} from "@hotwired/stimulus";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    ratingStars = [...document.getElementsByClassName("rating__star")];

    connect() {
        this.executeRating(this.ratingStars)
    }

    /**
     * Gestion des étoiles
     */
    executeRating(stars) {
        const starClassActive = "rating__star fas fa-star";
        const starClassInactive = "rating__star fal fa-star";
        const starsLength = stars.length;
        let i;

        stars.map((star) => {
            star.onclick = () => {
                i = stars.indexOf(star);
                $('#product_review_note').val(i)
                if (star.className === starClassInactive) {
                    for (i; i >= 0; --i) {
                        stars[i].className = starClassActive;
                        $('#product_review_note').val(i)
                    }
                } else {
                    for (i; i < starsLength; ++i) {
                        stars[i].className = starClassInactive;
                    }
                }
            };
        });
    }
}