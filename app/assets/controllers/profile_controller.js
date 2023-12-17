import { Controller } from '@hotwired/stimulus'

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ["fileInput"]

    connect() {
        console.log("Profile picture controller connected!")
    }

    changeImage(event) {
        const reader = new FileReader()
        reader.onload = e => document.getElementById("profilePictureImg").src = e.target.result
        reader.readAsDataURL(this.fileInputTarget.files[0])
    }
}