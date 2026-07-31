(function() {
    // Detect base path dynamically (e.g. /sms/public or /)
    const pathParts = window.location.pathname.split('/');
    const publicIndex = pathParts.indexOf('public');
    const basePath = publicIndex !== -1 ? pathParts.slice(0, publicIndex + 1).join('/') : '';
    const apiUrl = window.location.origin + basePath + '/api/school-settings';

    fetch(apiUrl)
        .then(response => response.json())
        .then(settings => {
            if (!settings) return;

            // 1. Document title and text logo updates
            document.title = document.title.replace(/BabyCare|Kiddos|Education Meeting|Edu Meeting/gi, settings.school_name);
            
            const logoTextSelectors = ['.navbar-brand', '.logo', '.nav-brand h1', '.footer-brand h2', '.navbar-brand h1'];
            logoTextSelectors.forEach(selector => {
                const el = document.querySelector(selector);
                if (el) {
                    el.innerHTML = settings.school_name;
                }
            });

            // 2. Scan and replace placeholders dynamically in all text nodes
            const placeholders = {
                emails: ['Email@Example.com', 'youremail@email.com', 'info@yourdomain.com', 'info@meeting.edu', 'info@yourdomain.com'],
                addresses: ['123 Street, New York', '198 West 21th Street, Suite 721 New York NY 10016', 'Rio de Janeiro - RJ, 22795-008, Brazil', '203 Fake St. Mountain View, San Francisco, California, USA'],
                phones: ['+ 0123 456 7890', '+ 1235 2355 98', '010-020-0340', '+2 392 3929 210', '010-020-0340']
            };

            function walk(node) {
                if (node.nodeType === Node.TEXT_NODE) {
                    let val = node.nodeValue;
                    placeholders.emails.forEach(email => {
                        if (val.toLowerCase().includes(email.toLowerCase())) {
                            val = val.replace(new RegExp(email, 'gi'), settings.school_email);
                        }
                    });
                    placeholders.addresses.forEach(addr => {
                        if (val.toLowerCase().includes(addr.toLowerCase())) {
                            val = val.replace(new RegExp(addr, 'gi'), settings.school_address);
                        }
                    });
                    placeholders.phones.forEach(phone => {
                        if (val.toLowerCase().includes(phone.toLowerCase())) {
                            val = val.replace(new RegExp(phone, 'gi'), settings.school_mobile_one);
                        }
                    });

                    // Replace template names with backend school name
                    if (val.toLowerCase().includes('babycare')) {
                        val = val.replace(/BabyCare/gi, settings.school_name);
                    }
                    if (val.toLowerCase().includes('kiddos')) {
                        val = val.replace(/Kiddos/gi, settings.school_name);
                    }
                    if (val.toLowerCase().includes('edu meeting')) {
                        val = val.replace(/Edu Meeting/gi, settings.school_name);
                    }

                    if (val !== node.nodeValue) {
                        node.nodeValue = val;
                    }
                } else if (node.nodeType === Node.ELEMENT_NODE && node.nodeName !== 'SCRIPT' && node.nodeName !== 'STYLE') {
                    if (node.nodeName === 'A') {
                        let href = node.getAttribute('href') || '';
                        if (href.startsWith('mailto:')) {
                            node.setAttribute('href', 'mailto:' + settings.school_email);
                        }
                        if (href.startsWith('tel:')) {
                            node.setAttribute('href', 'tel:' + settings.school_mobile_one);
                        }
                    }
                    for (let child of node.childNodes) {
                        walk(child);
                    }
                }
            }
            walk(document.body);

            // 3. Inject Brand CSS Override for Navy Blue (#0A1F44) and Sky Blue (#0284C7)
            const style = document.createElement('style');
            style.innerHTML = `
                /* CSS Branding Overrides */
                :root {
                    --bs-primary: #0A1F44 !important;
                    --bs-secondary: #0284C7 !important;
                    --primary: #0A1F44 !important;
                    --secondary: #0284C7 !important;
                    --tertiary: #38BDF8 !important;
                    --bg-primary: #0A1F44 !important;
                    --bg-secondary: #0284C7 !important;
                    --bs-blue: #0284C7 !important;
                }
                .bg-primary, .bg-primary-grad, .btn-primary, .main-button-red a, .sub-header { 
                    background-color: #0A1F44 !important; 
                    background: #0A1F44 !important; 
                }
                .bg-secondary, .btn-secondary, .main-button-yellow a { 
                    background-color: #0284C7 !important; 
                    background: #0284C7 !important; 
                }
                .text-primary, .primary-color { color: #0A1F44 !important; }
                .text-secondary, .secondary-color { color: #0284C7 !important; }
                .btn-primary, .btn-secondary { border-color: transparent !important; }
                .border-primary { border-color: #0A1F44 !important; }
                .border-secondary { border-color: #0284C7 !important; }
                
                /* Specific selector fixes for templates */
                header.header-area, nav.main-nav {
                    border-bottom: 0 !important;
                }
                header.header-area {
                    box-shadow: 0 10px 28px rgba(10, 31, 68, 0.16) !important;
                }
                header.header-area .logo, nav.navbar .navbar-brand {
                    color: #0A1F44 !important;
                }
                .navbar-light .navbar-nav .nav-link.active, .navbar-light .navbar-nav .nav-link:hover {
                    color: #0284C7 !important;
                }
                footer, .footer, .ftco-footer {
                    background-color: #0A1F44 !important;
                    background: #0A1F44 !important;
                }
            `;
            document.head.appendChild(style);
        })
        .catch(err => console.error("Error syncing school settings:", err));
})();
