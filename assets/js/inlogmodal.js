document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');
    let loginModal = document.querySelector('.login-modal'); // Selecting modal by class

    // Check for the login and user buttons
    let loginBtns = document.querySelectorAll('.login-btn, .user-btn'); // Selecting all buttons with either class

    // Loop through each button and add click event
    loginBtns.forEach(function(btn) {
        btn.addEventListener('click', function(event) {
            event.preventDefault();
            loginModal.style.display = 'block'; // Open the modal
            console.log('loginbtn loaded');
        });
    });

    // Close the modal when the close button is clicked
    let spanClose = loginModal.querySelector('.close-btn');
    if (spanClose) {
        spanClose.addEventListener('click', function() {
            loginModal.style.display = 'none'; // Close the modal
            console.log('closebtn loaded');
        });
    }

    // Close the modal when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target === loginModal) {
            loginModal.style.display = 'none';
        }
    });
});
