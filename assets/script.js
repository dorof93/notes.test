document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function (event) {
        if ( event.target.classList.contains('link_confirm') ) {
            let approv = confirm(event.target.dataset.confirm);
            if ( ! approv ) {
                event.preventDefault();
            }
        }
    });
});