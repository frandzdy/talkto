import {Controller} from "@hotwired/stimulus";

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

                if (star.className === starClassInactive) {
                    for (i; i >= 0; --i) {
                        stars[i].className = starClassActive;
                        $('#review_note').val(i)
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