document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.querySelector('[data-sidebar-toggle]');

    if (toggle) {
        toggle.addEventListener('click', function () {
            document.body.classList.toggle('sidebar-open');
        });
    }

    document.addEventListener('click', function (event) {
        if (!document.body.classList.contains('sidebar-open')) {
            return;
        }

        var sidebar = document.querySelector('.sidebar');
        var clickedToggle = event.target.closest('[data-sidebar-toggle]');

        if (sidebar && !sidebar.contains(event.target) && !clickedToggle) {
            document.body.classList.remove('sidebar-open');
        }
    });
});

