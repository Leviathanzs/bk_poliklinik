// Get the pop-up form and buttons
var popupForm = document.getElementById("popupForm");
var openFormBtn = document.getElementById("openFormBtn");
var closeFormBtn = document.getElementById("closeFormBtn");

// Open the pop-up form when the open button is clicked
openFormBtn.onclick = function() {
    popupForm.classList.remove("hidden");
}

// Close the pop-up form when the close button is clicked
closeFormBtn.onclick = function() {
    popupForm.classList.add("hidden");
}

// Close the pop-up form when the user clicks outside of it
window.onclick = function(event) {
    if (event.target == popupForm) {
        popupForm.classList.add("hidden");
    }
}