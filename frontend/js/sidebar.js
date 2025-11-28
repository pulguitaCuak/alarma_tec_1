// sidebar.js: loads sidebar include and handles visibility for admin link
async function loadSidebarInclude() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    try {
        const res = await fetch('/alarma_tec_1/frontend/includes/sidebar.html');
        if (!res.ok) throw new Error('No se pudo cargar el sidebar');
        const html = await res.text();
        sidebar.innerHTML = html;

        // call visibility handler if present or execute local one
        if (typeof checkAdminLinkVisibility === 'function') {
            checkAdminLinkVisibility();
        } else {
            // Inline check: if we have the get_session_user endpoint available
            try {
                const res2 = await fetch('/alarma_tec_1/back-end/get_session_user.php');
                const data = await res2.json();
                const link = document.getElementById('linkAdminUsuarios');
                if (link) {
                    if (data && (data.cargo === 1 || data.cargo === 2 || data.cargo === '1' || data.cargo === '2')) {
                        link.style.display = '';
                    } else {
                        link.style.display = 'none';
                    }
                }
            } catch (err) {
                const link = document.getElementById('linkAdminUsuarios');
                if (link) link.style.display = 'none';
                console.error('Error al comprobar sesión para admin link', err);
            }
        }

        // Dispatch event for other scripts that need to run after sidebar injection
        window.dispatchEvent(new Event('sidebarLoaded'));
    } catch (e) {
        console.error('Error al cargar el include del sidebar', e);
    }
}

// Load the include when DOM content is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadSidebarInclude);
} else {
    loadSidebarInclude();
}
