$(document).ready(function() {
    const sidebar = $(".sidebar");
    const toggleSidebarBtn = $(".toggle-sidebar-btn");
    const topbar = $(".topbar");
    const contentContainer = $(".content-container");

    toggleSidebarBtn.on('click', function() {
        sidebar.toggleClass('hidden');
        contentContainer.toggleClass('expanded');
    });

    // Toggle submenus
    $('.toggle-menu').on('click', function(e) {
        e.preventDefault();
        const submenu = $(this).next('.submenu');
        submenu.toggleClass('active');
        $(this).find('.arrow').toggleClass('bi-chevron-down bi-chevron-up');
    });

    // Mostrar/Ocultar el menú desplegable del usuario
    $('#userMenu').on('click', function() {
        $(this).next('.dropdown-menu').toggle();
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('#userMenu').length) {
            $('.dropdown-menu').hide();
        }
    });

    // Marcar el elemento activo basado en la URL
    const currentUrl = window.location.pathname;
    $('.sidebar-menu a').each(function() {
        if (this.href.includes(currentUrl)) {
            $(this).addClass('active');
            $(this).closest('.submenu').addClass('active');
            $(this).closest('.menu-item').find('.arrow').toggleClass('bi-chevron-down bi-chevron-up');
        }
    });
});